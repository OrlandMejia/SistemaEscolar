<?php 

class usuarioModel extends Model
{
  static $t1 = 'usuarios';

  static function all(){
    // Todos los registros
    $sql = 'SELECT * FROM usuarios ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }
  static function all_paginated(){
    // Todos los registros
    $sql = 'SELECT * FROM usuarios WHERE id = :id ORDER BY id ASC';
    return PaginationHandler::paginate($sql);
  }

public static function by_email($email){
  $sql = 'SELECT * FROM usuarios WHERE email = :email LIMIT 1';
  return ($rows = parent::query($sql, ['email' => $email])) ? $rows[0] : [];
}

public static function by_id($id)
{
  $sql = 'SELECT * FROM usuarios WHERE id = :id LIMIT 1';

  return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
}

}