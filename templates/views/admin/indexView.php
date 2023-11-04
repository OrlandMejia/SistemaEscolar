<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-xl-6">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#admin_panel" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="admin_panel">
          <h6 class="m-0 font-weight-bold text-primary">Reiniciar sistema</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="admin_panel">
          <div class="card-body">
            <form id="reiniciar_sistema_form" method="post">
              <?php echo insert_inputs(); ?>
              <button class="btn btn-success" type="submit"><i class="fas fa-database fa-fw"></i> Reiniciar base de datos</button>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>