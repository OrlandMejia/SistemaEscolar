<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de notas
 */
class notasModel extends Model {
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

  static function all_paginated()
  {
    // Todos los registros
    $sql = 'SELECT * FROM usuarios WHERE rol = "alumno" ORDER BY id ASC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM usuarios WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  static function getAlumnosCalificaciones($id_grupo)
  {
      // Obtener alumnos y calificaciones para el grupo dado
      $sql = '
        SELECT
          u.id AS id_usuario,
          u.numero,
          u.identificacion, -- Agrega esta línea para incluir la propiedad "identificacion"
          CONCAT(u.nombres, " ", u.apellidos) AS nombre_completo, -- Modifica esta línea para incluir "nombre_completo"
          u.email,
          u.telefono,
          u.status,
          c.id_calificacion,
          c.primer_bimestre,
          c.segundo_bimestre,
          c.tercer_bimestre,
          c.cuarto_bimestre,
          c.promedio
        FROM
          usuarios u
        JOIN
          calificacion c ON u.id = c.id_usuario
        JOIN
          grupos_alumnos ga ON u.id = ga.id_alumno
        WHERE
          ga.id_grupo = :id_grupo AND u.rol = "alumno"';
  
      return parent::query($sql, ['id_grupo' => $id_grupo]);
  }
}

