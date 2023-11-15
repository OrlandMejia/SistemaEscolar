<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de dashboard
 */
class dashboardController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
  
  }
  
  //ACA VAMOS A CARGAR TODO LO QUE NECESITAMOS PARA EL ROL QUE NECESITEMOS Y PARA LA VISTA Y COLUMNAS DE NUESTROS REGISTROS
  function index()
  {
    $rol  = get_user_rol();
    $data = 
    [
      'title' => 'Dashboard',
      'slug'  => 'dashboard'
    ];

    if (is_admin($rol)) {
      
      $data['stats'] = adminModel::stats();
      View::render('dashboard_admin', $data);

    }if (is_profesor($rol)) {
      
      $data['stats'] = profesorModel::stats_by_id(get_user('id'));
      // Nueva línea para obtener el total de lecciones
     // $data['tareas_publicadas'] = profesorModel::tareas_publicadas(get_user('id'));
      View::render('dashboard_profesor', $data);
      
    } else if (is_alumno($rol)) {
      
      $data['grupo'] = grupoModel::by_alumno(get_user('id'));
      View::render('dashboard_alumno', $data);

    } else {

      echo 'Tu rol de usuario no es válido.';
      
    }
  }
  function tareas_publicadas_chart()
  {
      View::render('dashboard_profesor_chart');
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
}