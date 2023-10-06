<?php

/**
 * Plantilla general de modelos
 * Versi�n 1.0.1
 *
 * Modelo de profesor
 */
class profesorModel extends Model {
  public static $t1   = 'usuarios'; // Nombre de la tabla en la base de datos;
  
  // Nombre de tabla 2 que talvez tenga conexi�n con registros
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
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" ORDER BY id ASC';
    return PaginationHandler::paginate($sql);
  }

  //busca los datos seg�n el id del registro
  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" AND id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  //busca los datos seg�n el numero de profesor
  static function by_numero($numero)
  {
    // Un registro con $numero
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" AND numero = :numero LIMIT 1';
    return ($rows = parent::query($sql, ['numero' => $numero])) ? $rows[0] : [];
  }
  //FUNCION PARA ASIGNAR LA MATERIA EN LA BASE DE DATOS RELACIONAL
  static function asignar_materia($id_profesor, $id_materia){
    $data = [
      'id_materia' => $id_materia,
      'id_profesor' => $id_profesor,
    ];

    if(!$id = self::add('materias_profesores', $data)) return false;
    return $id;
  }

  //FUNCI�N PARA ELIMINAR LA MATERIA DE LA BASE DE DATOS
  static function quitar_materia($id_profesor, $id_materia){
    $data = [
      'id_materia' => $id_materia,
      'id_profesor' => $id_profesor,
    ];
    return (self::remove('materias_profesores', $data)) ? true: false;
  }

  //ELIMINAR DATOS RELACIONADOS CUANDO SE ELIMINE EL PROFESOR
  static function eliminar($id_profesor)
  {
    $sql = 'DELETE u, mp FROM usuarios u LEFT JOIN materias_profesores mp ON mp.id_profesor = u.id WHERE u.id = :id AND u.rol = "profesor"';
    return (parent::query($sql, ['id' => $id_profesor])) ? true : false;
  }
}