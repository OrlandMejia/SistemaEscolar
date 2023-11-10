<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de perfil
 */
class perfilModel extends Model {
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
    $sql = 'SELECT * FROM usuarios ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM usuarios WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

public static function update_profile($id, $data) {
    // Actualiza el perfil del usuario en la base de datos
    $sql = 'UPDATE usuarios SET nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono WHERE id = :id';
    return parent::query($sql, array_merge(['id' => $id], $data));
}



}

