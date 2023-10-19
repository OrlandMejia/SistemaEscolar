<?php if (!empty($d)): ?>
  <ul class="list-group">
    <?php foreach ($d as $a): ?>
      <li class="list-group-item">
        <div class="btn-group float-right">
          <a class="btn btn-success btn-sm" href="<?php echo sprintf('mailto:%s?subject=[%s] - Hola %s', $a->email, get_sitename(), $a->nombre_completo); ?>"><i class="fas fa-envelope"></i></a>
          <!--VALIDAR QUE EL USUARIO ESTE SUSPENDIDO Y SI ES ASÍ ENTONCES SE MUESTRA EL BOTON DE VOLVER A ACTIVAR-->
          <?php if ($a->status === 'suspendido'): ?>
            <button class="btn btn-warning text-dark btn-sm remover_suspension_alumno" data-id="<?php echo $a->id; ?>"><i class="fas fa-undo"></i></button>
          <?php else: ?>
            <button class="btn btn-danger btn-sm suspender_alumno" data-id="<?php echo $a->id; ?>"><i class="fas fa-ban"></i></button>
          <?php endif; ?>
          <button class="btn btn-danger btn-sm quitar_alumno_grupo" data-id="<?php echo $a->id; ?>"><i class="fas fa-trash"></i></button>
        </div>
        <a href="<?php echo sprintf('alumnos/ver/%s', $a->id); ?>" target="_blank"><b><?php echo $a->nombre_completo; ?></b></a>
        <?php if ($a->status === 'suspendido'): ?>
          <br>
          <span class="badge badge-pill badge-warning text-dark d-inline-block">Suspendido</span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="text-center py-5">
    <img src="img/error.jpg" alt="No hay registros." class="img-fluid" style="width: 200px;">
    <p class="text-muted">No hay alumnos inscritos a este Grado.</p>
  </div>
<?php endif; ?>