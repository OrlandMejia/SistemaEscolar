<?php require_once INCLUDES.'inc_header.php'; ?>

<!-- Agregar botón de exportar PDF -->
<!-- Agregar botones de exportar PDF y Excel en el mismo div -->
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
		<?php if (!empty($d->alumnos->rows)): ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th>Carnet</th>
							<th>Nombre completo</th>
							<th>Correo electrónico</th>
							<th>Status</th>
							<th width="10%">Acción</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($d->alumnos->rows as $a): ?>
							<tr>
								<td><?php echo sprintf('<a href="alumnos/ver/%s">%s</a>', $a->id, $a->numero); ?></td>
								<td><?php echo empty($a->identificacion) ? '<span class="text-muted">Sin Carnet</span>' : $a->identificacion; ?></td>
								<td><?php echo empty($a->nombre_completo) ? '<span class="text-muted">Sin nombre</span>' : add_ellipsis($a->nombre_completo, 50); ?></td>
								<td><?php echo empty($a->email) ? '<span class="text-muted">Sin correo electrónico</span>' : $a->email; ?></td>
								<td><?php echo format_estado_usuario($a->status); ?></td>
								<td>
									<div class="btn-group">
										<a href="<?php echo 'alumnos/ver/'.$a->id; ?>" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
										<?php if ($a->status === 'suspendido'): ?>
										<button class="btn btn-warning text-dark btn-sm remover_suspension_alumno" data-view="alumnos" data-id="<?php echo $a->id; ?>"><i class="fas fa-undo"></i></button>
										<?php else: ?>
										<button class="btn btn-danger btn-sm suspender_alumno" data-view="alumnos" data-id="<?php echo $a->id; ?>"><i class="fas fa-ban"></i></button>
										<?php endif; ?>
										<a href="<?php echo buildURL('alumnos/borrar/'.$a->id); ?>" class="btn btn-sm btn-danger confirmar"><i class="fas fa-trash"></i></a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php echo $d->alumnos->pagination; ?>
			</div>
		<?php else: ?>
			<div class="py-5 text-center">
				<img src="error.jpg" alt="No hay registros" style="width: 250px;">
				<p class="text-muted">No hay registros en la base de datos.</p>
			</div>
		<?php endif; ?>
  </div>
</div>
	  
<?php require_once INCLUDES.'inc_footer.php'; ?>