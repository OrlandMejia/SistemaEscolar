<?php
use \Verot\Upload\Upload;
/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de grupos
 */
class gruposController extends Controller {
  private $id  = null;
  private $rol = null;

  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }

    $this->id  = get_user('id');
    $this->rol = get_user_rol();
  }

  function ver($id)
  {
    if (!is_admin($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    if (!$grupo = grupoModel::by_id($id)) {
      Flasher::new('No existe el grupo en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'  => sprintf('Grupo %s', $grupo['nombre']),
      'slug'   => 'grupos',
      'button' => ['url' => 'grupos', 'text' => '<i class="fas fa-table"></i> Todos los grupos'],
      'g'      => $grupo
    ];

    View::render('ver', $data);
  }

  function agregar()
  {
    $data = [
    'title'  => 'Agregar nuevo Grado',
    'slug'   => 'grupos',
    'button' => ['url' => 'grupos', 'text' => '<i class="fas fa-table"></i> Todos los Grados'],
    ];
    View::render('agregar', $data);
  }

  function post_agregar()
  {
    {
      try {
        if (!check_posted_data(['csrf','nombre','descripcion'], $_POST) || !Csrf::validate($_POST['csrf'])) {
          throw new Exception(get_notificaciones());
        }
  
        // Validar rol
        if(!is_admin(get_user_rol())){
          throw new Exception(get_notificaciones(1), 1);
        }
  
        $nombre      = clean($_POST["nombre"]);
        $descripcion = clean($_POST["descripcion"]);
  
        // Validar la longitud del nombre
        if (strlen($nombre) < 5) {
          throw new Exception('El nombre del grupo es demasiado corto.');
        }
  
        // Validar que el nombre del grupo no exista en la base de datos
        $sql = 'SELECT * FROM grupos WHERE nombre = :nombre LIMIT 1';
        if (grupoModel::query($sql, ['nombre' => $nombre])) {
          throw new Exception(sprintf('Ya existe el grupo <b>%s</b> en la base de datos.', $nombre));
        }
  
        $data =
        [
          'numero'      => rand(111, 999),
          'nombre'      => $nombre,
          'descripcion' => $descripcion,
          'horario'     => null,
          'creado'      => now()
        ];
  
        // Insertar a la base de datos
        if (!$id = grupoModel::add(grupoModel::$t1, $data)) {
          throw new Exception(get_notificaciones(2));
        }
  
        Flasher::new(sprintf('Nuevo grupo <b>%s</b> agregado con éxito.', $nombre), 'success');
        Redirect::back();
  
      } catch (PDOException $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
      } catch (Exception $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
      }
    }
  }

  function post_editar()
  {
    {
      try {
        if (!check_posted_data(['csrf','id','nombre','descripcion'], $_POST) || !Csrf::validate($_POST['csrf'])) {
          throw new Exception(get_notificaciones());
        }
  
        if(!is_admin(get_user_rol())){
          throw new Exception(get_notificaciones(1), 1);
        }
  
        $id          = clean($_POST["id"]);
        $nombre      = clean($_POST["nombre"]);
        $descripcion = clean($_POST["descripcion"]);
        $horario     = $_FILES["horario"];
        //variable para actualizar la imagen pasada, para actualizarla o borrarla
        $n_horario   = false;
  
        if (!$grupo = grupoModel::by_id($id)) {
          throw new Exception('No existe el grupo en la base de datos.');
        }
        //almacenamos en una variable el horario anterior
        $db_horario = $grupo['horario'];
  
        // Validar la longitud del nombre
        if (strlen($nombre) < 5) {
          throw new Exception('El nombre del grupo es demasiado corto.');
        }
  
        // Validar que el nombre del grupo no exista en la base de datos
        $sql = 'SELECT * FROM grupos WHERE id != :id AND nombre = :nombre LIMIT 1';
        if (grupoModel::query($sql, ['id' => $id, 'nombre' => $nombre])) {
          throw new Exception(sprintf('Ya existe el grupo <b>%s</b> en la base de datos.', $nombre));
        }
  
        $data =
        [
          'nombre'      => $nombre,
          'descripcion' => $descripcion
          
        ];
        
        // Validar si se está subiendo una imagen
        //4 es un codigo de error ya seteado
        if ($horario['error'] !== 4) {
          $tmp  = $horario['tmp_name'];
          $name = $horario['name'];
          $ext  = pathinfo($name, PATHINFO_EXTENSION);
  
          // Validar extensión del archivo, que sea cualquiera de los aceptados
          if (!in_array($ext, ['jpg','png','jpeg','bmp'])) {
            throw new Exception('Selecciona un formato de imagen válido.');
          }
          
          //inicializamos una clase de upload para subir el archivo, indica si la imagen se sube con exito
          $foo = new upload($horario); 
          if (!$foo->uploaded) {
            throw new Exception('Hubo un problema al subir el archivo.');
          }
  
          // Nuevo nombre y nuevas medidas de la imagen // generamos un nuevo nombre con la función que viene directamente en la plantilla
          $filename                = generate_filename();
          $foo->file_new_name_body = $filename;
          $foo->image_resize       = true; //le cambiamos el tamaño
          $foo->image_x            = 800; //definimos el tamño de la imagen
          $foo->image_ratio_y      = true; //no se modifica
  
          //indicamos con process en donde se guardará la imagen en el servidor
          $foo->process(UPLOADS);
          if (!$foo->processed) {
            throw new Exception('Hubo un problema al guardar la imagen en el servidor.');
          }
  
          //se añade al array donde se impregna la información la nueva información de horario, con el nombre y la extensión
          $data['horario'] = sprintf('%s.%s', $filename, $ext);
          $n_horario       = true; //establecemos al sistema que deba borrar cualquier imagen anterior
        }
  
        // Insertar a la base de datos
        if (!grupoModel::update(grupoModel::$t1, ['id' => $id], $data)) {
          throw new Exception(get_notificaciones(3));
        }
  
        // Borrado del horario anterior en caso de actualización // borra de la base de datos cualquier imagen anterior
        if ($db_horario !== null && $n_horario === true && is_file(UPLOADS.$db_horario)) {
          unlink(UPLOADS.$horario);
        }
  
        Flasher::new(sprintf('Grupo <b>%s</b> actualizado con éxito.', $nombre), 'success');
        Redirect::back();
  
      } catch (PDOException $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
      } catch (Exception $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
      }
    }
  }

  function borrar($id)
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        throw new Exception(get_notificaciones());
      }

      // Validar rol
      if(!is_admin(get_user_rol())){
        throw new Exception(get_notificaciones(1), 1);
      }

      // Exista el grupo
      if (!$grupo = grupoModel::by_id($id)) {
        throw new Exception('No existe el Grado en la base de datos.');
      }

      // Borramos el registro y sus conexiones
      if (grupoModel::eliminar($grupo['id']) === false) {
        throw new Exception(get_notificaciones(4));
      }

      // Borrar la imagen del horario
      if (is_file(UPLOADS.$grupo['horario'])) {
        unlink(UPLOADS.$grupo['horario']);
      }

      Flasher::new(sprintf('Grupo <b>%s</b> borrado con éxito.', $grupo['nombre']), 'success');
      Redirect::to('grupos');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  // Para profesores
  function asignados()
  {
    if (is_admin($this->rol)) {
      Redirect::to('grupos');
    }

    if (!is_profesor($this->rol)) {
      Flasher::deny();
      Redirect::back();
    }

    $data =
    [
      'title'  => 'Grados Asignados',
      'slug'   => 'grupos',
      'grupos' => profesorModel::grupos_asignados($this->id)
    ];

    View::render('asignados', $data);
  }

  function detalles($id)
  {
    if (is_admin($this->rol)) {
      Redirect::to(sprintf('grupos/ver/%s', $id));
    }

    if (!is_profesor($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    if (!$grupo = grupoModel::by_id($id)) {
      Flasher::new('No existe el grado en la base de datos.', 'danger');
      Redirect::back();
    }

    $grupo['materias'] = grupoModel::materias_asignadas($id, $this->id);
    $grupo['alumnos']  = grupoModel::alumnos_asignados($id);

    if (!profesorModel::asignado_a_grupo($this->id, $id)) {
      Flasher::new('No eres profesor de este grado.', 'danger');
      Redirect::to('grupos/asignados');
    }

    $data =
    [
      'title'  => sprintf('Grado %s', $grupo['nombre']),
      'slug'   => 'grupos',
      'button' => ['url' => 'grupos/asignados', 'text' => '<i class="fas fa-table"></i> Todos mis grados'],
      'g'      => $grupo
    ];

    View::render('detalles', $data);
  }

  function materia($id)
  {
    if (is_admin($this->rol)) {
      Redirect::to(sprintf('materias/ver/%s', $id));
    }

    if (!is_profesor($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    if (!$materia = materiaModel::by_id($id)) {
      Flasher::new('No existe la materia en la base de datos.', 'danger');
      Redirect::to('materias');
    }

    $data = 
    [
      'title'     => sprintf('Lecciones disponibles para %s', $materia['nombre']),
      'slug'      => 'grupos',
      'button'    => ['url' => 'materias/asignadas', 'text' => '<i class="fas fa-undo"></i> Mis materias'],
      'materia'   => $materia,
      'lecciones' => leccionModel::by_materia_profesor($id, $this->id)
    ];

    View::render('materia', $data);
  }

}