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
    if(!is_user(get_user_rol(), ['admin'])){
      Redirect::to('home');
    }
    $data = 
    [
      'title' => 'Reemplazar título',
      'msg'   => 'Bienvenido al controlador de "dashboard", se ha creado con éxito si ves este mensaje.'
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
}