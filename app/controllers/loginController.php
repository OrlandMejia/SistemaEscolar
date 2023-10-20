<?php 

class loginController extends Controller {
  function __construct()
  {
    if (Auth::validate()) {
      Flasher::new('Ya hay una sesión abierta.');
      Redirect::to('home/flash');
    }
  }

  function index()
  {
    $data =
    [
      'title'   => 'Ingresar a tu cuenta',
      'padding' => '0px'
    ];

    View::render('index', $data);
  }

  function post_login()
  {
    try {
      //code...
      if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['email','csrf','password'], $_POST)) {
        Flasher::new('Acceso no autorizado.', 'danger');
        Redirect::back();
      }

       // Verificar reCAPTCHA
       /**$recaptcha_secret = '6LeyOkgoAAAAAKxWQQUAuvZ9yqEFOOnSM-H6OzwC'; // Reemplaza con tu clave secreta de reCAPTCHA
       $recaptcha_response = $_POST['g-recaptcha-response'];

       $url = 'https://www.google.com/recaptcha/api/siteverify';
       $data = [
           'secret' => $recaptcha_secret,
           'response' => $recaptcha_response,
       ];

       $options = [
           'http' => [
               'header' => 'Content-type: application/x-www-form-urlencoded',
               'method' => 'POST',
               'content' => http_build_query($data),
           ],
       ];

       $context = stream_context_create($options);
       $result = file_get_contents($url, false, $context);
       $response = json_decode($result, true);

       if (!$response['success']) {
        Flasher::new('Verifica la reCAPTCHA', 'danger');
        Redirect::back();
       }**/
  
      // Data pasada del formulario
      $email  = clean($_POST['email']);
      $password = clean($_POST['password']);
  
//VERIFICAR SI EL EMAIL ES VALIDO
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  Flasher::new("No se encontró el correo electrónico", "danger");
  Redirect::back();
}

// verificar que exista el usuario con el método
if (!$user = usuarioModel::by_email($email)) {
  Flasher::new("Correo electrónico incorrecto", "danger");
  Redirect::back();
}

// Información del usuario loggeado, simplemente se puede reemplazar aquí con un query a la base de datos
// para cargar la información del usuario si es existente
if (!password_verify($password . AUTH_SALT, $user['password'])) {
  Flasher::new("La contraseña no es correcta", "danger");
  Redirect::back();
}

    //VALIDAR EL STATUS DEL USUARIO
    if ($user['status'] === 'pendiente') {
      mail_confirmar_cuenta($user['id']);
      Flasher::new("Porfavor Verifica tu Correo Electronico", "warning");
      Redirect::back();
  }
  

    // Loggear al usuario
    Auth::login($user['id'], $user);
    Redirect::to('dashboard');
  } catch (Exception $e) {
      Flasher::new($e->getMessage(). 'danger');
    }catch (PDOException $ex){
      Flasher::new($ex->getMessage(). 'danger');
    }
  }


function activate()
{
  try {
    if (!check_get_data(['email','hash'], $_GET)) {
      throw new Exception('El enlace de activación no es válido.');
    }

    // Data pasada en URL
    $email    = clean($_GET["email"]);
    $hash     = clean($_GET["hash"]);

    // Verificar que exista el usuario con ese email
    if (!$user = usuarioModel::by_email($email)) {
      throw new Exception('El enlace de activación no es válido.');
    }

    $id      = $user['id'];
    $nombre  = $user['nombres'];
    $status  = $user['status'];
    $db_hash = $user['hash'];

    // Verificar el hash del usuario y el status
    if ($hash !== $db_hash) {
      throw new Exception('El enlace de activación no es válido.');
    }

    // Validar el status del usuario
    if ($status !== 'pendiente') {
      throw new Exception('El enlace de activación no es válido.');
    }

    // Activar cuenta
    if (usuarioModel::update(usuarioModel::$t1, ['id' => $id], ['status' => 'activo']) === false) {
      throw new Exception(get_notificaciones(3));
    }

    Flasher::new(sprintf('Tu correo electrónico ha sido activado con éxito <b>%s</b>, ya puedes iniciar sesión.', $nombre), 'success');
    Redirect::to('login');

  } catch (Exception $e) {
    Flasher::new($e->getMessage(), 'danger');
    Redirect::to('login');
  } catch (PDOException $e) {
    Flasher::new($e->getMessage(), 'danger');
    Redirect::to('login');
  }
}
}
