<!DOCTYPE html>
<html lang="<?php echo SITE_LANG; ?>">
<head>
  <!-- Agregar basepath para definir a partir de donde se deben generar los enlaces y la carga de archivos -->
  <base href="<?php echo BASEPATH; ?>">

  <meta charset="UTF-8">
  
  <title><?php echo isset($d->title) ? $d->title.' - '.get_sitename() : 'Bienvenido - '.get_sitename(); ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <!-- Favicon del sitio -->
<link rel="icon" href="img/favicon.png" type="image/x-icon">

  
  <!-- inc_styles.php -->
  <?php require_once INCLUDES.'inc_styles.php'; ?>
</head>

<body style="background-image: url('img/LOGIN.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; z-index: -1;">

    <!-- Page Wrapper -->
    <div id="wrapper">