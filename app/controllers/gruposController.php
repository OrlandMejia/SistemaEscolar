<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.2
 *
 * Controlador de grupos
 */
class gruposController extends Controller {
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
      'title'  => 'Todos los Grados',
      'slug'   => 'grupos',
      'button' => ['url' => 'grupos/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar grupo'],
      'grupos' => grupoModel::all_paginated()
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
    {
      try {
        if (!check_posted_data(['csrf','nombre','descripcion'], $_POST) || !Csrf::validate($_POST['csrf'])) {
          throw new Exception(get_notificaciones());
        }
  
        // Validar rol
        if(!is_admin(get_user_rol())){
          throw new Exception(get_notificaciones(1), 1);
        }
  
        $nombre      = clean($_POST["nombre"]);
        $descripcion = clean($_POST["descripcion"]);
  
        // Validar la longitud del nombre
        if (strlen($nombre) < 5) {
          throw new Exception('El nombre del grupo es demasiado corto.');
        }
  
        // Validar que el nombre del grupo no exista en la base de datos
        $sql = 'SELECT * FROM grupos WHERE nombre = :nombre LIMIT 1';
        if (grupoModel::query($sql, ['nombre' => $nombre])) {
          throw new Exception(sprintf('Ya existe el grupo <b>%s</b> en la base de datos.', $nombre));
        }
  
        $data =
        [
          'numero'      => rand(111, 999),
          'nombre'      => $nombre,
          'descripcion' => $descripcion,
          'horario'     => null,
          'creado'      => now()
        ];
  
        // Insertar a la base de datos
        if (!$id = grupoModel::add(grupoModel::$t1, $data)) {
          throw new Exception(get_notificaciones(2));
        }
  
        Flasher::new(sprintf('Nuevo grupo <b>%s</b> agregado con éxito.', $nombre), 'success');
        Redirect::back();
  
      } catch (PDOException $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
      } catch (Exception $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
      }
    }
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