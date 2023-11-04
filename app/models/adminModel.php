<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de admin
 */
class adminModel extends Model {
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
    $sql = 'SELECT * FROM usuarios WHERE rol = "admin" ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM usuarios WHERE rol = "admin" AND id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  static function stats()
  {
    $materias   = 0;
    $grupos     = 0;
    $admins     = 0;
    $alumnos    = 0;
    $profesores = 0;
    $lecciones  = 0;
    $comunidad  = [];
    $ingresos   = []; // simulados con una función
    $ensenanza  = [];

    $sql        = 'SELECT COUNT(m.id) AS total FROM materias m';
    $materias   = parent::query($sql)[0]['total'];

    $sql        = 'SELECT COUNT(g.id) AS total FROM grupos g';
    $grupos     = parent::query($sql)[0]['total'];

    $sql        = 'SELECT COUNT(u.id) AS total FROM usuarios u WHERE u.rol IN("root","admin")';
    $admins     = parent::query($sql)[0]['total'];

    $sql        = 'SELECT COUNT(u.id) AS total FROM usuarios u WHERE u.rol = "alumno"';
    $alumnos    = parent::query($sql)[0]['total'];

    $sql        = 'SELECT COUNT(u.id) AS total FROM usuarios u WHERE u.rol = "profesor"';
    $profesores = parent::query($sql)[0]['total'];

    $sql        = 'SELECT COUNT(l.id) AS total FROM lecciones l';
    $lecciones  = parent::query($sql)[0]['total'];

    $sql        = 'SELECT u.rol, COUNT(u.id) AS total FROM usuarios u GROUP BY u.rol';
    $comunidad  = parent::query($sql);

    $ingresos   = get_ingresos();

    $ensenanza  = leccionModel::total_by_year();

    return
    [
      'materias'   => $materias,
      'grupos'     => $grupos,
      'admins'     => $admins,
      'alumnos'    => $alumnos,
      'profesores' => $profesores,
      'lecciones'  => $lecciones,
      'comunidad'  => $comunidad,
      'ingresos'   => $ingresos,
      'ensenanza'  => $ensenanza
    ];
  }
}

