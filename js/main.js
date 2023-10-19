$(document).ready(function() {

  // Toast para notificaciones
  //toastr.warning('My name is Inigo Montoya. You killed my father, prepare to die!');

  // Waitme
  //$('body').waitMe({effect : 'orbit'});
  console.log('////////// Bienvenido a Bee Framework Versi�n ' + Bee.bee_version + ' //////////');
  console.log('//////////////////// www.joystick.com.mx ////////////////////');
  console.log(Bee);

  /**
   * Prueba de peticiones ajax al backend en versi�n 1.1.3
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
      toastr.error('Prueba AJAX fallida.', '�Upss!');
    }).always(function() {
      body.waitMe('hide');
    })
  }
  
  /**
   * Alerta para confirmar una acci�n establecida en un link o ruta espec�fica
   */
  $('body').on('click', '.confirmar', function(e) {
    e.preventDefault();

    let url = $(this).attr('href'),
    ok      = confirm('�Est�s seguro?');

    // Redirecci�n a la URL del enlace
    if (ok) {
      window.location = url;
      return true;
    }
    
    console.log('Acci�n cancelada.');
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
  
  // Inicializaci�n de elementos
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
        emptyTable:     "No hay informaci�n.",
        paginate: {
          first:      "Primera",
          previous:   "Anterior",
          next:       "Siguiente",
          last:       "�ltima"
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

    // Validar que este seleccionada una opci�n type
    if(type === 'none') {
      toastr.error('Selecciona un tipo de movimiento v�lido', '�Upss!');
      return;
    }

    // Validar description
    if(description === '' || description.length < 5) {
      toastr.error('Ingresa una descripci�n v�lida', '�Upss!');
      return;
    }

    // Validar amount
    if(amount === '' || amount <= 0) {
      toastr.error('Ingresa un monto v�lido', '�Upss!');
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
        toastr.success(res.msg, '�Bien!');
        form.trigger('reset');
        bee_get_movements();
      } else {
        toastr.error(res.msg, '�Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petici�n', '�Upss!');
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
        toastr.error(res.msg, '�Upss!');
        wrapper.html('');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petici�n', '�Upss!');
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
        toastr.error(res.msg, '�Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petici�n', '�Upss!');
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

    // Validar que este seleccionada una opci�n type
    if(type === 'none') {
      toastr.error('Selecciona un tipo de movimiento v�lido', '�Upss!');
      return;
    }

    // Validar description
    if(description === '' || description.length < 5) {
      toastr.error('Ingresa una descripci�n v�lida', '�Upss!');
      return;
    }

    // Validar amount
    if(amount === '' || amount <= 0) {
      toastr.error('Ingresa un monto v�lido', '�Upss!');
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
        toastr.success(res.msg, '�Bien!');
        form.trigger('reset');
        form.remove();
        add_form.show();
        bee_get_movements();
      } else {
        toastr.error(res.msg, '�Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petici�n', '�Upss!');
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

    if(!confirm('�Est�s seguro?')) return false;

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
        toastr.error(res.msg, '�Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petici�n', '�Upss!');
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
        toastr.success(res.msg, '�Bien!');
        bee_get_movements();
      } else {
        toastr.error(res.msg, '�Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petici�n', '�Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }

//********************************FUNCIONES PARA MATERIAS_PROFESORES********************************** */
//FUNCION QUE CARGA EL LISTADO DE MATERIAS DISPONIBLES
function get_materias_disponibles_profesor() {
  
  var form = $('#profesor_asignar_materia_form'),
  select = $('select', form),
  id_profesor =$('input[name="id"]', form).val(),
  wrapper = $('profesor_materias'),
  opciones = '',
  action = 'get',
  hook = 'bee_hook';

  if(form.length == 0) return;

  //LIMPIAR LAS OPCIONES CUANDO SE CARGUE
  select.html('');

  //CODIGO PARA CARGAR LAS MATERIAS CON AJAX
  $.ajax({
    url: 'ajax/get_materias_disponibles-profesor',
    type: 'get',
    dataType: 'json',
    data : {
      '_t' : Bee.csrf,
      id_profesor,
      action,
      hook
    },
    //PETICION ASINCRONA
    beforeSend: function(){
      wrapper.waitMe();
    }
  }).done(function(res){
    //indica que si el status es 200 que todo est� ok
    if(res.status === 200){
      //condicionamos si existe una materia disponible
      if(res.data.length === 0){
        select.html('<option disabled selected>No hay opciones disponibles.</option>')
        $('button', form).attr('disabled', true);
        return;
      }
      //iteramos en cada una de las materias
      $.each(res.data, function(i,m){
        opciones += '<option value="'+m.id+'">'+m.nombre+'</option>';
      });
      
      select.html(opciones);
      $('button', form).attr('disabled', false);
    
    }else{
      select.html('<option disabled selected>No hay Opciones Disponibles.</option>')
      $('button',form).attr('disabled', true);
      toastr.error(res.msg, 'Upss!');
    }
  }).fail(function(err){
    toastr.error('Hubo un error en la petici�n.', '�Upss!');
  }).always(function(){
    wrapper.waitMe('hide');
  })
}
//EJECUTAMOS LA FUNCION
get_materias_disponibles_profesor();

//FUNCION PARA CARGAR DE FORMA DINAMICA LAS MATERIAS
//cargamos un bloque de contenido html que inyectamos dentro del wrapper del form 
function get_materias_profesor(){
  var wrapper = $('.wrapper_materias_profesor'),
  id_profesor = wrapper.data('id'),
  //variables obligatorias del framework para comunicarnos con ajax
  action = 'get',
  hook = 'bee_hook';

  if(wrapper.length == 0) return;

  //CODIGO AJAX PARA CARGAR LAS MATERIAS
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
    beforeSend: function(){
      wrapper.waitMe();
    }
    //promesas asincronas
  }).done(function(res){
    if(res.status === 200){
      wrapper.html(res.data);
    }else{
      wrapper.html(res.msg);
      toastr.error(res.msg, '¡Upss!');
    }
  }).fail(function(err){
    toastr.error('Hubo un error en la peticion.','¡Upss!');
  }).always(function(){
    wrapper.waitMe('hide');
  })
}
//EJECUTAMOS LA FUNCION
get_materias_profesor();

//FUNCION PARA AGREGAR MATERIAS CON AJAX 
$('#profesor_asignar_materia_form').on('submit', add_materia_profesor);
//la peticion no se hace directo al servidor sino que se hace a trave de ajax
function add_materia_profesor(e){
  e.preventDefault();
  //inicializamos variables
  var form = $('#profesor_asignar_materia_form'),
  select = $('select', form),
  id_materia = select.val(),
  id_profesor = $('input[name="id"]', form).val(),
  csrf = $('input[name="csrf"]', form).val(),
  action = 'post',
  hook = 'bee_hook';

  if(id_materia === undefined || id_materia === ''){
    toastr.error('Selecciona una materia valida');
    return;
  }

  //FUNCION AJAX
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
    beforeSend: function(){
      form.waitMe();
    }
  }).done(function(res){
    if(res.status === 201){
      toastr.success(res.msg);
      get_materias_disponibles_profesor();
      get_materias_profesor();
    }else{
      toastr.error(res.msg, '�Upss!');
    }
  }).fail(function(err){
    toastr.error('Hubo un error en la petici�n.', '�Upss!');
  }).always(function(){
    form.waitMe('hide');
  })
}

//BORRAR MATERIAS CON AJAX DEL FRONTEND
//los elementos que se cargan post la carga completa del sitio, se debe seleccionar de una manera diferente con body
$('body').on('click', '.quitar_materia_profesor', quitar_materia_profesor);
function quitar_materia_profesor(e) {
  e.preventDefault();
// this se refiere al elemento o boton al que le estamos dando clic en una lista de elementos iguales 
  var btn = $(this),
  wrapper = $('.wrapper_materias_profesor'),
  csrf = Bee.csrf,
  id_materia = btn.data('id'),
  id_profesor = wrapper.data('id'),
  li = btn.closest('li'),
  action = 'delete',
  hook = 'bee_hook';

  //realizamos la confirmaci�n
  if(!confirm('¿Estas seguro?')) return false;

  //CODIGO AJAX PARA LIMINAR EL FRONTEND LA MATERIA
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
    beforeSend: function(){
      //aparece un cargador solo en el elemento li
      li.waitMe();
    }
  }).done(function(res){
    if(res.status === 200){
      toastr.success(res.msg, 'Bien!');
      li.fadeOut(); //se va a borrar ese li es decir la materia
      get_materias_disponibles_profesor();
      get_materias_profesor();
    }else{
      toastr.error(res.msg,'¡Upss!');
    }
  }).fail(function(){
    toastr.error('Hubo un error en la petición','¡Upss!');
  }).always(function(){
    li.waitMe('hide'); 
  })
}
});

//****FUNCION PARA VALIDAR RECAPTCHA**** */
function checkRecaptcha() {
  var recaptchaResponse = grecaptcha.getResponse();
  if (recaptchaResponse.length === 0) {
      document.getElementById('recaptcha-error').style.display = 'block';
      return false;
  } else {
      document.getElementById('recaptcha-error').style.display = 'none';
      return true;
  }
}

// Otras funciones personalizadas aquí...
// Cargar materias disponibles para grupo
function get_materias_disponibles_grupo(){

  var form = $('#grupo_asignar_materia_form'),
  select = $('select', form),
  id_grupo = $('input[name="id_grupo"]', form).val(),
  wrapper = $('.wrapper_materias_grupo'),
  opciones = '',
  _t = Bee.csrf,
  action = 'get',
  hook = 'bee_hook';

  if(form.length == 0) return;

  //LIMPIAMOS LAS OPCIONES AL MOMENTO DE CARGAR
  select.html('');

  //CODIGO AJAX
  $.ajax({
    url: 'ajax/get_materias_disponibles_grupo',
    type: 'get',
    dataType: 'json',
    data: {
      _t,
      id_grupo,
      action,
      hook
    },
    beforeSend: function(){
      wrapper.waitMe();
    }
  }).done(function(res){
    if(res.status === 200){
      if(res.data.length === 0){
        select.html('<option disabled selected>No hay Opciones Disponibles.</option>')
        $('button',form).attr('disabled', true);
        return;
      }
      $.each(res.data, function(i,m){
        opciones += '<option value="'+m.id+'">'+m.materia+' Asignada A: '+m.profesor+'</option>';
      });
      select.html(opciones);
      $('button', form).attr('disabled', false);
    }else{
      select.html('<option disabled selected>No hay Opciones Disponibles.</option>')
      $('button',form).attr('disabled', true);
      toastr.error(res.msg, 'Upss!');    
    }
  }).fail(function(err){
    toastr.error('Hubo un error en la petición.', 'Upss!');
  }).always(function(){
    wrapper.waitMe('hide');
  })
}
get_materias_disponibles_grupo();

//FUCIÓN PARA CARGAR LAS MATERIAS QUE SE VAN ASIGNANDO A UN GRADO O SECCIÓN
function get_materias_grupo(){

  var wrapper = $('.wrapper_materias_grupo'),
  id_grupo = wrapper.data('id'),
  _t = Bee.csrf,
  action = 'get',
  hook = 'bee_hook';

  if(wrapper.length == 0) return;

  //FUNCIÓN AJAX PARA JALAR LOS DATOS AL FRONTEND
  $.ajax({
    url: 'ajax/get_materias_grupo',
    type: 'get',
    dataType: 'json',
    data: {
      _t,
      id_grupo,
      action,
      hook
    },
    beforeSend: function(){
      wrapper.waitMe();
    }
  }).done(function(res){
    if(res.status === 200){
      wrapper.html(res.data);
    }else{
      wrapper.html(res.msg)
      toastr.error(res.msg, 'Upss!');
    }
  }).fail(function(err){
    toastr.error('Hubo un error en la petición.', 'Upss!');
  }).always(function(){
    wrapper.waitMe('hide');
  })
}
get_materias_grupo();

//AGREGAR MATERIA AL GRADO
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

//********FUNCION AJAX PARA CARGAR LOS ALUMNOS DE UNA CLASE**************/
function get_alumnos_grupo(){
  var wrapper = $('.wrapper_alumnos_grupo');
  id_grupo = wrapper.data('id'),
  _t = Bee.csrf,
  action = 'get',
  hook = 'bee_hook';

  if(wrapper.length == 0) return;

  //FUNCION AJAX
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
    beforeSend: function(){
      wrapper.waitMe();
    }
  }).done(function(res){
    if(res.status === 200){
      wrapper.html(res.data);
    }else {
      wrapper.html(res.msg);
      toastr.error(res.msg, 'Ups!');
    }
  }).fail(function(err){
    toastr.error('Hubo un error en la petición.', 'Upss!');
  }).always(function(){
    wrapper.waitMe('hide');
  })
}
get_alumnos_grupo();

//*************FUNCION AJAX PARA QUITAR EL ALUMNO, OSEA QUITAR INSCRIPCION DEL GRUPO********** */
// Quitar ALUMNO de grupo
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

  //******FUNCIÓN PARA SUSPENDER A UN ALUMNO********/
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
        //se vuelven a cargar los alumnos
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