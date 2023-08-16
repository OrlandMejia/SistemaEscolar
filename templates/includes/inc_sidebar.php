        <!-- CODIGO PARA QUE LA CLASE ACTIVE DE LA BARRA DE NAVEGACIÓN SE COLOQUE DEPENDIENDO DEL PARAMETRO EN LA BARRA DE NAVEGACIÓN -->
        <?php 
        //se setea para que tenga un parametro por defecto co una variable llamada slog
        $slug = isset($d->slug) && !empty($d->slug) ? $d->slug : 'dashboard';
        ?>
        
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <!-- ACA VEMOS LO QUE ES EL COLOR DE LA BARRA Y CON LA FUNCION ECHO URL COLOCAMOS LA VARIABLE QUE DIRIGI AL INDEX-->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php ECHO URL;?>">
                <div class="sidebar-brand-icon">
                    <img src="<?php echo get_image('logo.png');?>" alt="<?php echo get_sitename();?>" class="img-fluid" style="width: 80px">
                </div>
                <div class="sidebar-brand-text mx-3">Sistema Judá<sup></sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- ADMINISTRACION -->
            <!--VALIDAMOS EL ROL DEL USUARIO Y APARECE EL BOTON SEGUN EL ROL-->
            <?php if (is_admin(get_user_rol())): ?>
            <li class="nav-item <?php echo $slug === 'admin' ? 'active' : null; ?>">
                <a class="nav-link" href="admin">
                    <i class="fas fa-fw fa-user-lock"></i>
                        <span>Administración</span></a>
            </li>
            <?php endif; ?>


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

              <!-- NAV ITEM QUE DIRIGE HACIA EL MODULO DE PROFESORES -->
              <li class="nav-item  <?php echo $slug === 'profesores' ? 'active' : null; ?>">
                <a class="nav-link" href="profesores">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Profesores</span></a>
            </li>

                        <!-- NAV ITEM QUE DIRIGE HACIA EL MODULO DE ALUMNOS -->
            <li class="nav-item  <?php echo $slug === 'alumnos' ? 'active' : null; ?>">
                <a class="nav-link" href="alumnos">
                    <i class="fas fa-fw fa-book-reader"></i>
                    <span>Alumnos</span></a>
            </li>

              <!-- NAV ITEM QUE DIRIGE HACIA EL MODULO DE MATERIAS -->
              <li class="nav-item  <?php echo $slug === 'materias' ? 'active' : null; ?>">
                <a class="nav-link" href="materias">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Materias</span></a>
            </li>

            <!-- NAV ITEM QUE DIRIGE HACIA EL MODULO DE GRUPOS -->
            <li class="nav-item  <?php echo $slug === 'grupos' ? 'active' : null; ?>">
                <a class="nav-link" href="grupos">
                    <i class="fas fa-fw fa-graduation-cap"></i>
                    <span>Grupos</span></a>
            </li>

            <!-- NAV ITEM QUE DIRIGE HACIA EL MODULO DE HORARIOS -->
            <li class="nav-item  <?php echo $slug === 'horarios' ? 'active' : null; ?>">
                <a class="nav-link" href="horarios">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Horarios</span></a>
            </li>

            <!-- NAV ITEM QUE DIRIGE HACIA EL MODULO DE LECCIONES -->
            <li class="nav-item  <?php echo $slug === 'lecciones' ? 'active' : null; ?>">
                <a class="nav-link" href="lecciones">
                    <i class="fas fa-fw fa-chalkboard-teacher"></i>
                    <span>Lecciones</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message --
            <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="img/undraw_rocket.svg" alt="...">
                <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to Pro!</a>
            </div>-->

        </ul>
        <!-- End of Sidebar -->