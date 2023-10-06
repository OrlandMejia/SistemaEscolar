<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de materias
 */
class materiasController extends Controller {
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
    $data = 
    [
      'title' => 'Todas las Materias',
      'slug' => 'materias',
      'button' => ['url' => 'materias/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Materia'],
      'materias' => materiaModel::all_paginated()
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($id)
  {
    if(!$materia = materiaModel::by_id($id)){
      Flasher::new('No existe la materia en la base de datos.','danger');
      Redirect::to('materias');
    }
    $data = 
    [
      'title' => sprintf('Viendo la Materia: %s', $materia['nombre']),
      'slug' => 'materias',
      'button' => ['url' => 'materias', 'text' => '<i class="fas fa-table"></i> Materias'],
      'm' => $materia
    ];
    View::render('ver',$data);
  }

  function agregar()
  {
    //creamos un array y le pasamos la información de la vista
    $data =
    [
      'title' => 'Agregar Materia',
      'slug' => 'materias',
      'button' => ['url' => 'materias', 'text' => '<i class="fas fa-table"></i> Materias']
    ];
    View::render('agregar', $data);
  }

  function post_agregar()
  {
    //PROCESO DE AGREGAR REGISTRO A LA BASE DE DATOS
    try {
      //code...
      //validamos el token csrf y los campos verificando que esten correctos para poder a agregar los datos y dejar pasar la información
      if(!check_posted_data(['csrf','nombre','descripcion'], $_POST) || !Csrf::validate($_POST['csrf'])){
        throw new Exception(get_notificaciones(0));
      }

      //VALIDAMOS EL ROL DEL USUARIO PARA QUE SOLO LOS USUARIOS AUTORIZADOS PUEDAN UTILIZAR ESTA FUNCION
      if(!is_admin(get_user_rol())){
        throw new Exception(get_notificaciones(1), 1);
      }

      $nombre = clean($_POST["nombre"]);
      $descripcion = clean($_POST["descripcion"]);

      //VALIDAR LA LONGITUD DEL NOMBRE
      if(strlen($nombre) < 5){
        throw new Exception('El nombre de la materia es demasiado corto');
      }

            //VALIDAR QUE EL NOMBRE DE LA MATERIA NO EXISTA EN LA BASE DE DATOS
            $sql = 'SELECT * FROM materias WHERE nombre = :nombre LIMIT 1';
            if(materiaModel::query($sql, ['nombre' => $nombre])){
              throw new Exception(sprintf('Ya existe la materia con el nombre <b>%s</b> en la base de datos.', $nombre));
              
            }

      //creamos nuestro data que es la informacion de la base de datos
      $data =
      [
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'creado' => now()
      ];

      //INSERTAR A LA BASE DE DATOS
      if(!$id = materiaModel::add(materiaModel::$t1, $data)){
        throw new Exception('Error al guardar el registro');
      }

      //notificación de logrado
      Flasher::new(sprintf('Materia <b>%s</b> agregada con éxito.', $nombre), 'success');
      Redirect::back();


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


  function post_editar()
  {
    //PROCESO DE AGREGAR REGISTRO A LA BASE DE DATOS
    try {
      //code...
      //validamos el token csrf y los campos verificando que esten correctos para poder a agregar los datos y dejar pasar la información
      if(!check_posted_data(['csrf','id','nombre','descripcion'], $_POST) || !Csrf::validate($_POST['csrf'])){
        throw new Exception('Acceso no autorizado.');
      }

      //VALIDAMOS EL ROL DEL USUARIO PARA QUE SOLO LOS USUARIOS AUTORIZADOS PUEDAN UTILIZAR ESTA FUNCION
      if(!is_admin(get_user_rol())){
        throw new Exception(get_notificaciones(1), 1);
      }

      //creamos los objetos y limpiamos y se le asignan los valores contenidos dentro de id, nombre y descripcion
      $id = clean($_POST["id"]);
      $nombre = clean($_POST["nombre"]);
      $descripcion = clean($_POST["descripcion"]);

      if(!$materia = materiaModel::by_id($id)){
        throw new Exception('No existe la Materia en la Base de Datos');
      }

      //VALIDAR LA LONGITUD DEL NOMBRE
      if(strlen($nombre) < 5){
        throw new Exception('El nombre de la materia es demasiado corto');
      }
      //VALIDAR QUE EL NOMBRE DE LA MATERIA NO EXISTA EN LA BASE DE DATOS
      $sql = 'SELECT * FROM materias WHERE id != :id AND nombre = :nombre LIMIT 1';
      if(materiaModel::query($sql, ['id' => $id, 'nombre' => $nombre])){
        throw new Exception(sprintf('Ya existe la materia <b>%s</b> en la base de datos.', $nombre));
        
      }

      //creamos nuestro data que es la informacion de la base de datos
      $data =
      [
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'creado' => now()
      ];

      //INSERTAR A LA BASE DE DATOS
      if(!materiaModel::update(materiaModel::$t1, ['id'=>$id], $data)){
        throw new Exception('Error al Actualizar el registro');
      }

      //notificación de logrado
      Flasher::new(sprintf('Materia <b>%s</b> Actualizada con éxito.', $nombre), 'success');
      Redirect::back();


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

  function borrar($id)
  {
    // Proceso de borrado
    //PROCESO DE AGREGAR REGISTRO A LA BASE DE DATOS
    try {
      //code...
      //validamos el token csrf y los campos verificando que esten correctos para poder a agregar los datos y dejar pasar la información
      //_t es el token de seguridad
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        throw new Exception(get_notificaciones(0));
      }

      //VALIDAMOS EL ROL DEL USUARIO PARA QUE SOLO LOS USUARIOS AUTORIZADOS PUEDAN UTILIZAR ESTA FUNCION
      if(!is_admin(get_user_rol())){
        throw new Exception(get_notificaciones(1));
      }

      if(!$materia = materiaModel::by_id($id)){
        throw new Exception('No existe la Materia en la Base de Datos');
      }

      //ELIMINAR EL REGISTRO DE LA BASE DE DATOS
      if(!materiaModel::remove(materiaModel::$t1, ['id'=>$id], 1)){
        throw new Exception('Hubo un error al borrar el registro');
      }

      //notificación de logrado
      Flasher::new(sprintf('Materia <b>%s</b> Borrada con éxito.', $materia['nombre']), 'success');
      Redirect::back();


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

  // materiasController.php


// Agregar función para exportar a PDF
function exportar_pdf()
{
  $data = 
  [
    'title' => 'Exportar PDF'
  ];

    // Cargar la biblioteca de generación de PDF (por ejemplo, TCPDF)
    require_once __DIR__ . '/../../app/TCPDF/tcpdf.php';

    // Crear un nuevo objeto PDF
    $pdf = new TCPDF();

    // Agregar una página
    $pdf->AddPage();

    // Obtener los datos de las materias ordenados por id de menor a mayor
    $materias = materiaModel::query('SELECT * FROM materias ORDER BY id ASC');

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
                .column-nombre {
                    width: 30%;
                }
                .column-descripcion {
                    width: 60%;
                }
            </style>';
    
    // Agregar el encabezado
    $html .= '<h2 style="text-align: center;">LISTA DE MATERIAS REGISTRADAS</h2>';
    
    // Agregar la tabla
    $html .= '<table>';
    
    // Agregar fila de encabezados
    $html .= '<tr><th class="column-id">ID</th><th class="column-nombre">Nombre</th><th class="column-descripcion">Descripción</th></tr>';
    
    // Agregar filas de datos
    foreach ($materias as $materia) {
        $html .= '<tr><td class="column-id">' . $materia['id'] . '</td><td class="column-nombre">' . $materia['nombre'] . '</td><td class="column-descripcion">' . $materia['descripcion'] . '</td></tr>';
    }
    $html .= '</table>';

    // Agregar el contenido a la página
    $pdf->writeHTML($html, true, false, false, false, '');

    // Generar el PDF y mostrarlo en el navegador
    $pdf->Output('materias.pdf', 'I');

    View::render('ver',$data);
}

// Agregar función para exportar a Excel
function exportar_excel()
{
  $materias = materiaModel::query('SELECT * FROM materias ORDER BY id ASC');

    $filename = 'materias.xls';

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre</th><th>Descripcion</th></tr>';

    // Recorremos el arreglo de materias en orden ascendente
    foreach ($materias as $materia) {
        echo '<tr><td>' . $materia['id'] . '</td><td>' . utf8_decode($materia['nombre']) . '</td><td>' . utf8_decode($materia['descripcion']) . '</td></tr>';
    }

    echo '</table>';
    exit;
}
}