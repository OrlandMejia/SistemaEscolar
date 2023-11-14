<?php require_once INCLUDES . 'inc_header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <a href="#calificaciones_data" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="calificaciones_data">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
            </a>
            <div class="collapse show" id="calificaciones_data">
                <div class="card-body">
                    <form action="notas/post_editar" method="post">
                        <?php echo insert_inputs(); ?>
                        <div class="row">
                            <div class="col-md-6">

                                <!-- Campos de Calificaciones -->
                                <div class="form-group">
                                    <label for="primer_bimestre">Primer Bimestre</label>
                                    <input type="number" class="form-control" id="primer_bimestre" name="primer_bimestre" value="<?php echo isset($d->ac->primer_bimestre) ? $d->ac->primer_bimestre : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="segundo_bimestre">Segundo Bimestre</label>
                                    <input type="number" class="form-control" id="segundo_bimestre" name="segundo_bimestre" value="<?php echo isset($d->ac->segundo_bimestre) ? $d->ac->segundo_bimestre : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="tercer_bimestre">Tercer Bimestre</label>
                                    <input type="number" class="form-control" id="tercer_bimestre" name="tercer_bimestre" value="<?php echo isset($d->ac->tercer_bimestre) ? $d->ac->tercer_bimestre : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="cuarto_bimestre">Cuarto Bimestre</label>
                                    <input type="number" class="form-control" id="cuarto_bimestre" name="cuarto_bimestre" value="<?php echo isset($d->ac->cuarto_bimestre) ? $d->ac->cuarto_bimestre : ''; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
    <!-- Otros Campos si es necesario -->

    <!-- Agregamos un campo oculto para el id_calificacion -->
    <input type="hidden" name="id_calificacion" value="<?php echo $d->ac->id_calificacion; ?>">

    <!-- Botón de Agregar -->
    <button class="btn btn-success" type="submit">Actualizar Calificaciones</button>
</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once INCLUDES . 'inc_footer.php'; ?>
