<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <a href="#calificaciones_data" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="calificaciones_data">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $d->title; ?></h6>
            </a>
            <div class="collapse show" id="calificaciones_data">
                <div class="card-body">
                    <form action="notas/post_agregar" method="post">
                        <?php echo insert_inputs(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Selección del Alumno -->
                                <?php if (!empty($d->alumnos)): ?>
                                <div class="form-group">
                                    <label for="id_alumno">Selecciona un Alumno</label>
                                    <select name="id_alumno" id="id_alumno" class="form-control" required>
                                        <?php foreach ($d->alumnos as $alumno): ?>
                                            <option value="<?php echo $alumno->id_usuario; ?>">
                                                <?php echo $alumno->nombres . ' ' . $alumno->apellidos; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php else: ?>
                <div class="form-group">
                  <label for="id_alumno">Selecciona un Alumno</label>
                  <div class="alert alert-danger">No hay Alumnos Asignados</div>
                </div>
              <?php endif; ?>

                                <!-- Campos de Calificaciones -->
                                <div class="form-group">
                                    <label for="primer_bimestre">Primer Bimestre</label>
                                    <input type="number" class="form-control" id="primer_bimestre" name="primer_bimestre" required>
                                </div>

                                <div class="form-group">
                                    <label for="segundo_bimestre">Segundo Bimestre</label>
                                    <input type="number" class="form-control" id="segundo_bimestre" name="segundo_bimestre" required>
                                </div>

                                <div class="form-group">
                                    <label for="tercer_bimestre">Tercer Bimestre</label>
                                    <input type="number" class="form-control" id="tercer_bimestre" name="tercer_bimestre" required>
                                </div>

                                <div class="form-group">
                                    <label for="cuarto_bimestre">Cuarto Bimestre</label>
                                    <input type="number" class="form-control" id="cuarto_bimestre" name="cuarto_bimestre" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Otros Campos si es necesario -->

                                <!-- Botón de Agregar -->
                                <button class="btn btn-success" type="submit">Agregar Calificaciones</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>
