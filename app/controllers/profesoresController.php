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
      'button' => ['url' => 'profesores/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Profesor'],
      'profesores' => profesorModel::all_paginated()
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($id)
  {
    View::render('ver');
  }

  function agregar()
  {
    View::render('agregar');
  }

  function post_agregar()
  {

  }

  function editar($id)
  {
    View::render('editar');
  }

  function post_editar()
  {

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
                      width: 20%;
                  }
                  .column-nombre {
                      width: 35%;
                  }
                  .column-email {
                      width: 30%;
                  }
                  .column-status {
                      width: 10%;
                  }
              </style>';
  
      // Agregar el encabezado
      $html .= '<h2 style="text-align: center;">LISTA DE PROFESORES REGISTRADOS</h2>';
  
      // Agregar la tabla
      $html .= '<table>';
  
      // Agregar fila de encabezados
      $html .= '<tr><th class="column-id">DPI</th><th class="column-nombre">Nombre Completo</th><th class="column-email">Correo Electrónico</th><th class="column-status">Status</th></tr>';
  
      // Agregar filas de datos
      foreach ($profesores as $profe) {
          $html .= '<tr><td class="column-id">' . $profe['numero'] . '</td><td class="column-nombre">' . utf8_decode($profe['nombre_completo']) . '</td><td class="column-email">' . utf8_decode($profe['email']) . '</td><td class="column-status">' . utf8_decode($profe['status']) . '</td></tr>';
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