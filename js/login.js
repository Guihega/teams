//var empleado;

$("#frmAcceso").on('submit',function(e)
{
	e.preventDefault();
  user=$("#email").val().toLowerCase();
  pass=$("#password").val();
  base64 = 0;
  var domNoPermitodo ='alumno';
  var domUno = 'profesor.uaemex.mx';
  var domDos = 'uaemex.mx';
  if (user.includes(domNoPermitodo)) {
    swal({
      title: "Error",
      text: "El dominio no esta permitido",
      icon: "error",
      button: "Aceptar",
    });
  }
  else{
    if (user.includes(domUno) || user.includes(domDos)) {
      $.post( "./ajax/acciones.php?op=login",{"email":user,"password":pass, "base64": base64},function(data){})
      .done(function(data) {
        var respuesta = JSON.parse(data);
        if (respuesta.error == 0)
        {
          var empleado = respuesta.empleado;
          $.post( "./ajax/acciones.php?op=datosEmpleado",{empleado},function(data)
          {
            autenticarGraph();
          })
          .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
              console.log(textStatus);
            }
          });
        }
        else
        {
          swal({
            title: "Error",
            text: respuesta.mensaje,
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
    else{
      swal({
        title: "Error",
        text: "El dominio no esta permitido",
        icon: "error",
        button: "Aceptar",
      });
    }
  }
})

function autenticarGraph(){
  window.location.replace("https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=f9b65de4-8994-4de4-bd4f-7dc64e654bbf&response_type=code&redirect_uri=https://sigeteams.uaemex.mx/panel.php&response_mode=query&scope=offline_access%20user.read%20mail.read&state=12345");
}