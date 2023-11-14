<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de notas
 */
class notasController extends Controller {

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
      'title'  => 'Calificaciones por Grado',
      'slug'   => 'notas',
      'button' => ['url' => 'grupos/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Grado'],
      'grupos' => grupoModel::all_paginated()
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($id_grupo)
  {
    if (!is_admin($this->rol)) {
      Flasher::new(get_notificaciones(), 'danger');
      Redirect::back();
    }

    // Obtener alumnos y calificaciones para el grupo dado
    $alumnosCalificaciones = notasModel::getAlumnosCalificaciones($id_grupo);

    // Datos para la vista
    $data = [
      'title' => 'Calificaciones por alumno',
      'slug' => 'notas',
      'button' => ['url' => 'alumnos/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Alumno'],
      'alumnosCalificaciones' => $alumnosCalificaciones
    ];

    // Descomentar vista si requerida
    View::render('ver', $data);
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