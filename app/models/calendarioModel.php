<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.1
 *
 * Modelo de calendario
 */
class calendarioModel extends Model {
  public static $t1   = 'calendario'; // Nombre de la tabla en la base de datos;
  
  // Nombre de tabla 2 que talvez tenga conexión con registros
  //public static $t2 = '__tabla 2___'; 
  //public static $t3 = '__tabla 3___'; 

  function __construct()
  {
    // Constructor general
  }
  
  public function registrar($title, $inicio, $fin, $color)
  {
      $sql = "INSERT INTO evento (title, start, fin, color) VALUES (?,?,?,?)";
      $array = array($title, $inicio, $fin, $color);
      $data = $this->save($sql, $array);
      if ($data == 1) {
          $res = 'ok';
      }else{
          $res = 'error';
      }
      return $res;
  }
  public function getEventos()
  {
      $sql = "SELECT * FROM evento";
      return $this->selectAll($sql);
  }
  public function modificar($title, $inicio, $fin, $color, $id)
  {
      $sql = "UPDATE evento SET title=?, start=?, color=? WHERE id=?";
      $array = array($title, $inicio, $fin, $color, $id);
      $data = $this->save($sql, $array);
      if ($data == 1) {
          $res = 'ok';
      } else {
          $res = 'error';
      }
      return $res;
  }
  public function eliminar($id)
  {
      $sql = "DELETE FROM evento WHERE id=?";
      $array = array($id);
      $data = $this->save($sql, $array);
      if ($data == 1) {
          $res = 'ok';
      } else {
          $res = 'error';
      }
      return $res;
  }
  public function dragOver($start, $fin, $id)
  {
      $sql = "UPDATE evento SET start=? WHERE id=?";
      $array = array($start, $fin, $id);
      $data = $this->save($sql, $array);
      if ($data == 1) {
          $res = 'ok';
      } else {
          $res = 'error';
      }
      return $res;
  }
}

