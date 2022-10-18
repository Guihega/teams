// var grupos= new Array();
var grupos= [];
var valores="";
var trId="trRow";
var numGpo;
var organismo;
var programa;
var clave;
var unidad;
var grupo;
var datosCursotTlb  = [];
var objCursosTbl = {};
var objAlumnosCorreo = [];
var objCorreosAlumnos = {};
var objAlumnos = [];
var numGrupo;
var numeroGrupo;
//location.reload();
$('.btnSalir').on('click', function(e) {
  e.preventDefault();
  salir();
});

$('.btnCrearGrupo').on('click', function(e) {
  e.preventDefault();
  objAlumnos = [];
  var strGpos = grupos.toString();
  $.post( "./ajax/acciones.php?op=crearGpos",{strGpos},function(data){
    objAlumnos = JSON.parse(data);  
    //obtenner tooken
    $('#cargando').fadeIn();
    //generarToken();
    crearGrupos();
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
});

$("#modalConfirm").on("hidden.bs.modal", function () {
  location.reload();
});

$(".cursos").change(function() {
  grupos = [];
  datosCursotTlb  = [];
  $(".cursos").each(function(){
    trId="#trRow";
    if(this.checked) {
      numGpo = "";
      organismo = "";
      programa = "";
      clavePer = "";
      nomMateria = "";
      claveGpo = "";
      trId +=$(this).val();
      numGpo = $(this).val();
      $("#tblCursos").find(trId).find("td").each(function(index) {
        switch (index) 
        {
          case 1:
            organismo = $(this).html();
            break;
          case 2:
            programa = $(this).html();
            break;
          case 3:
            clavePer = $(this).html();
            break;
          case 4:
            nomMateria = $(this).html();
            break;
          case 5:
            claveGpo = $(this).html();
            break;
        }
      });
      datosCursotTlb.push({ 
        //"unidad":unidad,
        "clavePer" : clavePer,
        "claveGpo":claveGpo,
        "nomMateria": nomMateria,
        "numGpo": numGpo,
      });

      objCursosTbl = datosCursotTlb;
      grupos.push($(this).val());
    }
  });
});

function salir(){
  $.post( "./ajax/acciones.php?op=salir",function(data){
    if (data = "0") {
      window.location.replace('https://sigeteams.uaemex.mx/');
    }
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
}

function generarToken(){
  $.post( "./ajax/acciones.php?op=obtenerToken",function(data){
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
}

function crearGrupos(){
  $('#lblMensajeModal').empty().html('Los grupos han sido creados y los usuarios asignados');
  $('#mensajeLoader').empty().html('Creando grupos...');
  $.post( "./ajax/acciones.php?op=crearGrupos",{objCursosTbl},function(data){
    //Obtener grupos y buscar los seleccionados
    listarGrupos();
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
}

function listarGrupos(){
  $.post( "./ajax/acciones.php?op=listarGpos",function(data){
    var gposTeam = JSON.parse(data);
    
    if (gposTeam.value != null) {
      for (var i = 0; i < gposTeam.value.length; i++) {
        for (var j = 0; j < objCursosTbl.length; j++) {
          if (gposTeam.value[i]["description"] == objCursosTbl[j]["numGpo"]) {
            objCorreosAlumnos = [];
            for (var k = 0; k < objAlumnos.length; k++) {
              if (gposTeam.value[i]["description"] == objCursosTbl[j]["numGpo"] & objCursosTbl[j]["numGpo"] == objAlumnos[k]["curso"]) {
                var id = gposTeam.value[i]["id"];
                objAlumnosCorreo = [];
                for (var l = 0; l < objAlumnos[k]["alumnos"]["EMAILUAEM"].length; l++) {
                  objAlumnosCorreo.push({ 
                    "correo":objAlumnos[k]["alumnos"]["EMAILUAEM"][l]
                  });
                }
                objCorreosAlumnos = objAlumnosCorreo;
                agregarAlumnos(id,objCorreosAlumnos)
              }
            }
          }
        }
      }
    }
    else{
      swal({
        title: "Error",
        text: 'La session ha caducado, favor de ingresar de nuevo',
        icon: "error",
        button: "Aceptar",
      });
    }
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
}

function agregarAlumnos(id,objCorreosAlumnos){
  $.post("./ajax/acciones.php?op=agregarAlumnos",{"id":id,"alumnos": objCorreosAlumnos},function(data)
  {
  })
  .done(function() {
    $('#cargando').fadeOut();
    $('#modalConfirm').modal('show');
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
}

function actualizarGpo(numGpo){
  $('#lblMensajeModal').empty().html('El grupo, o los grupos, han sido actualizados correctamente.');
  $('#mensajeLoader').empty().html('Actualizando grupo...');
  swal({
    title: "¿Confirma?",
    text: "Si ha agregado alumnos de manera manual en el equipo de TEAMS y no están en el sistema de control escolar, serán eliminados del equipo. ¿Desea actualizar el listado de alumnos conforme a los inscritos en el SICDE?",
    icon: "warning",
    buttons: {
      defeat: {text:'Si',className:'btn-success'},
      cancel: "No",
    },
    dangerMode: true,
  })
  .then((aceptar) => {
    if (aceptar) {
      $('#cargando').fadeIn();
      $.post( "./ajax/acciones.php?op=actualizarGpo",{"numGpo":numGpo},function(data){
      })
      .done(function() {
        $('#cargando').fadeOut();
        $('#modalConfirm').modal('show');
      })
      .fail(function(data) {
        swal({
          title: "Error",
          text: data,
          icon: "error",
          button: "Aceptar",
        });
      });
    }
  });
  return false;
}

$( document ).ready(function() {
  $( ".bloqueado" ).prop( "disabled", true );
  var gruposListados = $("#tblCursos").find('tbody tr').length;
  var gruposCreados = 0;
  var numItemsTeam = 0;
  $("#tblCursos").find("tr").find(".numItemsTeam").each(function(index) {
    numItemsTeam = parseInt($(this).html());
    if (numItemsTeam > 0) {
      gruposCreados = gruposCreados + 1;
    }
  });
  if (gruposCreados == gruposListados) {
    $( ".btnCrearGrupo" ).prop( "disabled", true );
  }
  //mostrarDatosTabla();
});

function mostrarDatosTabla(){
  $.post( "./ajax/acciones.php?op=mostrarDatos",function(data){
    var datosTablas = JSON.parse(data);
    $('#tblBodyCursos').empty();
    for (var i = 0 - 1; i < datosTablas.CVEGPO.length; i++) {
      $('#tblBodyCursos').append('<tr><td></td></tr>');
    }
  })
  .fail(function(data) {
    swal({
      title: "Error",
      text: data,
      icon: "error",
      button: "Aceptar",
    });
  })
}