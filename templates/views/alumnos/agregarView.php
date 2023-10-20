<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-xl-6 col-md-6 col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#profesor_data" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="profesor_data">
          <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="profesor_data">
          <div class="card-body">
            <form action="alumnos/post_agregar" method="post">
              <?php echo insert_inputs(); ?>
              
              <div class="form-group">
                <label for="nombres">Nombre(s)</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
              </div>

              <div class="form-group">
                <label for="apellidos">Apellido(s)</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
              </div>

              <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>

              <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono">
              </div>

              <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password">
              </div>

              <div class="form-group">
                <label for="conf_password">Confirmar contraseña</label>
                <input type="password" class="form-control" id="conf_password" name="conf_password">
              </div>

              <hr>

              <?php if (!empty($d->grupos)): ?>
                <div class="form-group">
                  <label for="id_grupo">Grado</label>
                  <select name="id_grupo" id="id_grupo" class="form-control">
                    <?php foreach ($d->grupos as $g): ?>
                      <?php echo sprintf('<option value="%s">%s</option>', $g->id, $g->nombre); ?>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php else: ?>
                <div class="form-group">
                  <label for="id_grupo">Grupo</label>
                  <div class="alert alert-danger">No hay Grados Registrados.</div>
                </div>
              <?php endif; ?>

              <button class="btn btn-success" type="submit" <?php echo empty($d->grupos) ? 'disabled' : null; ?>>Agregar alumno</button>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>