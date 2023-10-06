<?php if (!empty($d)): ?>
  <ul class="list-group">
    <?php foreach ($d as $m): ?>
      <li class="list-group-item">
        <a href="<?php echo sprintf('materias/ver/%s', $m->id_materia); ?>" target="_blank"><b><?php echo $m->materia; ?></b></a> impartida por <a href="<?php echo sprintf('profesores/ver/%s', $m->num_profesor); ?>" target="_blank"><b><?php echo $m->profesor; ?></b></a>

        <button class="btn btn-danger btn-sm float-right quitar_materia_grupo" data-id="<?php echo $m->id; ?>"><i class="fas fa-trash"></i></button>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="text-center py-5">
    <img src="img/error.jpg" alt="No hay registros." class="img-fluid" style="width: 200px;">
    <p class="text-muted">No hay materias asignadas al Grado.</p>
  </div>
<?php endif; ?>