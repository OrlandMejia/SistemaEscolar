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
function is_admin($rol){
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


/**function mail_confirmar_cuenta($id_usuario)
{
    if (!$usuario = usuarioModel::by_id($id_usuario)) return false; // nuevo método creado en modelo

    $nombre = $usuario['nombres'];
    $hash   = $usuario['hash'];
    $email  = $usuario['email'];
    $status = $usuario['status'];

    // Si no es pendiente el status no requiere activación
    if ($status !== 'pendiente') return false;

    // Generar la URL de activación
    $url = buildURL(URL.'login/activate', ['email' => $email, 'hash' => $hash], false, false);

    // Configurar PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurar el servidor SMTP de Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls'; // Puedes usar 'ssl' si prefieres SSL
        $mail->Port = 465; // Puerto SMTP de Gmail

        // Configurar las credenciales de tu cuenta de Gmail
        $mail->Username = 'colegiocristianojuda@gmail.com'; // Tu dirección de Gmail
        $mail->Password = 'lwml nvjg vceg qtpf'; // La contraseña de aplicación generada en el paso anterior

        // Remitente y destinatario
        $mail->setFrom('colegiocristianojuda@gmail.com', 'Sistema Juda'); // Tu dirección de Gmail y nombre
        $mail->addAddress($email, $nombre); // La dirección de correo del destinatario y su nombre

        // Configurar el contenido del correo
        $mail->isHTML(true);
        $mail->Subject = sprintf('Confirma tu correo eletrónico por favor %s', $nombre);
        $mail->AltBody = sprintf('Debes confirmar tu correo electrónico para poder ingresar a %s.', get_sitename());
        $mail->Body = sprintf(
            '¡Hola %s!<br>Para ingresar al sistema de <b>%s</b> primero debes confirmar tu dirección de correo electrónico dando clic en el siguiente enlace seguro: <a href="%s">%s</a>',
            $nombre,
            get_sitename(),
            $url,
            $url
        );

        // Enviar el correo
        $mail->send();

        return true;
    } catch (Exception $e) {
        // Manejar cualquier error que pueda ocurrir al enviar el correo
        return false;
    }
}**/
