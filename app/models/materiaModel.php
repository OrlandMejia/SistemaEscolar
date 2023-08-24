<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de materia
 */
class materiaModel extends Model {
  public static $t1   = 'materias'; // Nombre de la tabla en la base de datos;
  
  // Nombre de tabla 2 que talvez tenga conexión con registros
  //public static $t2 = '__tabla 2___'; 
  //public static $t3 = '__tabla 3___'; 

  function __construct()
  {
    // Constructor general
  }
  
  static function all()
  {
    // Todos los registros
    $sql = 'SELECT * FROM materias ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  //FUNCION DE PAGINACION PARA PAGINAR LOS REGISTROS EN PAGINAS DIFERENTES
  static function all_paginated(){
    $sql = 'SELECT * FROM materias ORDER BY id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM materias WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  //función para cargar las materias disponibles para cada profesor
  static function disponibles_profesor($id_profesor)
  {
    $sql = 
    'SELECT
      m.*
    FROM
      materias m
    WHERE
      m.id NOT IN (
        SELECT
          mp.id_materia
        FROM
          materias_profesores mp
        WHERE
          mp.id_profesor = :id_profesor
      )';
    return ($rows = parent::query($sql, ['id_profesor' => $id_profesor])) ? $rows : [];
  }

  static function materias_profesor($id_profesor)
  {
    $sql = 
    'SELECT
      m.*
    FROM
      materias m
    WHERE
      m.id IN (
        SELECT
          mp.id_materia
        FROM
          materias_profesores mp
        WHERE
          mp.id_profesor = :id_profesor
      )';
    return ($rows = parent::query($sql, ['id_profesor' => $id_profesor])) ? $rows : [];
  }
}

