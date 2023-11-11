<?php

//////////////////////////////// 2021
//////////////////////////////// Joystick
//////////////////////////////// Bee-Framework

// Definir el uso horario o timezone del sistema
date_default_timezone_set('America/Mexico_City');

define('PREPROS'     , false); // Activar en caso de trabajar el desarrollo en prepros como servidor local
define('PORT'       , '8848'); // Puerto por defecto de Prepros < 2020

// Lenguaje
define('SITE_LANG'   , $this->lng);

// Versión de la aplicación
define('BEE_NAME'    , $this->framework); // Viene desde Bee.php
define('BEE_VERSION' , $this->version);   // Viene desde Bee.php
define('SITE_NAME'   , 'Sistema Juda');    // Nombre del sitio
define('SITE_VERSION', '1.0.0');          // Versión del sitio

// Puerto y la URL del sitio
define('PROTOCOL'   , isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"); // Detectar si está en HTTPS o HTTP
define('HOST'       , $_SERVER['HTTP_HOST'] === 'localhost' ? (PREPROS ? 'localhost:'.PORT : 'localhost') : $_SERVER['HTTP_HOST']); // Dominio o host localhost.com tudominio.com
define('REQUEST_URI', $_SERVER["REQUEST_URI"]); // Parámetros y ruta requerida
define('URL'        , PROTOCOL.'://'.HOST.BASEPATH); // URL del sitio
define('CUR_PAGE'   , PROTOCOL.'://'.HOST.REQUEST_URI); // URL actual incluyendo parámetros get

// Las rutas de directorios y archivos
define('DS'         , DIRECTORY_SEPARATOR);
define('ROOT'       , getcwd().'/');

define('APP'        , ROOT.'app'.DS);
define('CLASSES'    , APP.'classes'.DS);
define('CONFIG'     , APP.'config'.DS);
define('CONTROLLERS', APP.'controllers'.DS);
define('FUNCTIONS'  , APP.'functions'.DS);
define('MODELS'     , APP.'models'.DS);
define('LOGS'       , APP.'logs'.DS);

define('TEMPLATES'  , ROOT.'templates'.DS);
define('INCLUDES'   , TEMPLATES.'includes'.DS);
define('MODULES'    , TEMPLATES.'modules'.DS);
define('VIEWS'      , TEMPLATES.'views'.DS);

// Rutas de recursos y assets absolutos
define('IMAGES_PATH', ROOT.'/img/');

// Rutas de archivos o assets con base URL
define('ASSETS'     , URL.'assets/');
define('CSS'        , ASSETS.'css/');
define('FAVICON'    , ASSETS.'favicon/');
define('FONTS'      , ASSETS.'fonts/');
define('IMAGES'     , 'img/');
define('JS'         , ASSETS.'js/');
define('PLUGINS'    , ASSETS.'plugins/');
define('UPLOADS'    , ROOT.'/uploads/');
define('UPLOADED'   , 'uploads/');

// Credenciales de la base de datos
// Set para conexión local o de desarrollo
define('LDB_ENGINE' , 'mysql');
define('LDB_HOST'   , 'localhost');
define('LDB_NAME'   , 'db_sistema_escolar');
define('LDB_USER'   , 'root');
define('LDB_PASS'   , '');
define('LDB_CHARSET', 'utf8');

// El controlador por defecto / el método por defecto / el controlador de errores por defecto
define('DEFAULT_CONTROLLER'      , 'dashboard');
define('DEFAULT_ERROR_CONTROLLER', 'error');
define('DEFAULT_METHOD'          , 'index');