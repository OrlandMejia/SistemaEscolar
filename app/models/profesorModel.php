<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de profesor
 */
class profesorModel extends Model {
  public static $t1   = 'usuarios'; // Nombre de la tabla en la base de datos;
  
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
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  //FUNCION QUE EXTRAE TODOS LOS REGISTRO YA PAGINADOS
  static function all_paginated()
  {
    // Todos los registros ya paginados
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" ORDER BY id DESC';
    return PaginationHandler::paginate($sql);
  }

  //busca los datos según el id del registro
  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" AND id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  //busca los datos según el numero de profesor
  static function by_numero($numero)
  {
    // Un registro con $numero
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" AND numero = :numero LIMIT 1';
    return ($rows = parent::query($sql, ['numero' => $numero])) ? $rows[0] : [];
  }
}