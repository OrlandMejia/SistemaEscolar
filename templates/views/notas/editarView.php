<?php require_once INCLUDES . 'inc_header.php'; ?>

<div class="row">
    <div class="col-10">
        <div class="card shadow mb-4">
            <a href="#calificaciones_data" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="calificaciones_data">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-graduation-cap"></i> <?php echo $d->title; ?>
                </h6>
            </a>
            <div class="collapse show" id="calificaciones_data">
                <div class="card-body">
                    <form action="notas/post_editar" method="post">
                        <?php echo insert_inputs(); ?>
                        <!-- Agrega el campo oculto para id_alumno -->
                        <input type="hidden" name="id_alumno" value="<?php echo $d->ac->id_usuario; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Campos de Calificaciones -->
                                <div class="form-group">
                                    <label for="primer_bimestre">
                                        <i class="fas fa-chart-bar"></i> Primer Bimestre
                                    </label>
                                    <input type="number" class="form-control" id="primer_bimestre" name="primer_bimestre" value="<?php echo $d->ac->primer_bimestre; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="segundo_bimestre">
                                        <i class="fas fa-chart-bar"></i> Segundo Bimestre
                                    </label>
                                    <input type="number" class="form-control" id="segundo_bimestre" name="segundo_bimestre" value="<?php echo $d->ac->segundo_bimestre; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="tercer_bimestre">
                                        <i class="fas fa-chart-bar"></i> Tercer Bimestre
                                    </label>
                                    <input type="number" class="form-control" id="tercer_bimestre" name="tercer_bimestre" value="<?php echo $d->ac->tercer_bimestre; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="cuarto_bimestre">
                                        <i class="fas fa-chart-bar"></i> Cuarto Bimestre
                                    </label>
                                    <input type="number" class="form-control" id="cuarto_bimestre" name="cuarto_bimestre" value="<?php echo $d->ac->cuarto_bimestre; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="cuarto_bimestre">
                                        <i class="fas fa-chart-bar"></i> Promedio General
                                    </label>
                                    <input type="number" class="form-control" id="promedio" name="promedio" value="<?php echo $d->ac->promedio; ?>" disabled>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Otros Campos si es necesario -->

                                <!-- Botón de Actualizar -->
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-sync-alt"></i> Actualizar Calificaciones
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once INCLUDES . 'inc_footer.php'; ?>
