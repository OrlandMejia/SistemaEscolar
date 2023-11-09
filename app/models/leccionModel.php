<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de leccion
 */
class leccionModel extends Model {
  public static $t1   = 'lecciones'; // Nombre de la tabla en la base de datos;
  
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
    $sql = 'SELECT * FROM lecciones ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    $sql = 
    'SELECT 
    l.*, 
    u.nombre_completo AS profesor, 
    m.nombre AS materia 
    FROM lecciones l
    LEFT JOIN usuarios u ON u.id = l.id_profesor
    LEFT JOIN materias m ON m.id = l.id_materia
    ORDER BY l.id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 
    'SELECT 
    l.* ,
    p.nombre_completo AS profesor,
    m.nombre AS materia
    FROM lecciones l
    LEFT JOIN usuarios p ON p.id = l.id_profesor AND p.rol = "profesor"
    LEFT JOIN materias m ON m.id = l.id_materia
    WHERE l.id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  static function by_materia_profesor($id_materia, $id_profesor)
  {
    $sql = 
    'SELECT
      l.*, 
      m.nombre AS materia,
      u.nombre_completo AS profesor
    FROM
      lecciones l
    JOIN materias_profesores mp ON mp.id_materia = l.id_materia AND mp.id_profesor = l.id_profesor
    LEFT JOIN materias m ON m.id = mp.id_materia
    LEFT JOIN usuarios u ON u.id = mp.id_profesor AND u.rol = "profesor"
    WHERE
      l.id_materia = :id_materia AND l.id_profesor = :id_profesor';

    return PaginationHandler::paginate($sql, ['id_materia' => $id_materia, 'id_profesor' => $id_profesor]);
  }

  static function by_materia($id_materia)
  {
    $sql = 
    'SELECT
      l.*, 
      m.nombre AS materia,
      u.nombre_completo AS profesor
    FROM
      lecciones l
    JOIN materias_profesores mp ON mp.id_materia = l.id_materia AND mp.id_profesor = l.id_profesor
    LEFT JOIN materias m ON m.id = mp.id_materia
    LEFT JOIN usuarios u ON u.id = mp.id_profesor AND u.rol = "profesor"
    WHERE
      l.id_materia = :id_materia';

    return PaginationHandler::paginate($sql, ['id_materia' => $id_materia]);
  }

  static function by_alumno($id_alumno, $publicadas = true, $id_materia = null, $id_profesor = null)
  {
    if ($publicadas === true) {
      $sql = 
      'SELECT
        l.*, 
        m.nombre AS materia,
        u.nombre_completo AS profesor
      FROM
        lecciones l
      JOIN materias_profesores mp ON mp.id_materia = l.id_materia AND mp.id_profesor = l.id_profesor
      LEFT JOIN materias m ON m.id = mp.id_materia
      LEFT JOIN usuarios u ON u.id = mp.id_profesor AND u.rol = "profesor"
      LEFT JOIN grupos_materias gm ON gm.id_mp = mp.id
      LEFT JOIN grupos g ON g.id = gm.id_grupo
      JOIN grupos_alumnos ga ON ga.id_grupo = g.id
      WHERE
      ga.id_alumno = :id_alumno AND l.status IN("publica") '.($id_materia === null || $id_profesor === null ? '' : 'AND l.id_materia = :id_materia AND l.id_profesor = :id_profesor').'
      ORDER BY m.id DESC, l.fecha_inicial DESC';

      $data =
      [
        'id_alumno' => $id_alumno
      ];

      if ($id_materia !== null && $id_profesor !== null) {
        $data['id_materia'] = $id_materia;
        $data['id_profesor'] = $id_profesor;
      }
  
      return PaginationHandler::paginate($sql, $data);
    }

    // Todas las lecciones sin importar su status
    $sql = 
    'SELECT
      l.*, 
      m.nombre AS materia,
      u.nombre_completo AS profesor
    FROM
      lecciones l
    JOIN materias_profesores mp ON mp.id_materia = l.id_materia AND mp.id_profesor = l.id_profesor
    LEFT JOIN materias m ON m.id = mp.id_materia
    LEFT JOIN usuarios u ON u.id = mp.id_profesor AND u.rol = "profesor"
    LEFT JOIN grupos_materias gm ON gm.id_mp = mp.id
    LEFT JOIN grupos g ON g.id = gm.id_grupo
    JOIN grupos_alumnos ga ON ga.id_grupo = g.id
    WHERE
    ga.id_alumno = :id_alumno '.($id_materia === null || $id_profesor === null ? '' : 'AND l.id_materia = :id_materia AND l.id_profesor = :id_profesor');

    $data =
    [
      'id_alumno' => $id_alumno
    ];

    if ($id_materia !== null && $id_profesor !== null) {
      $data['id_materia'] = $id_materia;
      $data['id_profesor'] = $id_profesor;
    }

    return PaginationHandler::paginate($sql, $data);
  }

  static function total_by_year($año = null)
  {
    $año = $año === null ? date('Y') : $año;
    $sql = 
"SELECT 
Months.mm AS mes, 
Months.m AS month, 
COUNT(l.id) AS total 
FROM 
(
  SELECT 1 as m, 'Enero' AS mm
  UNION SELECT 2 as m, 'Febrero' AS mm 
  UNION SELECT 3 as m, 'Marzo' AS mm 
  UNION SELECT 4 as m, 'Abril' AS mm 
  UNION SELECT 5 as m, 'Mayo' AS mm 
  UNION SELECT 6 as m, 'Junio' AS mm 
  UNION SELECT 7 as m, 'Julio' AS mm 
  UNION SELECT 8 as m, 'Agosto' AS mm 
  UNION SELECT 9 as m, 'Septiembre' AS mm 
  UNION SELECT 10 as m, 'Octubre' AS mm 
  UNION SELECT 11 as m, 'Noviembre' AS mm 
  UNION SELECT 12 as m, 'Diciembre' AS mm
) as Months
LEFT JOIN lecciones l ON Months.m = MONTH(l.creado) 
AND YEAR(l.creado) = :ano GROUP BY Months.m, Months.mm";

    return parent::query($sql, ['ano' => $año]);
  }
}