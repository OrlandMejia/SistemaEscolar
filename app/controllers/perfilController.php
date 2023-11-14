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

    function index() {
        $userData = $this->auth->user();
        $data = [
            'userData' => $userData,
        ];

        View::render('index', $data);
    }
    function editar() {
    }
    
    function guardar() {
        // ...
    }
}
