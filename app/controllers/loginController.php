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
  
      // Data pasada del formulario
      $email  = clean($_POST['email']);
      $password = clean($_POST['password']);
  
      //VERIFICAR SI EL EMAIL ES VALIDO
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        throw new Exception("No se encontró el correo electronico", 1);
        
      }

      //verificar que exista el usuario con el metodo
      if(!$user = usuarioModel::by_email($email)){
        throw new Exception("correo electronico incorrecto", 1);
      }


          // Información del usuario loggeado, simplemente se puede reemplazar aquí con un query a la base de datos
    // para cargar la información del usuario si es existente
    if (!password_verify($password.AUTH_SALT, $user['password'])) {
      throw new Exception("La contraseña no es correcta", 1);
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
}