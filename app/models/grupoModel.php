<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de grupo
 */
class grupoModel extends Model {
  public static $t1 = 'grupos'; // Nombre de la tabla en la base de datos;
  public static $t2 = 'grupos_materias';
  public static $t3 = 'grupos_alumnos';
  
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
    $sql = 'SELECT * FROM grupos ORDER BY id ASC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    // Todos los registros
    $sql = 'SELECT * FROM grupos ORDER BY id ASC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM grupos WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  static function materias_disponibles($id)
  {
    $sql = 
    'SELECT
      mp.id,
      m.nombre AS materia,
      u.nombre_completo AS profesor
    FROM
      materias_profesores mp
    LEFT JOIN materias m ON m.id = mp.id_materia
    LEFT JOIN usuarios u ON u.id = mp.id_profesor
    WHERE
      mp.id NOT IN (
        SELECT
          gm.id_mp
        FROM
          grupos_materias gm
        WHERE
          gm.id_grupo = :id_grupo
      )';

    return ($rows = parent::query($sql, ['id_grupo' => $id])) ? $rows : [];
  }

  static function materias_asignadas($id, $id_profesor = null)
  {
    // Cargar las materias del grupo sin importar el profesor
    if ($id_profesor === null) {
      $sql = 
      'SELECT
        mp.id,
        m.id AS id_materia,
        m.nombre AS materia,
        u.id AS id_profesor,
        u.numero AS num_profesor,
        u.nombre_completo AS profesor
      FROM
        materias_profesores mp
      LEFT JOIN materias m ON m.id = mp.id_materia
      LEFT JOIN usuarios u ON u.id = mp.id_profesor
      WHERE
        mp.id IN (
          SELECT
            gm.id_mp
          FROM
            grupos_materias gm
          WHERE
            gm.id_grupo = :id_grupo
        )';
  
      return ($rows = parent::query($sql, ['id_grupo' => $id])) ? $rows : [];
    }

    $sql = 
    'SELECT
      mp.id,
      m.id AS id_materia,
      m.nombre AS materia,
      u.id AS id_profesor,
      u.numero AS num_profesor,
      u.nombre_completo AS profesor
    FROM
      materias_profesores mp
    LEFT JOIN materias m ON m.id = mp.id_materia
    LEFT JOIN usuarios u ON u.id = mp.id_profesor
    WHERE
      mp.id IN (
        SELECT
          gm.id_mp
        FROM
          grupos_materias gm
        WHERE
          gm.id_grupo = :id_grupo
      ) AND mp.id_profesor = :id_profesor';

    return ($rows = parent::query($sql, ['id_grupo' => $id, 'id_profesor' => $id_profesor])) ? $rows : [];
  }

  static function asignar_materia($id_grupo, $id_mp)
  {
    $data =
    [
      'id_grupo' => $id_grupo,
      'id_mp'    => $id_mp
    ];

    if (!$id = self::add(self::$t2, $data)) return false;

    return $id;
  }

  static function quitar_materia($id_grupo, $id_mp)
  {
    $data =
    [
      'id_grupo' => $id_grupo,
      'id_mp'    => $id_mp
    ];

    return (self::remove(self::$t2, $data)) ? true : false;
  }

  static function alumnos_asignados($id_grupo)
  {
    $sql = 
    'SELECT
      u.*
    FROM
      usuarios u
    JOIN grupos_alumnos ga ON u.id = ga.id_alumno
    JOIN grupos g ON g.id = ga.id_grupo
    WHERE
      g.id = :id
    AND u.rol = "alumno"';

    return ($rows = parent::query($sql, ['id' => $id_grupo])) ? $rows : [];
  }

  static function quitar_alumno($id_grupo, $id_alumno)
  {
    $data =
    [
      'id_grupo' => $id_grupo,
      'id_alumno' => $id_alumno
    ];

    return (self::remove(self::$t3, $data)) ? true : false;
  }

  static function eliminar($id_grupo)
  {
    $sql = 
    'DELETE g, gm, ga 
    FROM grupos g 
    LEFT JOIN grupos_materias gm ON g.id = gm.id_grupo
    LEFT JOIN grupos_alumnos ga ON g.id = ga.id_grupo 
    WHERE g.id = :id';
    return parent::query($sql, ['id' => $id_grupo]) ? true : false;
  }

  static function by_alumno($id_alumno)
  {
    $sql = 
    'SELECT
      g.*
    FROM
      grupos g
    JOIN grupos_alumnos ga ON ga.id_grupo = g.id
    JOIN usuarios u ON u.id = ga.id_alumno
    WHERE
      u.id = :id
    AND u.rol = "alumno"';

    $grupo = [];
    $rows  = parent::query($sql, ['id' => $id_alumno]);

    if (!$rows) return $grupo;

    // Cargando materias
    $grupo = $rows[0];
    $grupo['materias'] = grupoModel::materias_asignadas($grupo['id']);
    
    // Cargando compañeros
    $grupo['alumnos']  = grupoModel::alumnos_asignados($grupo['id']);

    return $grupo;
  }
}

