<?php 

class usuarioModel extends Model
{
public static function by_email($email){
  $sql = 'SELECT * FROM usuarios WHERE email = :email LIMIT 1';
  return ($rows = parent::query($sql, ['email' => $email])) ? $rows[0] : [];
}
}