<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de notas
 */
class notasModel extends Model {
  public static $t1   = 'usuarios';
  public static $t2 = 'calificacion'; // Nombre de la tabla en la base de datos;
  
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

  static function by_id_notas($id_alumno)
  {
      // Un registro con $id
      $sql = '
      SELECT
          u.id AS id_usuario,
          u.numero,
          u.identificacion,
          CONCAT(u.nombres, " ", u.apellidos) AS nombre_completo,
          u.email,
          u.telefono,
          u.status,
          c.id_usuario, -- Cambié el nombre de la columna para evitar redundancia
          c.primer_bimestre,
          c.segundo_bimestre,
          c.tercer_bimestre,
          c.cuarto_bimestre,
          c.promedio
      FROM
          usuarios u
      LEFT JOIN
          calificacion c ON u.id = c.id_usuario
      WHERE
          u.id = :id_alumno AND u.rol = "alumno"';
  
      return parent::query($sql, ['id_alumno' => $id_alumno]);
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
          c.id,
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

  static function getAlumnosSinCalificacion($id_grupo) {
    // Obtener alumnos asignados al grupo pero sin calificación
    $sql = '
        SELECT
            u.id AS id_usuario,
            u.nombres,
            u.apellidos
        FROM
            usuarios u
        JOIN
            grupos_alumnos ga ON u.id = ga.id_alumno
        LEFT JOIN
            calificacion c ON u.id = c.id_usuario
        WHERE
            ga.id_grupo = :id_grupo AND u.rol = "alumno" AND c.id_usuario IS NULL';

    return parent::query($sql, ['id_grupo' => $id_grupo]);
}

// En tu controlador notasController.php

static function updateCalificacion($id_alumno, $calificacion_data)
{
    try {
        $sql = 'UPDATE calificacion
                SET primer_bimestre = :primer_bimestre,
                    segundo_bimestre = :segundo_bimestre,
                    tercer_bimestre = :tercer_bimestre,
                    cuarto_bimestre = :cuarto_bimestre
                WHERE id_usuario = :id_usuario';

        $params = [
            'primer_bimestre' => $calificacion_data['primer_bimestre'],
            'segundo_bimestre' => $calificacion_data['segundo_bimestre'],
            'tercer_bimestre' => $calificacion_data['tercer_bimestre'],
            'cuarto_bimestre' => $calificacion_data['cuarto_bimestre'],
            'id_usuario' => $id_alumno,
        ];

        return parent::query($sql, $params);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}

}