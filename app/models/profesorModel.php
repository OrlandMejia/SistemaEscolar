<?php
class profesorModel extends Model {
  public static $t1   = 'usuarios'; // Nombre de la tabla en la base de datos;
  function __construct(){


  }

  static function all(){
    // Todos los registros
    $sql = 'SELECT * FROM usuarios WHERE rol = "profesor" ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  
  }
  //FUNCION QUE EXTRAE TODOS LOS REGISTRO YA PAGINADOS
  static function all_paginated(){
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
  //METODO PARA MOSTRAR ESTADISTICAS EN DASHBOARD
  static function stats_by_id($id_profesor)
  {
    $materias  = 0;
    $grupos    = 0;
    $alumnos   = 0;
    $lecciones = 0;
    //CARGAR LAS MATERIAS Y TOTAL ASIGNADOS A UN PROFESOR
    $sql = 
    'SELECT
      COUNT(DISTINCT m.id) AS total
    FROM
      materias m
    JOIN materias_profesores mp ON mp.id_materia = m.id
    WHERE
      mp.id_profesor = :id';
    $materias = parent::query($sql, ['id' => $id_profesor])[0]['total'];
    //QUERY PARA CARGAR LOS DATOS TOTAL DE LOS GRADOS QUE TENGA UN PROFESOR
    $sql = 
    'SELECT 
      COUNT(DISTINCT g.id) AS total
    FROM
      grupos g
    JOIN grupos_materias gm ON gm.id_grupo = g.id
    JOIN materias_profesores mp ON mp.id = gm.id_mp
    WHERE mp.id_profesor = :id';
    $grupos = parent::query($sql, ['id' => $id_profesor])[0]['total'];
    //CONSULTA PARA UNIR Y REQUERIR LOS DATOS DE ALUMNOS
    $sql = 
    'SELECT
      COUNT(DISTINCT a.id) AS total
    FROM usuarios a
    JOIN grupos_alumnos ga ON ga.id_alumno = a.id
    JOIN grupos g ON g.id = ga.id_grupo
    JOIN grupos_materias gm ON gm.id_grupo = g.id
    JOIN materias_profesores mp ON mp.id = gm.id_mp
    WHERE mp.id_profesor = :id';
    $alumnos = parent::query($sql, ['id' => $id_profesor])[0]['total'];
    //CONSULTA PARA TENER EL TOTAL DE LECCIONES O TAREAS DE UN PROFESOR
    $sql = 'SELECT COUNT(l.id) AS total FROM lecciones l WHERE l.id_profesor = :id';
    $lecciones = parent::query($sql, ['id' => $id_profesor])[0]['total'];
    //RETORNAMOS UN ARRAY DE INFORMACIÓN
    return[
      'materias'  => $materias,
      'grupos'    => $grupos,
      'alumnos'   => $alumnos,
      'lecciones' => $lecciones];
    }
  static function grupos_asignados($id_profesor)
  {
    $sql = 
    'SELECT DISTINCT g.*
    FROM
      grupos g
    JOIN grupos_materias gm ON gm.id_grupo = g.id
    JOIN materias_profesores mp ON mp.id = gm.id_mp
    WHERE mp.id_profesor = :id';
    return PaginationHandler::paginate($sql, ['id' => $id_profesor]);
  }
  static function asignado_a_grupo($id_profesor, $id_grupo)
  {
    $sql = 
    'SELECT DISTINCT g.*
    FROM
      grupos g
    JOIN grupos_materias gm ON gm.id_grupo = g.id
    JOIN materias_profesores mp ON mp.id = gm.id_mp
    WHERE mp.id_profesor = :id_profesor AND g.id = :id_grupo';
    return parent::query($sql, ['id_profesor' => $id_profesor, 'id_grupo' => $id_grupo]) ? true : false;
  }
}