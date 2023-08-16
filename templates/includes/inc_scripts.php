<!-- Bootstrap core JavaScript-->
<script src="<?php echo ASSETS.'vendor/jquery/jquery.min.js';?>"></script>
<script src="<?php echo ASSETS.'vendor/bootstrap/js/bootstrap.bundle.min.js';?>"></script>

<!-- Core plugin JavaScript-->
<script src="<?php echo ASSETS.'vendor/jquery-easing/jquery.easing.min.js';?>"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo JS.'sb-admin-2.min.js'?>"></script>

<!-- Page level plugins -->
<script src="<?php echo ASSETS.'vendor/chart.js/Chart.min.js'; ?>"></script>
<script src="<?php echo ASSETS.'vendor/datatables/jquery.dataTables.min.js' ?>"></script>
<script src="<?php echo ASSETS.'vendor/datatables/dataTables.bootstrap4.min.js'; ?>"></script>

<!-- Page level custom scripts -->
<script src="<?php echo JS.'demo/chart-area-demo.js';?>"></script>
<script src="<?php echo JS.'demo/chart-pie-demo.js';?>"></script>

<!-- toastr js -->
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- waitme js -->
<script src="<?php echo PLUGINS.'waitme/waitMe.min.js'; ?>"></script>

<!-- Lightbox js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- Objeto Bee Javascript registrado -->
<?php echo load_bee_obj(); ?>

<!-- Scripts registrados manualmente -->
<?php echo load_scripts(); ?>

<!-- Scripts personalizados Bee Framework -->
<script src="<?php echo JS.'main.js?v='.get_version(); ?>"></script>