<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#usuario_data" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="usuarios_data">
          <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?>
            <div class="float-right">
              <?php echo format_estado_usuario($d->p->status); ?>
            </div>
          </h6>
          
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
                    <input type="text" class="form-control" style="width: 100%;" id="identificacion" name="identificacion" value="<?php echo $d->p->identificacion?>" disabled>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="nombres"><i class="fas fa-user"></i> Nombre(s)</label>
                    <input type="text" class="form-control" style="width: 100%;" id="nombres" name="nombres" value="<?php echo $d->p->nombres; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="apellidos"><i class="fas fa-user"></i> Apellido(s)</label>
                    <input type="text" class="form-control" style="width: 100%;" id="apellidos" name "apellidos" value="<?php echo $d->p->apellidos; ?>" disabled>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <div class="input-icon">
                    <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" class="form-control" style="width: 100%;" id="email" name="email" value="<?php echo $d->p->email; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="input-icon">
                  <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                  <input type="text" class="form-control" style="width: 50%;" id="telefono" name="telefono" value="<?php echo $d->p->telefono; ?>" disabled>
                </div>
              </div>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>
