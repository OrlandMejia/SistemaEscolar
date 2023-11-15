<!-- Nav Item - Dashboard -->
<li class="nav-item <?php echo $slug === 'dashboard' ? 'active' : null; ?>">
  <a class="nav-link" href="dashboard">
    <i class="fas fa-fw fa-tachometer-alt"></i>
    <span>Dashboard</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
  Menú
</div>

<!-- Nav Item - Lecciones -->
<li class="nav-item <?php echo $slug === 'alumno-lecciones' ? 'active' : null; ?>">
  <a class="nav-link" href="alumno/lecciones">
    <i class="fas fa-fw fa-chalkboard-teacher"></i>
    <span>Tareas</span></a>
</li>



<!-- Nav Item - Grupos del profesor -->
<li class="nav-item <?php echo $slug === 'alumno-grupo' ? 'active' : null; ?>">
  <a class="nav-link" href="alumno/grupo">
    <i class="fas fa-fw fa-users"></i>
    <span>Grado</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
  <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>