<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
  <div class="col-12">
    <!-- Detalles de la lección -->
    <div class="card shadow mb-4">
      <div class="card-header font-weight-bold text-primary">
        <?php echo sprintf('Lección / %s / <b>%s</b>', $d->l->profesor, $d->l->materia); ?>

        <a href="alumno/lecciones" class="btn btn-primary btn-sm float-right"><i class="fas fa-undo"></i> Regresar</a>
      </div>
      <div class="card-body">
        <h2><strong><?php echo $d->l->titulo; ?></strong></h2>
      </div>
      <div class="card-footer">
        <span class="float-left"><?php echo sprintf('Disponible hasta el <b>%s</b>.', format_date($d->l->fecha_disponible)); ?></span>
        <span class="float-right"><?php echo format_tiempo_restante($d->l->fecha_disponible); ?></span>
      </div>
    </div>

    <?php if (!empty($d->l->video)): ?>
      <!-- Video de la lección -->
      <div class="card shadow mb-4">
        <div class="card-header font-weight-bold text-primary">Video disponible</div>
        <div class="card-body p-0">
          <div class="yt_video_wrapper">
            <iframe src="<?php echo $d->l->video; ?>" title="<?php echo $d->l->titutlo; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Contenido de la lección -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#contenido_leccion" class="d-block card-header py-3" data-toggle="collapse"
          role="button" aria-expanded="true" aria-controls="contenido_leccion">
          <h6 class="m-0 font-weight-bold text-primary">Contenido</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="contenido_leccion">
          <div class="card-body">
            <?php echo nl2br($d->l->contenido); ?>
          </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>