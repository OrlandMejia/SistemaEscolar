<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de grupo
 */
class grupoModel extends Model {
  public static $t1   = 'grupos'; // Nombre de la tabla en la base de datos;
  
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
    $sql = 'SELECT * FROM grupos ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }
  static function all_paginated()
  {
    // Todos los registros ya paginados
    $sql = 'SELECT * FROM grupos ORDER BY id ASC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM grupos WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }
}

