<?php require_once INCLUDES.'inc_header.php'; ?>

<!-- DataTales Example -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
	  <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
  </div>
  <div class="card-body">
		<?php if (!empty($d->grupos->rows)): ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th>Nombre del Grado</th>
							<th>Ciclo Escolar</th>
							<th width="10%">Acción</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($d->grupos->rows as $g): ?>
							<tr>
								<td><?php echo sprintf('<a href="notas/ver/%s">%s</a>', $g->id, $g->numero); ?></td>
								<td><?php echo add_ellipsis($g->nombre, 50); ?></td>

								<td>
								<?php echo add_ellipsis($g->ciclo_escolar, 50); ?>
								</td>
								<td>
									<div class="btn-group">
										<a href="<?php echo 'notas/ver/'.$g->id; ?>" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
										
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php echo $d->grupos->pagination; ?>
			</div>
		<?php else: ?>
			<div class="py-5 text-center">
				<img src="img/error.jpg" alt="No hay registros" style="width: 250px;">
				<p class="text-muted">No hay registros en la base de datos.</p>
			</div>
		<?php endif; ?>
  </div>
</div>
	  

<style>
	.fa-lg {
  font-size: 1.7em; /* Ajusta el tamaño del icono según tus preferencias */
}

</style>
<?php require_once INCLUDES.'inc_footer.php'; ?>