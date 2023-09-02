<?php 

class usuarioModel extends Model
{
  //agregamos este estatico para poder acceder de manera rapido a la tabla general que utiliza el modelo
  
  static $t1 = 'usuarios';
  //metodo para validar al usuario por su email
public static function by_email($email){
  $sql = 'SELECT * FROM usuarios WHERE email = :email LIMIT 1';
  return ($rows = parent::query($sql, ['email' => $email])) ? $rows[0] : [];
}

//metodo para validar el ID del usuario
public static function by_id($id){
  $sql = 'SELECT * FROM usuarios WHERE id = :id LIMIT 1';
  return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
}
}