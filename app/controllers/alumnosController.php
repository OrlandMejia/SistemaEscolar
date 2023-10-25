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

  function agregar()
  {
    $data = 
    [
      'title' => 'Todos los Alumnos',
      'slug' => 'alumnos',
      'button' => ['url' => 'alumnos', 'text' => '<i class="fas fa-table"></i> Agregar Alumno'],
      'grupos' => grupoModel::all()
    ];
    View::render('agregar',$data);
  }

  function post_agregar()
  {
    try {
      if (!check_posted_data(['csrf','nombres','apellidos','email','telefono','password','conf_password','id_grupo'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_notificaciones());
      }

     //validar el rol de la persona que quiera acceder al listado
      if(!is_admin(get_user_rol())){
        Flasher::new(get_notificaciones(0), 'danger');
        Redirect::back();
      }

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

      // Validar el nombre del usuario
      if (strlen($nombres) < 5) {
        throw new Exception('Ingresa un nombre válido.');
      }

      // Validar el apellido del usuario
      if (strlen($apellidos) < 5) {
        throw new Exception('Ingresa un apellido válido.');
      }

      // Validar el password del usuario
      if (strlen($password) < 5) {
        throw new Exception('Ingresa una contraseña mayor a 5 caracteres.');
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

      Flasher::new(sprintf('Alumno <b>%s</b> agregado con éxito e inscrito al grupo <b>%s</b>.', $alumno['nombre_completo'], $grupo['nombre']), 'success');
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
      if (!check_posted_data(['csrf','id','nombres','apellidos','email','telefono','password','conf_password','id_grupo'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception(get_notificaciones());
      }

     //validar el rol de la persona que quiera acceder al listado
     if(!is_admin(get_user_rol())){
      Flasher::new(get_notificaciones(0), 'danger');
      Redirect::back();
    }

      // Validar existencia del alumno
      $id = clean($_POST["id"]);
      if (!$alumno = alumnoModel::by_id($id)) {
        throw new Exception('No existe el alumno en la base de datos.');
      }
      //VARIABLES QUE YA SE ENCUENTRAN EN LA BASE DE DATOS
      $db_email      = $alumno['email'];
      $db_pw         = $alumno['password'];
      $db_status     = $alumno['status'];
      $db_id_g       = $alumno['id_grupo'];
      //VARIABLES DE INFORMACIÓN DEL FORMULARIO
      $nombres       = clean($_POST["nombres"]);
      $apellidos     = clean($_POST["apellidos"]);
      $email         = clean($_POST["email"]);
      $telefono      = clean($_POST["telefono"]);
      $password      = clean($_POST["password"]);
      $conf_password = clean($_POST["conf_password"]);
      $id_grupo      = clean($_POST["id_grupo"]);
      //VARIABLES QUE DETERMINAN EL CAMBIO DE INFORMACIÓN SENSIBLE
      //indican que si la nueva es igual entonces es false y si es distinta lanza true
      $changed_email = $db_email === $email ? false : true;
      $changed_pw    = false;
      $changed_g     = $db_id_g === $id_grupo ? false : true;

      // Validar existencia del correo electrónico
      $sql = 'SELECT * FROM usuarios WHERE email = :email AND id != :id LIMIT 1';
      if (usuarioModel::query($sql, ['email' => $email, 'id' => $id])) {
        throw new Exception('El correo electrónico ya existe en la base de datos.');
      }

      // Validar que el correo sea válido solo pasa si hay un cambio en el correo electronico
      if ($changed_email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa un correo electrónico válido.');
      }

      // Validar el nombre del usuario
      if (strlen($nombres) < 5) {
        throw new Exception('Ingresa un nombre válido.');
      }

      // Validar el apellido del usuario
      if (strlen($apellidos) < 5) {
        throw new Exception('Ingresa un apellido válido.');
      }

      // Validar el password del usuario segun la función de la verificacion de contraseña
      $pw_ok = password_verify($db_pw, $password.AUTH_SALT);
      if (!empty($password) && $pw_ok === false && strlen($password) < 7) {
        throw new Exception('Ingresa una contraseña mayor a 7 caracteres.');
      }

      // Validar ambas contraseñas
      if (!empty($password) && $pw_ok === false && $password !== $conf_password) {
        throw new Exception('Las contraseñas no son iguales.');
      }

      // Exista el id_grupo que exista el grado
      if ($id_grupo === '' || !grupoModel::by_id($id_grupo)) {
        throw new Exception('Selecciona un Grado válido.');
      }

      $data   =
      [
        'nombres'         => $nombres,
        'apellidos'       => $apellidos,
        'nombre_completo' => sprintf('%s %s', $nombres, $apellidos),
        'email'           => $email,
        'telefono'        => $telefono,
        'status'          => $changed_email ? 'pendiente' : $db_status
      ];

      // Actualización de contraseña
      if (!empty($password) && $pw_ok === false) {
        $data['password'] = password_hash($password.AUTH_SALT, PASSWORD_BCRYPT);
        $changed_pw       = true; //se actualizo la contraseña anteriormente
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
      $grupo  = grupoModel::by_id($id_grupo);
      
      Flasher::new(sprintf('Alumno <b>%s</b> actualizado con éxito.', $alumno['nombre_completo']), 'success');

        //NOTIFICACIONES QUE INDICAN SI HUBO ALGUN CAMBIO EN LOS DATOS SENSIBLES
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
    // Proceso de borrado
  }
}