<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <!-- info del grupo -->
  <div class="col-xl-4 col-md-6 col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#grupo_data" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="grupo_data">
          <h6 class="m-0 font-weight-bold text-primary"><?php echo sprintf('Grupo #%s', $d->g->numero); ?></h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="grupo_data">
          <div class="card-body">
            <form action="grupos/post_editar" method="post" enctype="multipart/form-data">
              <?php echo insert_inputs(); ?>
              <input type="hidden" name="id" value="<?php echo $d->g->id; ?>" required>
              
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $d->g->nombre; ?>" required>
              </div>

              <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" cols="10" rows="5" class="form-control"><?php echo $d->g->descripcion; ?></textarea>
              </div>

              <div class="form-group">
                <label for="horario">Horario de clases</label>
                <input type="file" class="form-control" id="horario" name="horario" accept="image/png, image/gif, image/jpeg">
              </div>

              <button class="btn btn-success" type="submit">Guardar cambios</button>
            </form>
          </div>
      </div>
    </div>

    <!-- Horario -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#grupo_horario" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="grupo_horario">
          <h6 class="m-0 font-weight-bold text-primary">Horario de clases</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="grupo_horario">
          <div class="card-body">
            <?php if ($d->g->horario !== null): ?>
              <?php if (is_file(UPLOADS.$d->g->horario)): ?>
                <a href="<?php echo UPLOADED.$d->g->horario; ?>" data-lightbox="Horario" title="<?php echo sprintf('Horario del grupo %s', $d->g->nombre); ?>">
                  <img src="<?php echo UPLOADED.$d->g->horario; ?>" alt="<?php echo sprintf('Horario del grupo %s', $d->g->nombre); ?>" class="img-fluid img-thumbnail">
                </a>
              <?php else: ?>
                <a href="<?php echo get_image('broken.png'); ?>" data-lightbox="Horario" title="<?php echo sprintf('Horario del grupo %s', $d->g->nombre); ?>">
                  <img src="<?php echo get_image('broken.png'); ?>" alt="<?php echo sprintf('Horario del grupo %s', $d->g->nombre); ?>" class="img-fluid img-thumbnail">
                </a>
                <p class="text-muted"><?php echo sprintf('El archivo <b>%s</b> no existe o está dañado.', $d->g->horario); ?></p>
              <?php endif; ?>
            <?php else: ?>
              No hay un horario definido aún para este grupo.
            <?php endif; ?>
          </div>
      </div>
    </div>
  </div>

  <!-- Materias y profesores -->
  <div class="col-xl-4 col-md-6 col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#grupo_materias" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="grupo_materias">
          <h6 class="m-0 font-weight-bold text-primary">Materias y Profesores</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="grupo_materias">
          <div class="card-body">
            <form id="grupo_asignar_materia_form" method="post">
              <?php echo insert_inputs(); ?>
              <input type="hidden" name="id_grupo" value="<?php echo $d->g->id; ?>" required>

              <div class="form-group">
                <label for="id_mp">Selecciona una opción disponible</label>
                <select name="id_mp" id="id_mp" class="form-control" required>
                  <option value="">Materia Impartida</option>
                </select>
              </div>

              <button class="btn btn-success" type="submit">Agregar</button>
            </form>

            <hr>
            
            <div class="wrapper_materias_grupo" data-id="<?php echo $d->g->id; ?>"><!-- agregar con ajax la lista de materias --></div>
          </div>
      </div>
    </div>
  </div>

  <!-- Alumnos -->
  <div class="col-xl-4 col-md-6 col-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#grupo_alumnos" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="grupo_alumnos">
          <h6 class="m-0 font-weight-bold text-primary">Alumnos inscritos</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="grupo_alumnos">
        <div class="card-body">
          <div class="wrapper_alumnos_grupo" data-id="<?php echo $d->g->id; ?>">
            <!-- ajax filled -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>