<?php require_once INCLUDES . 'inc_header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-6 offset-xl-3">
            <h2 class="mt-5 mb-3">Perfil de Usuario</h2>
            <!-- Mostrar mensajes flash si los hubiera -->
            <?php echo Flasher::flash(); ?>

            <form method="post" action="perfil/editar" enctype="multipart/form-data">
                <!-- Campos del formulario para editar el perfil -->
                <div class="mb-3">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $userData['nombre'] ?? ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="apellido">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $userData['apellido'] ?? ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $userData['email'] ?? ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $userData['telefono'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="foto_perfil">Foto de Perfil</label>
                    <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>

<?php require_once INCLUDES . 'inc_footer.php'; ?>
