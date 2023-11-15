<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-xl-6 col-md-6 col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#profesor_data" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="profesor_data">
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="profesor_data">
          <div class="card-body">
            <form action="profesores/post_editar" method="post">
              <?php echo insert_inputs(); ?>
              <input type="hidden" name="id_alumno" value="<?php echo $d->ud->id_usuario; ?>" required>

              <div class="form-group">
                <label for="dpi"><i class="fas fa-id-card"></i> DPI</label>
                <input type="text" class="form-control" id="identificacion" name="identificacion" value="<?php echo $d->ud->identificacion; ?>" required>
              </div>

              <div class="form-group">
                <label for="nombres"><i class="fas fa-user"></i> Nombre(s)</label>
                <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo $d->p->nombres; ?>" required>
              </div>

              <div class="form-group">
                <label for="apellidos"><i class="fas fa-user"></i> Apellido(s)</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo $d->p->apellidos; ?>" required>
              </div>

              <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $d->p->email; ?>" required>
              </div>

              <div class="form-group">
                <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $d->p->telefono; ?>">
              </div>

              <button class="btn btn-success" type="submit">Guardar</button>
            </form>
          </div>
      </div>
    </div>
    </div>

</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>
