<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de lecciones
 */
class leccionesController extends Controller {
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
  
  function index()
  {
    if (!is_admin($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    $data = 
    [
      'title'     => 'Todas las Tareas Publicadas',
      'slug'      => 'lecciones',
      'lecciones' => leccionModel::all_paginated()
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($id)
  {
    if (!is_profesor($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    // Validar que exista la lección
    if (!$leccion = leccionModel::by_id($id)) {
      Flasher::new('No existe la tarea seleccionada.', 'danger');
      Redirect::back();
    }

    // Validar el id del profesor y del registro
    if ($leccion['id_profesor'] !== $this->id && !is_admin($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'      => sprintf('Tarea %s', $leccion['titulo']),
      'hide_title' => true,
      'slug'       => is_admin($this->rol) ? 'lecciones' : 'grupos',
      'id_profesor'=> $this->id,
      'l'          => $leccion
    ];
    
    View::render('ver', $data);
  }

  function agregar()
  {
    if (!is_profesor($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::to('dashboard');
    }

    $data =
    [
      'title'             => 'Agregar nueva tarea',
      'slug'              => 'grupos',
      'id_profesor'       => $this->id,
      'id_materia'        => isset($_GET["id_materia"]) ? $_GET["id_materia"] : null,
      'materias_profesor' => materiaModel::materias_profesor($this->id),
      'button'            => ['url' => 'materias/asignadas', 'text' => '<i class="fas fa-undo"></i> Mis materias'],
    ];

    View::render('agregar', $data);
  }

  function post_agregar()
  {
    try {
      if (!check_posted_data(['csrf','titulo','video','contenido','id_materia','id_profesor','fecha_max','fecha_inicial','status'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_notificaciones());
      }

      // Validar rol
      if (!is_profesor($this->rol)) {
        throw new Exception(get_notificaciones(1));
      }

      $titulo      = clean($_POST["titulo"]);
      $video       = clean($_POST["video"]);
      $contenido   = clean($_POST["contenido"], true);
      $id_profesor = clean($_POST["id_profesor"]);
      $id_materia  = clean($_POST["id_materia"]);
      $fecha_ini   = clean($_POST["fecha_inicial"]);
      $fecha_max   = clean($_POST["fecha_max"]);
      $status      = clean($_POST["status"]);

      // Validar que el profesor exista
      if (!$profesor = profesorModel::by_id($id_profesor)) {
        throw new Exception('El profesor de la tarea no existe en la base de datos.');
      }

      // Validar la materia
      if (!$materia = materiaModel::by_id($id_materia)) {
        throw new Exception('La materia no existe en la base de datos.');
      }

      $sql = 'SELECT mp.* FROM materias_profesores mp WHERE mp.id_materia = :id_materia AND mp.id_profesor = :id_profesor';
      if (!profesorModel::query($sql, ['id_materia' => $id_materia, 'id_profesor' => $id_profesor])) {
        throw new Exception(sprintf('El profesor no tiene asignada la materia <b>%s</b>.', $materia['nombre']));
      }

      // Validar el nombre del usuario
      if (strlen($titulo) < 5) {
        throw new Exception('Ingresa un título mayor a 5 caracteres.');
      }

      // Validar que el url del video
      if (!filter_var($video, FILTER_VALIDATE_URL) && !empty($video)) {
        throw new Exception('Ingresa una URL de video válida.');
      }

      // Lección a guardar
      $data   =
      [
        'id_materia'       => $id_materia,
        'id_profesor'      => $id_profesor,
        'titulo'           => $titulo,
        'video'            => $video,
        'contenido'        => $contenido,
        'status'           => $status,
        'fecha_inicial'    => $fecha_ini,
        'fecha_disponible' => $fecha_max,
        'creado'           => now()
      ];

      // Insertar a la base de datos
      if (!$id = leccionModel::add(leccionModel::$t1, $data)) {
        throw new Exception(get_notificaciones(2));
      }

      Flasher::new(sprintf('Nueva tarea titulada <b>%s</b> agregada con éxito para la materia <b>%s</b>.', add_ellipsis($titulo, 50), $materia['nombre']), 'success');
      Redirect::to(sprintf('grupos/materia/%s', $id_materia));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function editar($id)
  {
    if (!is_profesor($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::to('dashboard');
    }

    // Validar que exista la lección
    if (!$leccion = leccionModel::by_id($id)) {
      Flasher::new('No existe la tarea seleccionada.', 'danger');
      Redirect::back();
    }

    // Validar el id del profesor y del registro
    if ($leccion['id_profesor'] !== $this->id && !is_admin($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'      => sprintf('Tarea %s', $leccion['titulo']),
      'slug'       => 'grupos',
      'id_profesor'=> $this->id,
      'l'          => $leccion,
      'button'     => ['url' => sprintf('grupos/materia/%s', $leccion['id_materia']), 'text' => '<i class="fas fa-undo"></i> Tareas'],
    ];

    View::render('editar', $data);
  }

  function post_editar()
  {
    try {
      if (!check_posted_data(['csrf','id','titulo','video','contenido','fecha_max','fecha_inicial','status'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_notificaciones());
      }

      // Validar rol
      if (!is_profesor($this->rol)) {
        throw new Exception(get_notificaciones(1));
      }

      // Validar que exista la lección
      $id = clean($_POST["id"]);
      if (!$leccion = leccionModel::by_id($id)) {
        throw new Exception('No existe la tarea seleccionada.');
      }

      $id_profesor = $this->id;
      
      // Validar el id del profesor y del registro
      if ($leccion['id_profesor'] !== $id_profesor && !is_admin($this->rol)) {
        throw new Exception(get_notificaciones());
      }

      $titulo      = clean($_POST["titulo"]);
      $video       = clean($_POST["video"]);
      $contenido   = clean($_POST["contenido"], true);
      $fecha_ini   = clean($_POST["fecha_inicial"]);
      $fecha_max   = clean($_POST["fecha_max"]);
      $status      = clean($_POST["status"]);

      // Validar el nombre del usuario
      if (strlen($titulo) < 5) {
        throw new Exception('Ingresa un título mayor a 5 caracteres.');
      }

      // Validar que el url del video
      if (!filter_var($video, FILTER_VALIDATE_URL) && !empty($video)) {
        throw new Exception('Ingresa una URL de video válida.');
      }

      // Lección a guardar
      $data   =
      [
        'titulo'           => $titulo,
        'video'            => $video,
        'contenido'        => $contenido,
        'status'           => $status,
        'fecha_inicial'    => $fecha_ini,
        'fecha_disponible' => $fecha_max
      ];

      // Actualizar registro
      if (!leccionModel::update(leccionModel::$t1, ['id' => $id], $data)) {
        throw new Exception(get_notificaciones(3));
      }

      Flasher::new(sprintf('Tarea titulada <b>%s</b> actualizada con éxito.', add_ellipsis($titulo, 50)), 'success');
      Redirect::to(sprintf('grupos/materia/%s', $leccion['id_materia']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function borrar($id)
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        throw new Exception(get_notificaciones());
      }

      if (!is_profesor($this->rol)) {
        throw new Exception(get_notificaciones());
      }
  
      // Validar que exista la lección
      if (!$leccion = leccionModel::by_id($id)) {
        throw new Exception('No existe la tarea seleccionada.');
      }
  
      $id_profesor = $this->id;
      
      // Validar el id del profesor y del registro
      if ($leccion['id_profesor'] !== $id_profesor && !is_admin($this->rol)) {
        throw new Exception(get_notificaciones());
      }

      // Eliminar registro de la base de datos
      if (!leccionModel::remove(leccionModel::$t1, ['id' => $id], 1)) {
        throw new Exception(get_notificaciones(4));
      }

      Flasher::new(sprintf('Tarea titulada <b>%s</b> borrada con éxito.', add_ellipsis($leccion['titulo'])), 'success');
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