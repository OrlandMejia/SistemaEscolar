<?php require_once INCLUDES.'inc_header.php'; ?>

<!-- DataTales Example -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
	  <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
  </div>
  <div class="card-body">
		<?php if (!empty($d->materias)): ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Nombre</th>
							<th width="10%">Acción</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($d->materias as $m): ?>
							<tr>
								<td><a href="<?php echo sprintf('grupos/materia/%s', $m->id); ?>"><?php echo $m->nombre; ?></a></td>
								<td>
									<div class="btn-group">
                    <a class="btn btn-success btn-sm" href="<?php echo buildURL('lecciones/agregar', ['id_materia' => $m->id], false, false); ?>"><i class="fas fa-plus"></i></a>
										<a class="btn btn-success btn-sm" href="<?php echo sprintf('grupos/materia/%s', $m->id); ?>"><i class="fas fa-eye"></i></a>
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
				<p class="text-muted">No hay registros en la base de datos.</p>
			</div>
		<?php endif; ?>
  </div>
</div>
	  
<?php require_once INCLUDES.'inc_footer.php'; ?>