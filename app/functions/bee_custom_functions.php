<?php 

//para devolver el rol del usuario logeado, y en caso de que esté vacio devuelve un valor

/**
 * @return mixed
 */
function get_user_rol(){
  return $rol = get_user('rol');
}

//RETORNA LOS LOS ROLES POR DEFECTOS QUE TIENEN SUPER PRIVILEGIOS
function get_default_roles(){
  return ['root', 'admin'];
}

//verifica el que usuario que este logueado sea super admin para ingresar al dashboard con ROOT
function is_root($rol){
  return in_array($rol, ['root']);
}

//EN PROFESOR Y ALMUMNO VALIDAMOS SI ES PROFESOR PUEDE ENTRAR A LAS FUNCIONES DE ESTE ROL, PERO TAMBIEN
//SI ES UN SUPER ADMIN TAMBIEN TIENE ACCESO A ESOS RECURSOS
function is_profesor($rol){
  return in_array($rol, ['profesor', 'admin', 'root']);
}

//NIVEL ADMINISTRADOR
function is_admin($rol) {
  return in_array($rol, ['admin', 'root']);
}

function is_alumno($rol){
  return in_array($rol, ['estudiante', 'admin', 'root']);
}

//funcion para la accion o acceso solamente para roles aceptados
function is_user($rol, $roles_aceptados){
  $default = get_default_roles();

  //validaciones
  if(is_array($roles_aceptados)){
    array_push($default, $roles_aceptados);
    return in_array($rol, $default);
  }
  return in_array($rol, array_merge($default, $roles_aceptados));
}

//CREACION DE FUNCION DE EXCEPCIONES Y MENSAJES
/**
 * 0 Accesso no autorizado
 * 1 Acción no autorizada
 * REGRESA UN STRING
 */
function get_notificaciones($index = 0){
  //creamos objeto que contenga las notificaciones
  $notificaciones = 
  [
    'Acceso no autorizado.',
    'Acción no autorizada.',
    'Hubo un error al agregar el registro.',
    'Hubo un error al actualizar el registro.',
    'Hubo un error al borrar el registro.'
  ];
  //retorta y setea que lo que nosotros coloquemos en el llamado se encuentre dentro del array, sino devolverá por defecto el contenido del index 0
  return isset($notificaciones[$index]) ? $notificaciones[$index] : $notificaciones[0];

}

//funcion que regrese el status de cada usuario
//retorna un estado en un objeto, primero es lo que guardamos en la DB el segundo es una representación visual
function get_estados_usuarios(){
  return[
    ['pendiente', 'Pendiente de activación'],
    ['activo', 'Activo'],
    ['suspendido', 'Suspendido']
  ];
}

//FUNCION PARA FORMATEAR DE MANERA VISUAL EL STATUS
function format_estado_usuario($status){
  $placeholder = '<div class="badge %s"><i class= "%s"></i>%s</div>';
  $classes = '';
  $icon = '';
  $text = '';

  switch ($status) {
    case 'pendiente':
      # code...
      $classes = 'badge-warning text-dark';
      $icon = 'fas fa-clock';
      $text = 'Pendiente';
      break;

      case 'activo':
        # code...
        $classes = 'badge-success';
        $icon = 'fas fa-check';
        $text = 'Activo';
        break;

        case 'suspendido':
          # code...
          $classes = 'badge-danger';
          $icon = 'fas fa-times';
          $text = 'Suspendido';
          break;
    //colocamos un estado de protección
    default:
      # code...
      $classes = 'badge-danger';
      $icon = 'fas fa-question';
      $text = 'Desconocido';
      break;
  }
  return sprintf($placeholder, $classes, $icon, $text);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function mail_confirmar_cuenta($id_usuario) {
  // Se verifica que el usuario exista
  $usuario = usuarioModel::by_id($id_usuario);
  if (!$usuario) {
      return false;
  }

  // Se obtienen los datos del usuario
  $nombre = $usuario['nombres'];
  $hash = $usuario['hash'];
  $email = $usuario['email'];
  $status = $usuario['status'];

  // Si el estado del usuario no es "pendiente", no es necesario confirmar
  if ($status !== 'pendiente') {
      return false;
  }

  // Configuración de PHPMailer

  require 'vendor/autoload.php'; // Ruta al archivo autoload.php de Composer

  $mail = new PHPMailer(true);

  try {
      // Configuración del servidor SMTP
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com'; // Reemplaza con tu servidor SMTP
      $mail->SMTPAuth = true;
      $mail->Username = 'colegiojuda34@gmail.com'; // Reemplaza con tu correo electrónico
      $mail->Password = 'Unodostres123'; // Reemplaza con tu contraseña
      $mail->SMTPSecure = 'tls'; // Puedes cambiar a 'ssl' si es necesario
      $mail->Port = 587; // Puerto SMTP
      
      // Configuración del correo
      $mail->setFrom('tu_correo', 'Tu Nombre');
      $mail->addAddress($email, $nombre);
      $mail->Subject = sprintf('Confirma tu correo electrónico por favor, %s', $nombre);
      $mail->AltBody = sprintf('Debes confirmar tu correo electrónico para poder ingresar a %s.', get_sitename());
      $url = buildURL(URL.'login/activate', ['email' => $email, 'hash' => $hash], false, false);
      $text = '¡Hola %s! <br>Para ingresar al Sistema de <b>%s</b>, primero debes confirmar tu dirección de correo electrónico haciendo clic en el siguiente enlace seguro: <a href="%s">%s</a>';
      $mail->Body = sprintf($text, $nombre, get_sitename(), $url, $url);

      // Envío del correo electrónico
      $mail->send();

      return true;
  } catch (Exception $e) {
    error_log("Error al enviar correo: {$mail->ErrorInfo}");
    return false;
}

}


