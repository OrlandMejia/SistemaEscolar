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

    if (!$grupo = grupoModel::by_id($id_grupo)) {
      Flasher::new('No existe el grupo en la base de datos.', 'danger');
      Redirect::back();
    }

    // Obtener alumnos y calificaciones para el grupo dado
    $alumnosCalificaciones = notasModel::getAlumnosCalificaciones($id_grupo);

    // Datos para la vista
    $data = [
      'title'  => sprintf('Calificaciones Alumnos de: %s', $grupo['nombre']),
      'slug' => 'notas',
      'button' => ['url' => 'alumnos/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Alumno'],
      'alumnosCalificaciones' => $alumnosCalificaciones
    ];

    // Descomentar vista si requerida
    View::render('ver', $data);
  }

  function agregar($id_grupo) {
    // Obtener alumnos sin calificación para el grupo dado
    $alumnosSinCalificacion = notasModel::getAlumnosSinCalificacion($id_grupo);

    // Resto del código...
    
    $data = [
        'title' => 'Agregar Calificaciones',
        'slug' => 'notas',
        'button' => ['url' => 'alumnos/agregar', 'text' => '<i class="fas fa-plus"></i> Agregar Alumno'],
        'alumnos' => $alumnosSinCalificacion
    ];

    // Descomentar vista si requerida
    View::render('agregar', $data);
}

function post_agregar()
{
    try {
        if (!check_posted_data(['csrf', 'id_alumno', 'primer_bimestre', 'segundo_bimestre', 'tercer_bimestre', 'cuarto_bimestre'], $_POST) || !Csrf::validate($_POST['csrf'])) {
            throw new Exception(get_notificaciones());
        }

        if (!is_admin($this->rol)) {
          Flasher::new(get_notificaciones(), 'danger');
          Redirect::back();
        }

        // Obtener datos del formulario
        $id_alumno = clean($_POST['id_alumno']);
        $primer_bimestre = clean($_POST['primer_bimestre']);
        $segundo_bimestre = clean($_POST['segundo_bimestre']);
        $tercer_bimestre = clean($_POST['tercer_bimestre']);
        $cuarto_bimestre = clean($_POST['cuarto_bimestre']);

        // Validar las calificaciones (puedes agregar más validaciones según tus necesidades)
        if (!is_numeric($primer_bimestre) || !is_numeric($segundo_bimestre) || !is_numeric($tercer_bimestre) || !is_numeric($cuarto_bimestre)) {
            throw new Exception('Ingresa calificaciones numéricas válidas.');
        }

        // Crear un array con los datos de calificación
        $calificacion_data = [
            'id_usuario' => $id_alumno,
            'primer_bimestre' => $primer_bimestre,
            'segundo_bimestre' => $segundo_bimestre,
            'tercer_bimestre' => $tercer_bimestre,
            'cuarto_bimestre' => $cuarto_bimestre,
            'promedio' => ($primer_bimestre + $segundo_bimestre + $tercer_bimestre + $cuarto_bimestre) / 4
        ];

        // Insertar la calificación en la base de datos
        if (!$id_calificacion = notasModel::add(notasModel::$t2, $calificacion_data)) {
            throw new Exception(get_notificaciones(2));
        }

        // Redirigir o mostrar mensaje de éxito según tu lógica
        Flasher::new('Calificaciones agregadas con éxito.', 'success');
        Redirect::to('notas');

    } catch (PDOException $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
    } catch (Exception $e) {
        Flasher::new($e->getMessage(), 'danger');
        Redirect::back();
    }
}


public function editar($id)
{
  $alumno = notasModel::by_id_notas($id);

  if (empty($alumno)) {
    Flasher::new('No existe el Alumno en la base de datos o no tiene calificaciones.', 'danger');
    Redirect::back();
  }

  // Cargar otros datos necesarios para la vista de edición si es necesario

  $data = [
    'title'  => sprintf('Calificaciones del Alumno: %s', $alumno[0]['nombre_completo']),
    'slug' => 'notas',
    'button' => ['url' => 'notas', 'text' => '<i class="fas fa-table"></i> Ver Notas'],
    'ac' => $alumno[0]
    // Otros datos necesarios para la vista de edición
  ];

  View::render('editar', $data);
  //echo debug($data);
}




// En el controlador notasController.php
function post_editar()
{
  
}




  function borrar($id)
  {
    // Proceso de borrado
  }
}