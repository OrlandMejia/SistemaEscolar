<?php require_once INCLUDES.'inc_header.php'; ?>

<!-- Agregar botón de exportar PDF -->
<div class="mb-3 d-flex">
  <a href="<?php echo buildURL('alumnos/exportar_pdf'); ?>" class="btn btn-info mr-2"  target="_blank"><i class="fas fa-file-pdf"></i> Exportar a PDF</a>
  <a href="<?php echo buildURL('alumnos/exportar_excel'); ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar a Excel</a>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
  </div>
  <div class="card-body">
    <?php if (!empty($d->alumnosCalificaciones)): ?>
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th width="5%">No.</th>
              <th>Carnet</th>
              <th>Nombre completo</th>
              <th>Correo electrónico</th>
              <th>Status</th>
              <th>Primer Bimestre</th>
              <th>Segundo Bimestre</th>
              <th>Tercer Bimestre</th>
              <th>Cuarto Bimestre</th>
              <th>Promedio</th>
              <th width="10%">Acción</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($d->alumnosCalificaciones as $ac): ?>
              <tr>
                <td><?php echo sprintf('<a href="notas/ver/%s">%s</a>', $ac->id_usuario, $ac->numero); ?></td>
                <td><?php echo empty($ac->identificacion) ? '<span class="text-muted">Sin Carnet</span>' : $ac->identificacion; ?></td>
                <td><?php echo empty($ac->nombre_completo) ? '<span class="text-muted">Sin nombre</span>' : add_ellipsis($ac->nombre_completo, 50); ?></td>
                <td><?php echo empty($ac->email) ? '<span class="text-muted">Sin correo electrónico</span>' : $ac->email; ?></td>
                <td><?php echo format_estado_usuario($ac->status); ?></td>
                <td><?php echo $ac->primer_bimestre; ?></td>
                <td><?php echo $ac->segundo_bimestre; ?></td>
                <td><?php echo $ac->tercer_bimestre; ?></td>
                <td><?php echo $ac->cuarto_bimestre; ?></td>
                <td><?php echo $ac->promedio; ?></td>
                <td>
                  <div class="btn-group">
                    <a href="<?php echo 'notas/editar/'.$ac->id_usuario; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="py-5 text-center">
        <img src="img/error.jpg" alt="No hay registros" style="width: 250px;">
        <p class="text-muted">No hay registros en la bases de datos.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>
