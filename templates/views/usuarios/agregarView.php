<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#usuario_data" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="profesor_data">
          <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="profesor_data">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <form action="usuarios/post_agregar" method="post">
                  <?php echo insert_inputs(); ?>
                  
                  <div class="form-group">
                    <label for="identificacion"><i class="fas fa-address-card" style="vertical-align: top;"></i> Identificación</label>
                    <input type="text" class="form-control" style="width: 100%;" id="identificacion" name="identificacion" required>
                  </div>

                  <div class="form-group">
                    <label for="nombres"><i class="fas fa-user" style="vertical-align: top;"></i> Nombre(s)</label>
                    <input type="text" class="form-control" style="width: 100%;" id="nombres" name="nombres" required>
                  </div>

                  <div class="form-group">
                    <label for="apellidos"><i class="fas fa-user" style="vertical-align: top;"></i> Apellido(s)</label>
                    <input type="text" class="form-control" style="width: 100%;" id="apellidos" name="apellidos" required>
                  </div>

                  <div class="form-group">
                    <label for="email"><i class="fas fa-envelope" style="vertical-align: top;"></i> Correo electrónico</label>
                    <input type="email" class="form-control" style="width: 100%;" id="email" name="email" required>
                  </div>
                </form>
              </div>

              <div class="col-md-6">
                <form action="usuarios/post_agregar" method="post">
                  <?php echo insert_inputs(); ?>
                  
                  <div class="form-group">
                    <label for="telefono"><i class="fas fa-phone" style="vertical-align: top;"></i> Teléfono</label>
                    <input type="text" class="form-control" style="width: 100%;" id="telefono" name="telefono">
                  </div>

                  <div class="form-group">
                    <label for="password"><i class="fas fa-lock" style="vertical-align: top;"></i> Contraseña</label>
                    <input type="password" class="form-control" style="width: 100%;" id="password" name="password">
                  </div>

                  <div class="form-group">
                    <label for="conf_password"><i class="fas fa-lock" style="vertical-align: top;"></i> Confirmar contraseña</label>
                    <input type="password" class="form-control" style="width: 100%;" id="conf_password" name="conf_password">
                  </div>
                  <hr>
                  <button class="btn btn-success" type="submit">Agregar Administrador</button>
                </form>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<script>
function validatePassword(password) {
    const messageElement = document.getElementById("password-validation-message");
    const passwordElement = document.getElementById("password");

    const isLengthValid = password.length >= 8;
    const hasSpecialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password);
    const hasNumbers = /\d/.test(password);

    if (isLengthValid && hasSpecialChars && hasNumbers) {
        messageElement.textContent = "Contraseña válida";
        messageElement.style.color = "green";
    } else {
        messageElement.textContent = "La contraseña debe contener al menos 8 caracteres, caracteres especiales y números.";
        messageElement.style.color = "red";
    }
}

// Agregar un evento de escucha para validar la contraseña en tiempo real
const passwordElement = document.getElementById("password");
passwordElement.addEventListener("input", function () {
    validatePassword(this.value);
});
</script>

<?php require_once INCLUDES.'inc_footer.php'; ?>
