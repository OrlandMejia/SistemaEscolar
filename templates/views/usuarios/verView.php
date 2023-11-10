<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#usuario_data" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="usuarios_data">
          <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="alumno_data">
          <div class="card-body">
            <form action="usuarios/post_editar" method="post">
              <?php echo insert_inputs(); ?>
              <input type="hidden" name="id" value="<?php echo $d->p->id; ?>" required>
              
              <div class="form-row">
                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="identificacion"><i class="fas fa-id-card"></i> Identificación</label>
                    <input type="text" class="form-control" style="width: 100%;" id="identificacion" name="identificacion" value="<?php echo $d->p->identificacion?>" required>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="nombres"><i class="fas fa-user"></i> Nombre(s)</label>
                    <input type="text" class="form-control" style="width: 100%;" id="nombres" name="nombres" value="<?php echo $d->p->nombres; ?>" required>
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="apellidos"><i class="fas fa-user"></i> Apellido(s)</label>
                    <input type="text" class="form-control" style="width: 100%;" id="apellidos" name "apellidos" value="<?php echo $d->p->apellidos; ?>" required>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" class="form-control" style="width: 100%;" id="email" name="email" value="<?php echo $d->p->email; ?>" required>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="input-icon">
                  <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                  <input type="text" class="form-control" style="width: 50%;" id="telefono" name="telefono" value="<?php echo $d->p->telefono; ?>">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" class="form-control" style="width: 100%;" id="password" name="password">
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="conf_password"><i class="fas fa-lock"></i> Confirmar contraseña</label>
                    <input type="password" class="form-control" style="width: 100%;" id="conf_password" name="conf_password">
                  </div>
                </div>
              </div>
              <hr>

              <button class="btn btn-success" type="submit">Guardar cambios</button>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>
