<?php

class perfilController extends Controller {
    private $auth;

    function __construct() {
        $this->auth = new Auth();

        if (!$this->auth->validate()) {
            Flasher::new('Debes iniciar sesión primero.', 'danger');
            Redirect::to('login');
        }
    }

    public function index() {
        // Obtener los datos del usuario desde la sesión
        $userData = $_SESSION['user_session']['user'];

        $data = [
        'title' => 'PERFIL',
        'ud'=> $userData
        ];
        // Pasar los datos a la vista
        View::render('index', ['userData' => $userData]);
        //echo debug($userData);
    }
    function editar() {
    }
    
    function guardar() {
        // ...
    }
}
