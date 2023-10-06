<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-xl-6">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#agregar_grupo_form" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="agregar_grupo_form">
          <h6 class="m-0 font-weight-bold text-primary">Completa el formulario</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="agregar_grupo_form">
          <div class="card-body">
            <form action="grupos/post_agregar" method="post">
              <?php echo insert_inputs(); ?>
              
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>

              <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" cols="10" rows="5" class="form-control"></textarea>
              </div>

              <button class="btn btn-success" type="submit">Guardar grupo</button>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>