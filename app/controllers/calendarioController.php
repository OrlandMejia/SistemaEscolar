<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de calendario
 */
class calendarioController extends Controller {
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
    $data = 
    [
      'title' => 'Reemplazar título',
      'msg'   => 'Bienvenido al controlador de "calendario", se ha creado con éxito si ves este mensaje.'
    ];
    
    // Descomentar vista si requerida
    View::render('index', $data);
  }

  function ver($id)
  {
    View::render('ver');
  }

  public function registrar()
  {
      if (isset($_POST)) {
          if (empty($_POST['title']) || empty($_POST['start']) || empty($_POST['fin'])) {
          }else{
              $title = $_POST['title'];
              $start = $_POST['start'];
              $fin = $_POST['fin'];
              $color = $_POST['color'];
              $id = $_POST['id'];
              if ($id == '') {
                  $data = $this->model->registrar($title, $start, $fin, $color);
                  if ($data == 'ok') {
                      $msg = array('msg' => 'Evento Registrado', 'estado' => true, 'tipo' => 'success');
                  }else{
                      $msg = array('msg' => 'Error al Registrar', 'estado' => false, 'tipo' => 'danger');
                  }
              } else {
                  $data = $this->model->modificar($title, $start, $color, $id);
                  if ($data == 'ok') {
                      $msg = array('msg' => 'Evento Modificado', 'estado' => true, 'tipo' => 'success');
                  } else {
                      $msg = array('msg' => 'Error al Modificar', 'estado' => false, 'tipo' => 'danger');
                  }
              }
              
          }
          echo json_encode($msg);
      }
      die();
  }
  public function listar()
  {
      $data = $this->model->getEventos();
      echo json_encode($data);
      die();
  }
  public function eliminar($id)
  {
      $data = $this->model->eliminar($id);
      if ($data == 'ok') {
          $msg = array('msg' => 'Evento Eliminado', 'estado' => true, 'tipo' => 'success');
      } else {
          $msg = array('msg' => 'Error al Eliminar', 'estado' => false, 'tipo' => 'danger');
      }
      echo json_encode($msg);
      die();
  }
  public function drag()
  {
      if (isset($_POST)) {
          if (empty($_POST['id']) || empty($_POST['start'])) {
              $msg = array('msg' => 'Todo los campos son requeridos', 'estado' => false, 'tipo' => 'danger');
          } else {
              $start = $_POST['start'];
              $id = $_POST['id'];
              $data = $this->model->dragOver($start, $id);
              if ($data == 'ok') {
                  $msg = array('msg' => 'Evento Modificado', 'estado' => true, 'tipo' => 'success');
              } else {
                  $msg = array('msg' => 'Error al Modificar', 'estado' => false, 'tipo' => 'danger');
              }
          }
          echo json_encode($msg);
      }
      die();
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