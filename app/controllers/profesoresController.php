<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de profesores
 */
class profesoresController extends Controller {
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
    //validar el rol de la persona que quiera acceder al listado
    if(!is_admin(get_user_rol())){
      Flasher::new(get_notificaciones(0), 'danger');
      Redirect::back();
    }
    //array que contiene los datos del titulo la viable slug que activa el link y la url del boton para agregar
    $data = 
    [
      'title' => 'Todos los Profesores',
      'slug' => 'profesores',
      'button' => ['url' => buildURL('profesores/agregar'), 'text' => '<i class="fas fa-plus"></i> Agregar Profesor'],
      'profesores' => profesorModel::all_paginated()
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($numero)
  {

    if (!$profesor = profesorModel::by_numero($numero)) {
      Flasher::new('No existe el profesor en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'  => sprintf('Profesor #%s', $profesor['numero']),
      'slug'   => 'profesores',
      'button' => ['url' => 'profesores', 'text' => '<i class="fas fa-table"></i> Profesores'],
      'p'      => $profesor
    ];

    View::render('ver', $data);

  }

  function agregar()
  {
    //PROCESO DE AGREGAR REGISTRO A LA BASE DE DATOS
    try {
      //code...
      //validamos el token csrf y los campos verificando que esten correctos para poder a agregar los datos y dejar pasar la información
      if(!check_posted_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])){
        throw new Exception(get_notificaciones(0));
      }

      //VALIDAMOS EL ROL DEL USUARIO PARA QUE SOLO LOS USUARIOS AUTORIZADOS PUEDAN UTILIZAR ESTA FUNCION
      if(!is_admin(get_user_rol())){
        throw new Exception(get_notificaciones(1), 1);
      }
      //variable para crear numeros random y asignarlos al profesor
      $numero = rand(1111,9999);
      $data = 
      [
        'numero' => $numero,
        'dpi' => null,
        'nombres' => null,
        'apellidos' => null,
        'nombre_completo' => null,
        'email' => null,
        'password' => null,
        'hash' => generate_token(),
        'rol' => 'profesor',
        'status' => 'pendiente',
        'creado' => now()
      ];

      //INSERTAR A LA BASE DE DATOS
      if(!$id = profesorModel::add(profesorModel::$t1, $data)){
        throw new Exception(get_notificaciones(2));
      }

      //notificación de logrado
      Flasher::new(sprintf('Nuevo Profesor <b>%s</b> agregado con éxito.', $numero), 'success');
      Redirect::to(sprintf('profesores/ver/%s',$numero));


    } catch (PDOException $e) {
      //throw $th;
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }catch (Exception $e) {
      //throw $th;
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }


  function post_agregar()
  {

  }


  function post_editar()
  {
    try {
      //revisamos la data y verificamos el token csrf y los datos que estan en el profesor
  if (!check_posted_data(['csrf','id','dpi','nombres','apellidos','email','telefono','password'], $_POST) || !Csrf::validate($_POST['csrf'])) {
    throw new Exception(get_notificaciones());
}


      // Validar rol que seamos administradores
      //VALIDAMOS EL ROL DEL USUARIO PARA QUE SOLO LOS USUARIOS AUTORIZADOS PUEDAN UTILIZAR ESTA FUNCION
      if(!is_admin(get_user_rol())){
        throw new Exception(get_notificaciones(1), 1);
      }

      $id = clean($_POST["id"]);


      //verificamos que el registro exista en la base de datos
      if (!$profesor = profesorModel::by_id($id)) {
        throw new Exception('No existe el profesor en la base de datos.');
      }

      //creación de variables para insertar la información con POST
      $dpi = clean($_POST["dpi"]);

      // Validar longitud del DPI
      if (strlen($dpi) !== 13) {
      throw new Exception('El DPI debe tener 13 caracteres.');
      }

      $nombres = clean($_POST["nombres"]);
      $apellidos = clean($_POST["apellidos"]);
      $email = clean($_POST["email"]);
      $telefono = clean($_POST["telefono"]);
      $password = clean($_POST["password"]);

      // Validar que el correo sea válido
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa un correo electrónico válido.');
      }

      //objeto data con los parametros que le vamos a pasar para su inserción
      $data   =
      [
        'dpi' => $dpi,
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'nombre_completo' => sprintf('%s %s', $nombres, $apellidos),
        'email' => $email,
        'telefono' => $telefono
      ];

      // En caso de que se cambie el correo electrónico se coloca un estado en pendiente
      if ($profesor['email'] !== $email && !in_array($profesor['status'], ['pendiente', 'suspendido'])) {
        $data['status'] = 'pendiente'; //seteamos un valor del objeto data
      }

      // En caso de que se cambie la contraseña se valida la información
      if (!empty($password) && !password_verify($password.AUTH_SALT, $profesor['password'])) {
        $data['password'] = password_hash($password.AUTH_SALT, PASSWORD_BCRYPT);
      }

      // Insertar a la base de datos
      if (!profesorModel::update(profesorModel::$t1, ['id' => $id], $data)) {
        throw new Exception(get_notificaciones(3));
      }

      // Volver a cargar la información del profesor
      $profesor = profesorModel::by_id($id);

      Flasher::new(sprintf('Profesor <b>%s</b> actualizado con éxito.', $profesor['nombre_completo']), 'success');
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

  // Agregar función para exportar a PDF
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
  
      // Obtener los datos de los profesores ordenados por número de DPI
      $profesores = profesorModel::query('SELECT * FROM usuarios WHERE rol = "profesor" ORDER BY numero ASC');
  
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
      $html .= '<h2 style="text-align: center;">LISTA DE PROFESORES REGISTRADOS</h2>';
  
      // Agregar la tabla
      $html .= '<table>';
  
      // Agregar fila de encabezados
      $html .= '<tr><th class="column-id">No.</th><th class="column-dpi">DPI</th><th class="column-nombre">Nombre Completo</th><th class="column-email">Correo Electrónico</th><th class="column-status">Status</th></tr>';
  
      // Agregar filas de datos
      foreach ($profesores as $profe) {
          $html .= '<tr><td class="column-id">' . $profe['numero'] . '</td><td class="column-dpi">' . utf8_decode($profe['dpi']) .'</td><td class="column-nombre">' . utf8_decode($profe['nombre_completo']) . '</td><td class="column-email">' . utf8_decode($profe['email']) . '</td><td class="column-status">' . utf8_decode($profe['status']) . '</td></tr>';
      }
      $html .= '</table>';
  
      // Agregar el contenido a la página
      $pdf->writeHTML($html, true, false, false, false, '');
  
      // Generar el PDF y mostrarlo en el navegador
      $pdf->Output('profesores.pdf', 'I');
  
      // Redireccionar a la página desde la que se inició la exportación
      header('Location: ' . buildURL('profesores')); // Cambia 'profesores' por la URL correcta
  }
  
  
  // Agregar función para exportar a Excel
  function exportar_excel()
  {
      $profesores = profesorModel::query('SELECT * FROM usuarios WHERE rol = "profesor" ORDER BY numero ASC');
  
      $filename = 'profesores.xls';
  
      header("Content-Type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=\"$filename\"");
  
      echo '<table>';
      echo '<tr><th>DPI</th><th>Nombre Completo</th><th>Correo Electronico</th><th>Status</th></tr>';
  
      // Recorremos el arreglo de profesores en orden ascendente
      foreach ($profesores as $profe) {
          echo '<tr><td>' . $profe['numero'] . '</td><td>' . utf8_decode($profe['nombre_completo']) . '</td><td>' . utf8_decode($profe['email']) . '</td><td>' . utf8_decode($profe['status']) . '</td></tr>';
      }
  
      echo '</table>';
      exit;
  }
  
}