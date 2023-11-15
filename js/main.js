$(document).ready(function() {

  // Toast para notificaciones
  //toastr.warning('My name is Inigo Montoya. You killed my father, prepare to die!');

  // Waitme
  //$('body').waitMe({effect : 'orbit'});
  console.log('////////// Bienvenido a Bee Framework Versión ' + Bee.bee_version + ' //////////');
  console.log('//////////////////// www.joystick.com.mx ////////////////////');
  console.log(Bee);

  /**
   * Prueba de peticiones ajax al backend en versión 1.1.3
   */
  function test_ajax() {
    var body = $('body'),
    hook     = 'bee_hook',
    action   = 'post',
    csrf     = Bee.csrf;

    if ($('#test_ajax').length == 0) return;

    $.ajax({
      url: 'ajax/test',
      type: 'post',
      dataType: 'json',
      data : { hook , action , csrf },
      beforeSend: function() {
        body.waitMe();
      }
    }).done(function(res) {
      toastr.success(res.msg);
      console.log(res);
    }).fail(function(err) {
      toastr.error('Prueba AJAX fallida.', '¡Upss!');
    }).always(function() {
      body.waitMe('hide');
    })
  }
  
  /**
   * Alerta para confirmar una acción establecida en un link o ruta específica
   */
  $('body').on('click', '.confirmar', function(e) {
    e.preventDefault();

    let url = $(this).attr('href'),
    ok      = confirm('¿Estás seguro?');

    // Redirección a la URL del enlace
    if (ok) {
      window.location = url;
      return true;
    }
    
    console.log('Acción cancelada.');
    return true;
  });

  /**
   * Inicializa summernote el editor de texto avanzado para textareas
   */
  function init_summernote() {
    if ($('.summernote').length == 0) return;

    $('.summernote').summernote({
      placeholder: 'Escribe en este campo...',
      tabsize: 2,
      height: 300
    });
  }

  /**
   * Inicializa tooltips en todo el sitio
   */
  function init_tooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  }
  
  // Inicialización de elementos
  init_summernote();
  init_tooltips();
  test_ajax();
  $('#dataTable').DataTable(
    {
      language: {
        search:         "Buscar&nbsp;:",
        lengthMenu:     "Mostrar _MENU_ registros",
        info:           "Mostrando _START_ a _END_ de _TOTAL_ registros.",
        infoEmpty:      "Mostrando 0 registros.",
        infoFiltered:   "(Filtrando de _MAX_ registros en total)",
        infoPostFix:    "",
        zeroRecords:    "No hay registros encontrados.",
        emptyTable:     "No hay información.",
        paginate: {
          first:      "Primera",
          previous:   "Anterior",
          next:       "Siguiente",
          last:       "Última"
        }
      },
      paging: false,
      aaSorting: []
    }
  );

  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  ///////// NO REQUERIDOS, SOLO PARA EL PROYECTO DEMO DE GASTOS E INGRESOS
  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  
  // Agregar un movimiento
  $('.bee_add_movement').on('submit', bee_add_movement);
  function bee_add_movement(event) {
    event.preventDefault();

    var form    = $('.bee_add_movement'),
    hook        = 'bee_hook',
    action      = 'add',
    data        = new FormData(form.get(0)),
    type        = $('#type').val(),
    description = $('#description').val(),
    amount      = $('#amount').val();
    data.append('hook', hook);
    data.append('action', action);

    // Validar que este seleccionada una opción type
    if(type === 'none') {
      toastr.error('Selecciona un tipo de movimiento válido', '¡Upss!');
      return;
    }

    // Validar description
    if(description === '' || description.length < 5) {
      toastr.error('Ingresa una descripción válida', '¡Upss!');
      return;
    }

    // Validar amount
    if(amount === '' || amount <= 0) {
      toastr.error('Ingresa un monto válido', '¡Upss!');
      return;
    }

    // AJAX
    $.ajax({
      url: 'ajax/bee_add_movement',
      type: 'post',
      dataType: 'json',
      contentType: false,
      processData: false,
      cache: false,
      data : data,
      beforeSend: function() {
        form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 201) {
        toastr.success(res.msg, '¡Bien!');
        form.trigger('reset');
        bee_get_movements();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }

  // Cargar movimientos
  bee_get_movements();
  function bee_get_movements() {
    var wrapper = $('.bee_wrapper_movements'),
    hook        = 'bee_hook',
    action      = 'load';

    if (wrapper.length === 0) {
      return;
    }

    $.ajax({
      url: 'ajax/bee_get_movements',
      type: 'POST',
      dataType: 'json',
      cache: false,
      data: {
        hook, action
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        wrapper.html(res.data);
      } else {
        toastr.error(res.msg, '¡Upss!');
        wrapper.html('');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
      wrapper.html('');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }

  // Actualizar un movimiento
  $('body').on('dblclick', '.bee_movement', bee_update_movement);
  function bee_update_movement(event) {
    var li              = $(this),
    id                  = li.data('id'),
    hook                = 'bee_hook',
    action              = 'get',
    add_form            = $('.bee_add_movement'),
    wrapper_update_form = $('.bee_wrapper_update_form');

    // AJAX
    $.ajax({
      url: 'ajax/bee_update_movement',
      type: 'POST',
      dataType: 'json',
      cache: false,
      data: {
        hook, action, id
      },
      beforeSend: function() {
        wrapper_update_form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        wrapper_update_form.html(res.data);
        add_form.hide();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      wrapper_update_form.waitMe('hide');
    })
  }

  $('body').on('submit', '.bee_save_movement', bee_save_movement);
  function bee_save_movement(event) {
    event.preventDefault();

    var form    = $('.bee_save_movement'),
    hook        = 'bee_hook',
    action      = 'update',
    data        = new FormData(form.get(0)),
    type        = $('select[name="type"]', form).val(),
    description = $('input[name="description"]', form).val(),
    amount      = $('input[name="amount"]', form).val(),
    add_form            = $('.bee_add_movement');
    data.append('hook', hook);
    data.append('action', action);

    // Validar que este seleccionada una opción type
    if(type === 'none') {
      toastr.error('Selecciona un tipo de movimiento válido', '¡Upss!');
      return;
    }

    // Validar description
    if(description === '' || description.length < 5) {
      toastr.error('Ingresa una descripción válida', '¡Upss!');
      return;
    }

    // Validar amount
    if(amount === '' || amount <= 0) {
      toastr.error('Ingresa un monto válido', '¡Upss!');
      return;
    }

    // AJAX
    $.ajax({
      url: 'ajax/bee_save_movement',
      type: 'post',
      dataType: 'json',
      contentType: false,
      processData: false,
      cache: false,
      data : data,
      beforeSend: function() {
        form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, '¡Bien!');
        form.trigger('reset');
        form.remove();
        add_form.show();
        bee_get_movements();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }

  // Borrar un movimiento
  $('body').on('click', '.bee_delete_movement', bee_delete_movement);
  function bee_delete_movement(event) {
    var boton   = $(this),
    id          = boton.data('id'),
    hook        = 'bee_hook',
    action      = 'delete',
    wrapper     = $('.bee_wrapper_movements');

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/bee_delete_movement',
      type: 'POST',
      dataType: 'json',
      cache: false,
      data: {
        hook, action, id
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, 'Bien!');
        bee_get_movements();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }

  // Guardar o actualizar opciones
  $('.bee_save_options').on('submit', bee_save_options);
  function bee_save_options(event) {
    event.preventDefault();

    var form = $('.bee_save_options'),
    data     = new FormData(form.get(0)),
    hook     = 'bee_hook',
    action   = 'add';
    data.append('hook', hook);
    data.append('action', action);

    // AJAX
    $.ajax({
      url: 'ajax/bee_save_options',
      type: 'post',
      dataType: 'json',
      contentType: false,
      processData: false,
      cache: false,
      data : data,
      beforeSend: function() {
        form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200 || res.status === 201) {
        toastr.success(res.msg, '¡Bien!');
        bee_get_movements();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }

  /////////////////////////////////////////////////////////
  //// PROYECTO SISTEMA ESCOLAR
  /////////////////////////////////////////////////////////

  // Función para cargar el listado de materias disponibles
  function get_materias_disponibles_profesor() {

    var form    = $('#profesor_asignar_materia_form'),
    select      = $('select', form),
    id_profesor = $('input[name="id"]', form).val(),
    wrapper     = $('#profesor_materias'),
    opciones    = '',
    action      = 'get',
    hook        = 'bee_hook';

    if (form.length == 0) return;

    // Limpiar las opciones al cargar
    select.html('');

    // AJAX
    $.ajax({
      url: 'ajax/get_materias_disponibles_profesor',
      type: 'get',
      dataType: 'json',
      data : { 
        '_t': Bee.csrf,
        id_profesor,
        action,
        hook
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        if (res.data.length === 0) {
          select.html('<option disabled selected>No hay opciones disponibles.</option>')
          $('button', form).attr('disabled', true);
          return;
        }
        
        $.each(res.data, function(i, m) {
          opciones += '<option value="'+m.id+'">'+m.nombre+'</option>';
        });

        select.html(opciones);
        $('button', form).attr('disabled', false);

      } else {
        select.html('<option disabled selected>No hay opciones disponibles.</option>')
        $('button', form).attr('disabled', true);
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  get_materias_disponibles_profesor();

  // Función para cargar las materias del profesor
  function get_materias_profesor() {

    var wrapper = $('.wrapper_materias_profesor'),
    id_profesor = wrapper.data('id'),
    action      = 'get',
    hook        = 'bee_hook';

    if (wrapper.length == 0) return;

    // AJAX
    $.ajax({
      url: 'ajax/get_materias_profesor',
      type: 'get',
      dataType: 'json',
      data : { 
        '_t': Bee.csrf,
        id_profesor,
        action,
        hook
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        wrapper.html(res.data);
      } else {
        wrapper.html(res.msg);
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  get_materias_profesor();

  // Agregar nueva materia al profesor
  $('#profesor_asignar_materia_form').on('submit', add_materia_profesor);
  function add_materia_profesor(e) {
    e.preventDefault();

    var form    = $('#profesor_asignar_materia_form'),
    select      = $('select', form),
    id_materia  = select.val(),
    id_profesor = $('input[name="id"]', form).val(),
    csrf        = $('input[name="csrf"]', form).val(),
    action      = 'post',
    hook        = 'bee_hook';

    if (id_materia === undefined || id_materia === '') {
      toastr.error('Selecciona una materia válida.');
      return;
    }

    // AJAX
    $.ajax({
      url: 'ajax/add_materia_profesor',
      type: 'post',
      dataType: 'json',
      data : { 
        csrf,
        id_materia,
        id_profesor,
        action,
        hook
      },
      beforeSend: function() {
        form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 201) {
        toastr.success(res.msg);
        get_materias_disponibles_profesor();
        get_materias_profesor();

      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }

  // Quitar materia de profesor
  $('body').on('click', '.quitar_materia_profesor', quitar_materia_profesor);
  function quitar_materia_profesor(e) {
    e.preventDefault();

    var btn     = $(this),
    wrapper     = $('.wrapper_materias_profesor'),
    csrf        = Bee.csrf,
    id_materia  = btn.data('id'),
    id_profesor = wrapper.data('id'),
    li          = btn.closest('li'),
    action      = 'delete',
    hook        = 'bee_hook';

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/quitar_materia_profesor',
      type: 'post',
      dataType: 'json',
      cache: false,
      data: {
        csrf,
        id_materia,
        id_profesor,
        action,
        hook
      },
      beforeSend: function() {
        li.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, 'Bien!');
        li.fadeOut();
        get_materias_disponibles_profesor();
        get_materias_profesor();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      li.waitMe('hide');
    })
  }

  // Cargar materias disponibles para grupo
  function get_materias_disponibles_grupo() {

    var form    = $('#grupo_asignar_materia_form'),
    select      = $('select', form),
    id_grupo    = $('input[name="id_grupo"]', form).val(),
    wrapper     = $('.wrapper_materias_grupo'),
    opciones    = '',
    _t          = Bee.csrf,
    action      = 'get',
    hook        = 'bee_hook';

    if (form.length == 0) return;

    // Limpiar las opciones al cargar
    select.html('');

    // AJAX
    $.ajax({
      url: 'ajax/get_materias_disponibles_grupo',
      type: 'get',
      dataType: 'json',
      data : { 
        _t,
        id_grupo,
        action,
        hook
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        if (res.data.length === 0) {
          select.html('<option disabled selected>No hay opciones disponibles.</option>')
          $('button', form).attr('disabled', true);
          return;
        }
        
        $.each(res.data, function(i, m) {
          opciones += '<option value="'+m.id+'">'+m.materia+' impartida por '+m.profesor+'</option>';
        });

        select.html(opciones);
        $('button', form).attr('disabled', false);

      } else {
        select.html('<option disabled selected>No hay opciones disponibles.</option>')
        $('button', form).attr('disabled', true);
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  get_materias_disponibles_grupo();

  // Función para cargar las materias de un grupo
  function get_materias_grupo() {

    var wrapper = $('.wrapper_materias_grupo'),
    id_grupo    = wrapper.data('id'),
    _t          = Bee.csrf,
    action      = 'get',
    hook        = 'bee_hook';

    if (wrapper.length == 0) return;

    // AJAX
    $.ajax({
      url: 'ajax/get_materias_grupo',
      type: 'get',
      dataType: 'json',
      data : { 
        _t,
        id_grupo,
        action,
        hook
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        wrapper.html(res.data);
      } else {
        wrapper.html(res.msg);
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  get_materias_grupo();

  // Agregar nueva materia al grupo
  $('#grupo_asignar_materia_form').on('submit', add_materia_grupo);
  function add_materia_grupo(e) {
    e.preventDefault();

    var form    = $('#grupo_asignar_materia_form'),
    select      = $('select', form),
    id_mp       = select.val(),
    id_grupo    = $('input[name="id_grupo"]', form).val(),
    csrf        = $('input[name="csrf"]', form).val(),
    action      = 'post',
    hook        = 'bee_hook';

    if (id_mp === undefined || id_mp === '') {
      toastr.error('Selecciona una materia válida.');
      return;
    }

    // AJAX
    $.ajax({
      url: 'ajax/add_materia_grupo',
      type: 'post',
      dataType: 'json',
      data : { 
        csrf,
        id_mp,
        id_grupo,
        action,
        hook
      },
      beforeSend: function() {
        form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 201) {
        toastr.success(res.msg);
        get_materias_disponibles_grupo();
        get_materias_grupo();

      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }

  // Quitar materia de grupo
  $('body').on('click', '.quitar_materia_grupo', quitar_materia_grupo);
  function quitar_materia_grupo(e) {
    e.preventDefault();

    var btn     = $(this),
    wrapper     = $('.wrapper_materias_grupo'),
    csrf        = Bee.csrf,
    id_mp       = btn.data('id'),
    id_grupo    = wrapper.data('id'),
    li          = btn.closest('li'),
    action      = 'delete',
    hook        = 'bee_hook';

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/quitar_materia_grupo',
      type: 'post',
      dataType: 'json',
      cache: false,
      data: {
        csrf,
        id_mp,
        id_grupo,
        action,
        hook
      },
      beforeSend: function() {
        li.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, 'Bien!');
        li.fadeOut();
        get_materias_disponibles_grupo();
        get_materias_grupo();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      li.waitMe('hide');
    })
  }

  // Función para cargar los alumnos de un grupo
  function get_alumnos_grupo() {

    var wrapper = $('.wrapper_alumnos_grupo'),
    id_grupo    = wrapper.data('id'),
    _t          = Bee.csrf,
    action      = 'get',
    hook        = 'bee_hook';

    if (wrapper.length == 0) return;

    // AJAX
    $.ajax({
      url: 'ajax/get_alumnos_grupo',
      type: 'get',
      dataType: 'json',
      data : { 
        _t,
        id_grupo,
        action,
        hook
      },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        wrapper.html(res.data);
      } else {
        wrapper.html(res.msg);
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición.', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  get_alumnos_grupo();

  // Quitar alumno de grupo
  $('body').on('click', '.quitar_alumno_grupo', quitar_alumno_grupo);
  function quitar_alumno_grupo(e) {
    e.preventDefault();

    var btn     = $(this),
    wrapper     = $('.wrapper_alumnos_grupo'),
    csrf        = Bee.csrf,
    id_alumno   = btn.data('id'),
    id_grupo    = wrapper.data('id'),
    li          = btn.closest('li'),
    action      = 'delete',
    hook        = 'bee_hook';

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/quitar_alumno_grupo',
      type: 'post',
      dataType: 'json',
      cache: false,
      data: {
        csrf,
        id_alumno,
        id_grupo,
        action,
        hook
      },
      beforeSend: function() {
        li.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, 'Bien!');
        li.fadeOut();
        get_alumnos_grupo();

      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      li.waitMe('hide');
    })
  }

  // Suspender alumno
  $('body').on('click', '.suspender_alumno', suspender_alumno);
  function suspender_alumno(e) {
    e.preventDefault();

    var btn     = $(this),
    csrf        = Bee.csrf,
    view        = btn.data('view'),
    id_alumno   = btn.data('id'),
    action      = 'put',
    hook        = 'bee_hook';

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/suspender_alumno',
      type: 'post',
      dataType: 'json',
      cache: false,
      data: {
        csrf,
        id_alumno,
        action,
        hook
      },
      beforeSend: function() {
        $('body').waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, 'Bien!');

        if (view === 'alumnos') {
          window.location.reload();
          return false;
        }

        get_alumnos_grupo();
        
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      $('body').waitMe('hide');
    })
  }

  // Retirar suspensión del alumno
  $('body').on('click', '.remover_suspension_alumno', remover_suspension_alumno);
  function remover_suspension_alumno(e) {
    e.preventDefault();

    var btn     = $(this),
    csrf        = Bee.csrf,
    view        = btn.data('view'),
    id_alumno   = btn.data('id'),
    action      = 'put',
    hook        = 'bee_hook';

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/remover_suspension_alumno',
      type: 'post',
      dataType: 'json',
      cache: false,
      data: {
        csrf,
        id_alumno,
        action,
        hook
      },
      beforeSend: function() {
        $('body').waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, 'Bien!');

        if (view === 'alumnos') {
          window.location.reload();
          return false;
        }
        
        get_alumnos_grupo();
        
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      $('body').waitMe('hide');
    })
  }

  // Inicializar la configuración de las gráficas
  Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#858796';

  // Función que usa el theme para formatear números
  function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
      s = '',
      toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
      };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
  }

  // Cargar dashboard de administrador
  function init_dashboard() {
    var chart1 = $('#resumen_ingresos_chart'),
    chart2     = $('#resumen_comunidad_chart'),
    chart3     = $('#resumen_enseñanza_chart'),
    chart4     = $('#resumen_enseñanza_profesor_chart');

    if (chart1.length !== 0) draw_resumen_ingresos_chart(chart1);
    if (chart2.length !== 0) draw_resumen_comunidad_chart(chart2);
    if (chart3.length !== 0) draw_resumen_enseñanza_chart(chart3);
    if (chart4.length !== 0) draw_resumen_enseñanza_profesor_chart(chart4);
  }
  init_dashboard();

  // Dibujar gráfica de resumen de ingresos
  function draw_resumen_ingresos_chart(element) {
    var wrapper = element.parent('div'),
    _t          = Bee.csrf,
    action      = 'get',
    hook        = 'bee_hook';

    // AJAX
    $.ajax({
      url: 'ajax/get_resumen_ingresos',
      type: 'get',
      dataType: 'json',
      data : { _t, action, hook },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        var myLineChart = new Chart(element, {
          type: 'line',
          data: {
            labels: res.data.labels,
            datasets: [{
              label: "Ingresos",
              lineTension: 0.3,
              backgroundColor: "rgba(78, 115, 223, 0.05)",
              borderColor: "rgba(78, 115, 223, 1)",
              pointRadius: 3,
              pointBackgroundColor: "rgba(78, 115, 223, 1)",
              pointBorderColor: "rgba(78, 115, 223, 1)",
              pointHoverRadius: 3,
              pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
              pointHoverBorderColor: "rgba(78, 115, 223, 1)",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: res.data.data,
            }],
          },
          options: {
            maintainAspectRatio: false,
            layout: {
              padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
              }
            },
            scales: {
              xAxes: [{
                time: {
                  unit: 'date'
                },
                gridLines: {
                  display: false,
                  drawBorder: false
                },
                ticks: {
                  maxTicksLimit: 20
                }
              }],
              yAxes: [{
                ticks: {
                  maxTicksLimit: 8,
                  padding: 10,
                  // Include a dollar sign in the ticks
                  callback: function(value, index, values) {
                    return '$' + number_format(value);
                  }
                },
                gridLines: {
                  color: "rgb(234, 236, 244)",
                  zeroLineColor: "rgb(234, 236, 244)",
                  drawBorder: false,
                  borderDash: [2],
                  zeroLineBorderDash: [2]
                }
              }],
            },
            legend: {
              display: false
            },
            tooltips: {
              backgroundColor: "rgb(255,255,255)",
              bodyFontColor: "#858796",
              titleMarginBottom: 10,
              titleFontColor: '#6e707e',
              titleFontSize: 14,
              borderColor: '#dddfeb',
              borderWidth: 1,
              xPadding: 15,
              yPadding: 15,
              displayColors: false,
              intersect: false,
              mode: 'index',
              caretPadding: 10,
              callbacks: {
                label: function(tooltipItem, chart) {
                  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                  return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
                }
              }
            }
          }
        });
      } else {
        wrapper.html(res.msg);
      }
    }).fail(function(err) {
      wrapper.html('Hubo un error al cargar la información.')
    }).always(function() {
      wrapper.waitMe('hide');
    });
  }

  // Dibujar gráfica de resumen de comunidad
  function draw_resumen_comunidad_chart(element) {
    var wrapper = element.parent('div'),
    _t          = Bee.csrf,
    action      = 'get',
    hook        = 'bee_hook';

    // AJAX
    $.ajax({
      url: 'ajax/get_resumen_comunidad',
      type: 'get',
      dataType: 'json',
      data : { _t, action, hook },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        var myPieChart = new Chart(element, {
          type: 'doughnut',
          data: {
            labels: res.data.labels,
            datasets: [{
              data: res.data.data,
              backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
              hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
              hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
          },
          options: {
            maintainAspectRatio: false,
            tooltips: {
              backgroundColor: "rgb(255,255,255)",
              bodyFontColor: "#858796",
              borderColor: '#dddfeb',
              borderWidth: 1,
              xPadding: 15,
              yPadding: 15,
              displayColors: false,
              caretPadding: 10,
            },
            legend: {
              display: true
            },
            cutoutPercentage: 50,
          },
        });
      } else {
        wrapper.html(res.msg);
      }
    }).fail(function(err) {
      wrapper.html('Hubo un error al cargar la información.')
    }).always(function() {
      wrapper.waitMe('hide');
    });
  }

  // Dibujar gráfica de resumen de lecciones
  function draw_resumen_enseñanza_chart(element) {
    var wrapper = element.parent('div'),
    _t          = Bee.csrf,
    action      = 'get',
    hook        = 'bee_hook';

    // AJAX
    $.ajax({
      url: 'ajax/get_resumen_ensenanza',
      type: 'get',
      dataType: 'json',
      data : { _t, action, hook },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        var myLineChart = new Chart(element, {
          type: 'bar',
          data: {
            labels: res.data.labels,
            datasets: [{
              label: "Tareas",
              lineTension: 0.3,
              backgroundColor: "rgba(78, 115, 223, 0.5)",
              borderColor: "rgba(78, 115, 223, 1)",
              pointRadius: 3,
              pointBackgroundColor: "rgba(78, 115, 223, 1)",
              pointBorderColor: "rgba(78, 115, 223, 1)",
              pointHoverRadius: 3,
              pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
              pointHoverBorderColor: "rgba(78, 115, 223, 1)",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: res.data.data,
            }],
          },
          options: {
            maintainAspectRatio: false,
            layout: {
              padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
              }
            },
            scales: {
              xAxes: [{
                time: {
                  unit: 'date'
                },
                gridLines: {
                  display: false,
                  drawBorder: false
                },
                ticks: {
                  maxTicksLimit: 20
                }
              }],
              yAxes: [{
                ticks: {
                  maxTicksLimit: 5,
                  padding: 10
                },
                gridLines: {
                  color: "rgb(234, 236, 244)",
                  zeroLineColor: "rgb(234, 236, 244)",
                  drawBorder: false,
                  borderDash: [2],
                  zeroLineBorderDash: [2]
                }
              }],
            },
            legend: {
              display: false
            },
            tooltips: {
              backgroundColor: "rgb(255,255,255)",
              bodyFontColor: "#858796",
              titleMarginBottom: 10,
              titleFontColor: '#6e707e',
              titleFontSize: 14,
              borderColor: '#dddfeb',
              borderWidth: 1,
              xPadding: 15,
              yPadding: 15,
              displayColors: false,
              intersect: false,
              mode: 'index',
              caretPadding: 10,
              callbacks: {
                label: function(tooltipItem, chart) {
                  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                  return datasetLabel + ': ' + tooltipItem.yLabel;
                }
              }
            }
          }
        });
      } else {
        wrapper.html(res.msg);
      }
    }).fail(function(err) {
      wrapper.html('Hubo un error al cargar la información.')
    }).always(function() {
      wrapper.waitMe('hide');
    });
  }

  // Recargar tabla de resumen de ingresos
  $('.recargar_resumen_ingresos_chart').on('click', recargar_resumen_ingresos_chart);
  function recargar_resumen_ingresos_chart(e) {
    e.preventDefault();

    var chart = $('#resumen_ingresos_chart');

    if (chart.length === 0) return;

    draw_resumen_ingresos_chart(chart);
  }

  // Recargar tabla de resumen de enseñanza o lecciones
  $('.recargar_resumen_enseñanza_chart').on('click', recargar_resumen_enseñanza_chart);
  function recargar_resumen_enseñanza_chart(e) {
    e.preventDefault();

    var chart = $('#resumen_enseñanza_chart');

    if (chart.length === 0) return;

    draw_resumen_enseñanza_chart(chart);
  }

  // Reiniciar el sistema
  $('#reiniciar_sistema_form').on('submit', reiniciar_sistema);
  function reiniciar_sistema(e) {
    e.preventDefault();

    var form    = $(this),
    button      = $('button', form),
    data        = new FormData(form.get(0));

    if(!confirm('¿Estás seguro?')) return false;

    $.ajax({
      url: 'ajax/reiniciar_sistema',
      type: 'post',
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      data: data,
      beforeSend: function() {
        button.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        toastr.success(res.msg, '¡Bien!');
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      button.waitMe('hide');
    })
  }

 // Dibujar gráfica de resumen de lecciones
 function draw_resumen_enseñanza_profesor_chart(element) {
  var wrapper = element.parent('div'),
  _t          = Bee.csrf,
  action      = 'get',
  hook        = 'bee_hook';

  // AJAX
  $.ajax({
    url: 'ajax/get_resumen_ensenanza_profesor',
    type: 'get',
    dataType: 'json',
    data : { _t, action, hook },
    beforeSend: function() {
      wrapper.waitMe();
    }
  }).done(function(res) {
    if(res.status === 200) {
      var myLineChart = new Chart(element, {
        type: 'bar',
        data: {
          labels: res.data.labels,
          datasets: [{
            label: "Tareas",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.5)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: res.data.data,
          }],
        },
        options: {
          maintainAspectRatio: false,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 0,
              bottom: 0
            }
          },
          scales: {
            xAxes: [{
              time: {
                unit: 'date'
              },
              gridLines: {
                display: false,
                drawBorder: false
              },
              ticks: {
                maxTicksLimit: 20
              }
            }],
            yAxes: [{
              ticks: {
                maxTicksLimit: 5,
                padding: 10
              },
              gridLines: {
                color: "rgb(234, 236, 244)",
                zeroLineColor: "rgb(234, 236, 244)",
                drawBorder: false,
                borderDash: [2],
                zeroLineBorderDash: [2]
              }
            }],
          },
          legend: {
            display: false
          },
          tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
              label: function(tooltipItem, chart) {
                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                return datasetLabel + ': ' + tooltipItem.yLabel;
              }
            }
          }
        }
      });
    } else {
      wrapper.html(res.msg);
    }
  }).fail(function(err) {
    wrapper.html('Hubo un error al cargar la información.')
  }).always(function() {
    wrapper.waitMe('hide');
  });
}

});