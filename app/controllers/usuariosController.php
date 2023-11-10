<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de usuarios
 */
class usuariosController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    /**
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
    */
  }
  
  function index()
  {
    $data = [
      'title' => 'Usuarios Administradores Registrados',
      'slug' => 'usuarios',
      'msg' => 'Bienvenido al controlador de "usuarios", se ha creado con éxito si ves este mensaje.',
      'usuarios' => usuariosModel::all_paginated(), // Obtener usuarios con rol "admin"
    ];

    View::render('index', $data); // Cargar la vista de usuarios con el nuevo dato 'usuarios'
  }

  function ver($numero)
  {

    if (!$usuario = usuariosModel::by_numero($numero)) {
      Flasher::new('No existe el usuario en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'  => sprintf('Usuario #%s', $usuario['numero']),
      'slug'   => 'profesores',
      'button' => ['url' => 'usuarios', 'text' => '<i class="fas fa-table"></i> Usuarios'],
      'p'      => $usuario 
    ];

    View::render('ver', $data);

  }

  function agregar()
  {
    $data = 
    [
      'title' => 'Agregar un usuario administrador',
      'slug' => 'admin',
      'msg'   => 'Bienvenido al controlador de "usuarios", se ha creado con éxito si ves este mensaje.',
      'button' => ['url' => buildURL('usuarios/index'), 'text' => '<i class="fas fa-plus"></i> Ver Usuarios']
    ];
    View::render('agregar', $data);
  }

  function post_agregar()
  {
    try {
      if (!check_posted_data(['csrf','identificacion','nombres','apellidos','email','telefono','password','conf_password'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_notificaciones());
      }

  //validar el rol de la persona que quiera acceder al listado
  if(!is_admin(get_user_rol())){
    Flasher::new(get_notificaciones(0), 'danger');
    Redirect::back();
  }
    //VARIABLES PARA INGRESAR DATOS EN LA BASE DE DATOS
      $identificacion         = clean($_POST['identificacion']);
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
      if(strlen($identificacion) < 5){
        throw new Exception('Ingresa un numero de identificacion valido');
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

      $numero = rand(111111, 999999);
      $data   =
      [
        'numero'          => $numero,
        'identificacion'  => $identificacion,
        'nombres'         => $nombres,
        'apellidos'       => $apellidos,
        'nombre_completo' => sprintf('%s %s', $nombres, $apellidos),
        'email'           => $email,
        'telefono'        => $telefono,
        'password'        => password_hash($password.AUTH_SALT, PASSWORD_BCRYPT),
        'hash'            => generate_token(),
        'rol'             => 'admin',
        'status'          => 'pendiente',
        'creado'          => now()
      ];

      // Insertar a la base de datos
      if (!$id = usuariosModel::add(usuarioModel::$t1, $data)) {
        throw new Exception(get_notificaciones(2));
      }

      // Email de confirmación de correo
      mail_confirmar_cuenta($id);

      Flasher::new(sprintf('Nuevo Administrador <b>%s</b> agregado con éxito. Porfavor Confirmar el Correo Electronico', $numero), 'success');
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
          if (!check_posted_data(['csrf', 'id','identificacion', 'nombres', 'apellidos', 'email', 'telefono', 'password', 'conf_password'], $_POST) || !Csrf::validate($_POST['csrf'])) {
              throw new Exception(get_notificaciones());
          }
  
          // Validar el rol de la persona que quiera acceder al listado
          if (!is_admin(get_user_rol())) {
              Flasher::new(get_notificaciones(0), 'danger');
              Redirect::back();
          }
  
          // Validar existencia del alumno
          $id = clean($_POST["id"]);
          if (!$usuarios = usuariosModel::by_id($id)) {
              throw new Exception('No existe el alumno en la base de datos.');
          }
  
          // VARIABLES DE INFORMACIÓN DEL FORMULARIO
          $identificacion = clean($_POST['identificacion']);
          $nombres = clean($_POST["nombres"]);
          $apellidos = clean($_POST["apellidos"]);
          $email = clean($_POST["email"]);
          $telefono = clean($_POST["telefono"]);
          $password = clean($_POST["password"]);
          $conf_password = clean($_POST["conf_password"]);
  
  
          // Validar existencia del correo electrónico
          $sql = 'SELECT * FROM usuarios WHERE email = :email AND id != :id LIMIT 1';
          if (usuarioModel::query($sql, ['email' => $email, 'id' => $id])) {
              throw new Exception('El correo electrónico ya existe en la base de datos.');
          }
  
          // Validar que el correo sea válido solo pasa si hay un cambio en el correo electrónico
          if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
  
          $data = [
              'identificacion' => $identificacion,
              'nombres' => $nombres,
              'apellidos' => $apellidos,
              'nombre_completo' => sprintf('%s %s', $nombres, $apellidos),
              'email' => $email,
              'telefono' => $telefono
          ];
  
          // Actualización de contraseña
          if (!empty($password)) {
              $data['password'] = password_hash($password . AUTH_SALT, PASSWORD_BCRYPT);
              $changed_pw = true;
          }
  
          // Actualizar base de datos
          if (!usuariosModel::update(alumnoModel::$t1, ['id' => $id], $data)) {
              throw new Exception(get_notificaciones(2));
          }
  
          $alumno = usuariosModel::by_id($id);
  
          Flasher::new(sprintf('Alumno <b>%s</b> actualizado con éxito.', $alumno['nombre_completo']), 'success');
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
    // Proceso de borrado
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
  
      // Obtener los datos de los usuarios con rol "admin" ordenados por número de DPI
      $usuarios = usuariosModel::query('SELECT * FROM usuarios WHERE rol = "admin" ORDER BY numero ASC');
  
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
                  .column-dpi {
                      width: 18%;
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
      $html .= '<h2 style="text-align: center;">LISTA DE USUARIOS ADMINISTRADORES REGISTRADOS</h2>';
  
      // Agregar la tabla
      $html .= '<table>';
  
      // Agregar fila de encabezados
      $html .= '<tr><th class="column-dpi">DPI</th><th class="column-nombre">Nombre Completo</th><th class="column-email">Correo Electrónico</th><th class="column-status">Status</th></tr>';
  
      // Agregar filas de datos
      foreach ($usuarios as $usuario) {
          $html .= '<tr><td class="column-dpi">' . utf8_decode($usuario['identificacion']) .'</td><td class="column-nombre">' . utf8_decode($usuario['nombre_completo']) . '</td><td class="column-email">' . utf8_decode($usuario['email']) . '</td><td class="column-status">' . utf8_decode($usuario['status']) . '</td></tr>';
      }
      $html .= '</table>';
  
      // Agregar el contenido a la página
      $pdf->writeHTML($html, true, false, false, false, '');
  
      // Generar el PDF y mostrarlo en el navegador
      $pdf->Output('usuarios_admin.pdf', 'I');
  
      // Redireccionar a la página desde la que se inició la exportación
      header('Location: ' . buildURL('usuarios')); // Cambia 'usuarios' por la URL correcta
  }
  
  function exportar_excel()
  {
      $usuarios = usuariosModel::query('SELECT * FROM usuarios WHERE rol = "admin" ORDER BY numero ASC');
  
      $filename = 'usuarios_admin.xls';
  
      header("Content-Type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=\"$filename\"");
  
      echo '<table>';
      echo '<tr><th>DPI</th><th>Nombre Completo</th><th>Correo Electrónico</th><th>Status</th></tr>';
  
      // Recorremos el arreglo de usuarios con rol "admin" en orden ascendente
      foreach ($usuarios as $usuario) {
          echo '<tr><td>' . $usuario['numero'] . '</td><td>' . utf8_decode($usuario['nombre_completo']) . '</td><td>' . utf8_decode($usuario['email']) . '</td><td>' . utf8_decode($usuario['status']) . '</td></tr>';
      }
  
      echo '</table>';
      exit;
  }
}