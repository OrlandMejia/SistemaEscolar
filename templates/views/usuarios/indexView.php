<?php require_once INCLUDES.'inc_header.php'; ?>

<!-- Agregar botón de exportar PDF -->
<!-- Agregar botones de exportar PDF y Excel en el mismo div -->
<div class="mb-3 d-flex">
  <a href="<?php echo buildURL('usuarios/exportar_pdf'); ?>" class="btn btn-info mr-2"  target="_blank"><i class="fas fa-file-pdf"></i> Exportar a PDF</a>
  <a href="<?php echo buildURL('usuarios/exportar_excel'); ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar a Excel</a>
</div>
					<!-- DataTales Example -->
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
		</div>
		<div class="card-body">
		<!--VALIDAMOS QUE EXISTA INFORMACION EN LA TABLA-->
	<?php if(!empty($d->usuarios->rows)):?>
								<div class="table-responsive">
								<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
									<thead>
										<tr>
											<th width="5%">No.</th>
											<th width="5%">Identificación</th>
											<th>Nombre Completo</th>
											<th>Correo Electronico</th>
					  						<th>status</th>
											<th width="10%">Acción</th>
										</tr>
									</thead>

									<tbody>
										<?php foreach ($d->usuarios->rows as $p): ?>
										<tr>
										<td><?php echo sprintf('<a href="profesores/ver/%s">%s</a>', $p->numero, $p->numero); ?></td>
										<td><?php echo empty($p->identificacion) ? 'Sin Identificación' : $p->identificacion; ?></td>
										<td><?php echo empty($p->nombre_completo) ? 'Sin Nombre' : add_ellipsis($p->nombre_completo,50);?></td>
										<td><?php echo empty($p->email) ? 'Sin Correo Electronico' : $p->email; ?></td>
										<td><?php echo format_estado_usuario($p->status); ?></td>
										<td>
											<div class="btn-group">
												<a href="<?php echo 'usuarios/ver/'.$p->numero; ?>" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
												<a href="<?php echo buildURL('profesores/borrar/'.$p->id); ?>" class="btn btn-sm btn-danger confirmar"><i class="fas fa-trash"></i></a>
												

											</div>
										</td>
									</tr>
										 <?php endforeach; ?>

									</tbody>
								</table>
								<?php echo $d->usuarios->pagination; ?>
							</div>
							<?php else:?>
								<div class="py-5 text-center">
									<img src="img/error.jpg" alt="No hay Registros" style="width: 200px;">
									<p class="text-muted">No hay Registros en la Base de Datos</p>
								</div>
								<?php endif; ?>

						</div>
					</div>
<?php require_once INCLUDES.'inc_footer.php'; ?>