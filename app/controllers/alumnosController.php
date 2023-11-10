<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de alumnos
 */
class alumnosController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
  
  }
  
  function index()
  {
    //validar el rol de la persona que quiera acceder al listado
    if(!is_admin(get_user_rol())){
      Flasher::new(get_notificaciones(0), 'danger');
      Redirect::back();
    }
    //array que contiene los datos del titulo la viable slug que activa el link y la url del boton para agregar
    $data = 
    [
      'title' => 'Todos los Alumnos',
      'slug' => 'alumnos',
      'button' => ['url' => 'alumnos/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Alumno'],
      'alumnos' => alumnoModel::all_paginated()
];

// Descomentar vista si requerida
View::render('index', $data);
  }

  function ver($id)
  {
  //validar el rol de la persona que quiera acceder al listado
  if(!is_admin(get_user_rol())){
    Flasher::new(get_notificaciones(0), 'danger');
    Redirect::back();
  }
    
    if (!$alumno = alumnoModel::by_id($id)) {
      Flasher::new('No existe el alumno en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'  => sprintf('Alumno #%s', $alumno['numero']),
      'slug'   => 'alumnos',
      'button' => ['url' => 'alumnos', 'text' => '<i class="fas fa-table"></i> Alumnos'],
      'grupos' => grupoModel::all(),
      'a'      => $alumno
    ];

    View::render('ver', $data);
  }
  
  function detalles($id)
  {
  //validar el rol de la persona que quiera acceder al listado
  if(!is_profesor(get_user_rol())){
    Flasher::new(get_notificaciones(0), 'danger');
    Redirect::back();
  }
    
    if (!$alumno = alumnoModel::by_id($id)) {
      Flasher::new('No existe el alumno en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'  => sprintf('Alumno #%s', $alumno['numero']),
      'slug'   => 'alumnos',
      'button' => ['url' => 'alumnos', 'text' => '<i class="fas fa-table"></i> Alumnos'],
      'grupos' => grupoModel::all(),
      'a'      => $alumno
    ];

    View::render('detalles', $data);
  }

  function agregar()
  {
    $data = 
    [
      'title' => 'Inscribir un Nuevo Estudiante',
      'slug' => 'alumnos',
      'button' => ['url' => 'alumnos', 'text' => '<i class="fas fa-table"></i> Ver Alumnos'],
      'grupos' => grupoModel::all()
    ];
    View::render('agregar',$data);
  }

  function post_agregar()
  {
    try {
      if (!check_posted_data(['csrf','carnet','nombres','apellidos','email','telefono','password','conf_password','id_grupo'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_notificaciones());
      }

  //validar el rol de la persona que quiera acceder al listado
  if(!is_admin(get_user_rol())){
    Flasher::new(get_notificaciones(0), 'danger');
    Redirect::back();
  }
    //VARIABLES PARA INGRESAR DATOS EN LA BASE DE DATOS
      $carnet         = clean($_POST['carnet']);
      $nombres       = clean($_POST["nombres"]);
      $apellidos     = clean($_POST["apellidos"]);
      $email         = clean($_POST["email"]);
      $telefono      = clean($_POST["telefono"]);
      $password      = clean($_POST["password"]);
      $conf_password = clean($_POST["conf_password"]);
      $id_grupo      = clean($_POST["id_grupo"]);

      // Validar que el correo sea válido
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa un correo electrónico válido.');
      }

      //validar longitud del carnet
      if(empty($carnet)){
        throw new Exception('Llena este campo con el carnet o clave del estudiante.');
      }

      // Validar el nombre del usuario
      if (strlen($nombres) < 5) {
        throw new Exception('Ingresa un nombre válido.');
      }

      // Validar el apellido del usuario
      if (strlen($apellidos) < 5) {
        throw new Exception('Ingresa un apellido válido.');
      }

        // Validar la contraseña
        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
          throw new Exception('La contraseña debe contener al menos 8 caracteres, incluyendo caracteres especiales y números.');
      }

      // Validar ambas contraseñas
      if ($password !== $conf_password) {
        throw new Exception('Las contraseñas no son iguales.');
      }

      // Exista el id_grupo
      if ($id_grupo === '' || !grupoModel::by_id($id_grupo)) {
        throw new Exception('Selecciona un grupo válido.');
      }

      $data   =
      [
        'numero'          => rand(111111, 999999),
        'identificacion'  => $carnet,
        'nombres'         => $nombres,
        'apellidos'       => $apellidos,
        'nombre_completo' => sprintf('%s %s', $nombres, $apellidos),
        'email'           => $email,
        'telefono'        => $telefono,
        'password'        => password_hash($password.AUTH_SALT, PASSWORD_BCRYPT),
        'hash'            => generate_token(),
        'rol'             => 'alumno',
        'status'          => 'pendiente',
        'creado'          => now()
      ];

      $data2 =
      [
        'id_alumno' => null,
        'id_grupo'  => $id_grupo
      ];

      // Insertar a la base de datos
      if (!$id = alumnoModel::add(alumnoModel::$t1, $data)) {
        throw new Exception(get_notificaciones(2));
      }

      $data2['id_alumno'] = $id;

      // Insertar a la base de datos
      if (!$id_ga = grupoModel::add(grupoModel::$t3, $data2)) {
        throw new Exception(get_notificaciones(2));
      }

      // Email de confirmación de correo
      mail_confirmar_cuenta($id);

      $alumno = alumnoModel::by_id($id);
      $grupo  = grupoModel::by_id($id_grupo);

      Flasher::new(sprintf('Alumno <b>%s</b> agregado con éxito e inscrito al Grado de <b>%s</b>.', $alumno['nombre_completo'], $grupo['nombre']), 'success');
      Redirect::back();

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
    View::render('editar');
  }


  function post_editar()
{
    try {
        if (!check_posted_data(['csrf', 'id','carnet', 'nombres', 'apellidos', 'email', 'telefono', 'password', 'conf_password', 'id_grupo'], $_POST) || !Csrf::validate($_POST['csrf'])) {
            throw new Exception(get_notificaciones());
        }

        // Validar el rol de la persona que quiera acceder al listado
        if (!is_admin(get_user_rol())) {
            Flasher::new(get_notificaciones(0), 'danger');
            Redirect::back();
        }

        // Validar existencia del alumno
        $id = clean($_POST["id"]);
        if (!$alumno = alumnoModel::by_id($id)) {
            throw new Exception('No existe el alumno en la base de datos.');
        }

        // VARIABLES QUE YA SE ENCUENTRAN EN LA BASE DE DATOS
        $db_email = $alumno['email'];
        $db_pw = $alumno['password'];
        $db_status = $alumno['status'];
        $db_id_g = $alumno['id_grupo'];

        // VARIABLES DE INFORMACIÓN DEL FORMULARIO
        $carnet = clean($_POST['carnet']);
        $nombres = clean($_POST["nombres"]);
        $apellidos = clean($_POST["apellidos"]);
        $email = clean($_POST["email"]);
        $telefono = clean($_POST["telefono"]);
        $password = clean($_POST["password"]);
        $conf_password = clean($_POST["conf_password"]);
        $id_grupo = clean($_POST["id_grupo"]);

        // VARIABLES QUE DETERMINAN EL CAMBIO DE INFORMACIÓN SENSIBLE
        // Indican que si la nueva es igual entonces es false y si es distinta lanza true
        $changed_email = $db_email === $email ? false : true;
        $changed_pw = false;
        $changed_g = $db_id_g === $id_grupo ? false : true;

        // Validar existencia del correo electrónico
        $sql = 'SELECT * FROM usuarios WHERE email = :email AND id != :id LIMIT 1';
        if (usuarioModel::query($sql, ['email' => $email, 'id' => $id])) {
            throw new Exception('El correo electrónico ya existe en la base de datos.');
        }

        // Validar que el correo sea válido solo pasa si hay un cambio en el correo electrónico
        if ($changed_email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Ingresa un correo electrónico válido.');
        }

        // Validar el nombre del usuario
        if (strlen($nombres) < 5) {
            throw new Exception('Ingresa un nombre válido.');
        }

        // Validar la contraseña
        if (!empty($password) && !password_compleja($password)) {
            throw new Exception('La contraseña debe contener al menos 8 caracteres, incluyendo caracteres especiales y números.');
        }

        // Validar ambas contraseñas
        if (!empty($password) && $password !== $conf_password) {
            throw new Exception('Las contraseñas no son iguales.');
        }

        // Exista el id_grupo
        if ($id_grupo === '' || !grupoModel::by_id($id_grupo)) {
            throw new Exception('Selecciona un Grado válido.');
        }

        $data = [
            'identificacion' => $carnet,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'nombre_completo' => sprintf('%s %s', $nombres, $apellidos),
            'email' => $email,
            'telefono' => $telefono,
            'status' => $changed_email ? 'pendiente' : $db_status
        ];

        // Actualización de contraseña
        if (!empty($password)) {
            $data['password'] = password_hash($password . AUTH_SALT, PASSWORD_BCRYPT);
            $changed_pw = true;
        }

        // Actualizar base de datos
        if (!alumnoModel::update(alumnoModel::$t1, ['id' => $id], $data)) {
            throw new Exception(get_notificaciones(2));
        }

        // Actualizar base de datos
        if ($changed_g) {
            if (!grupoModel::update(grupoModel::$t3, ['id_alumno' => $id], ['id_grupo' => $id_grupo])) {
                throw new Exception(get_notificaciones(2));
            }
        }

        $alumno = alumnoModel::by_id($id);
        $grupo = grupoModel::by_id($id_grupo);

        Flasher::new(sprintf('Alumno <b>%s</b> actualizado con éxito.', $alumno['nombre_completo']), 'success');

        if ($changed_email) {
            mail_confirmar_cuenta($id);
            Flasher::new('El correo electrónico del alumno ha sido actualizado, debe ser confirmado.');
        }

        if ($changed_pw) {
            Flasher::new('La contraseña del alumno ha sido actualizada.');
        }

        if ($changed_g) {
            Flasher::new(sprintf('El Grado del alumno ha sido actualizado a <b>%s</b> con éxito.', $grupo['nombre']));
        }

        Redirect::back();

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

  //validar el rol de la persona que quiera acceder al listado
  if(!is_admin(get_user_rol())){
    Flasher::new(get_notificaciones(0), 'danger');
    Redirect::back();
  }

      // Exista el alumno
      if (!$alumno = alumnoModel::by_id($id)) {
        throw new Exception('No existe el alumno en la base de datos.');
      }

      // Borramos el registro y sus conexiones
      if (alumnoModel::eliminar($alumno['id']) === false) {
        throw new Exception(get_notificaciones(4));
      }

      Flasher::new(sprintf('Alumno <b>%s</b> borrado con éxito.', $alumno['nombre_completo']), 'success');
      Redirect::to('alumnos');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function exportar_pdf()
  {
      $data = [
          'title' => 'Exportar PDF'
      ];
  
      // Cargar la biblioteca de generación de PDF (por ejemplo, TCPDF)
      require_once __DIR__ . '/../../app/TCPDF/tcpdf.php';
  
      // Crear un nuevo objeto PDF
      $pdf = new TCPDF();
  
      // Agregar una página
      $pdf->AddPage();
  
      // Obtener todos los alumnos utilizando el modelo
      $alumnos = alumnoModel::all();
  
      // Crear una tabla en el PDF con estilos CSS
      $html = '<style>
                    th {
                        background-color: #f2f2f2;
                        padding: 8px;
                        text-align: left;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border: 1px solid #ddd;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    .column-id {
                        width: 10%;
                    }
                    .column-carnet {
                      width: 12%;
                  }
                    .column-nombre {
                        width: 35%;
                    }
                    .column-email {
                        width: 30%;
                    }
                    .column-status {
                        width: 12%;
                    }
                </style>';
  
      // Agregar el encabezado
      $html .= '<h2 style="text-align: center;">LISTA DE ALUMNOS REGISTRADOS</h2>';
  
      // Agregar la tabla
      $html .= '<table>';
  
      // Agregar fila de encabezados
      $html .= '<tr><th class="column-id">No.</th><th class="column-carnet">Carnet</th><th class="column-nombre">Nombre Completo</th><th class="column-email">Correo Electrónico</th><th class="column-status">Status</th></tr>';
  
      // Agregar filas de datos
      foreach ($alumnos as $alumno) {
          $html .= '<tr><td class="column-id">' . $alumno['numero'] . '</td><td class="column-carnet">' . utf8_decode($alumno['identificacion']) .'</td><td class="column-nombre">' . utf8_decode($alumno['nombre_completo']) . '</td><td class="column-email">' . utf8_decode($alumno['email']) . '</td><td class="column-status">' . utf8_decode($alumno['status']) . '</td></tr>';
      }
      $html .= '</table>';
  
      // Agregar el contenido a la página
      $pdf->writeHTML($html, true, false, false, false, '');
  
      // Generar el PDF y mostrarlo en el navegador
      $pdf->Output('alumnos.pdf', 'I');
  
      // Redireccionar a la página desde la que se inició la exportación
      header('Location: ' . buildURL('alumnos')); // Cambia 'alumnos' por la URL correcta
  }
  
  
  
  function exportar_excel()
  {
      $alumnos = profesorModel::query('SELECT * FROM usuarios WHERE rol = "alumno" ORDER BY numero ASC');
  
      $filename = 'alumnos.xls';
  
      header("Content-Type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=\"$filename\"");
  
      echo '<table>';
      echo '<tr><th>Numero</th><th>Nombre Completo</th><th>Correo Electronico</th><th>Status</th></tr>';
  
      // Recorremos el arreglo de profesores en orden ascendente
      foreach ($alumnos as $alumno) {
          echo '<tr><td>' . $alumno['numero'] . '</td><td>' . utf8_decode($alumno['nombre_completo']) . '</td><td>' . utf8_decode($alumno['email']) . '</td><td>' . utf8_decode($alumno['status']) . '</td></tr>';
      }
  
      echo '</table>';
      exit;
  }
}

