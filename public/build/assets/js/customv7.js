var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host;
var original_position_x = 0;
var original_position_y = 0;
var notif_initial_qty = false;
var notif_qty = 0;
var traduccion_datatable = {
    "sProcessing":     "Procesando...",
    "sLengthMenu":     "Mostrar _MENU_ registros",
    "sZeroRecords":    "No se encontraron resultados",
    "sEmptyTable":     "Ningún dato disponible en esta tabla",
    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix":    "",
    "sSearch":         "Buscar:",
    "sUrl":            "",
    "sInfoThousands":  ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst":    "Primero",
        "sLast":     "Último",
        "sNext":     "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    }
};

function isIE() {
    return ua = navigator.userAgent, ua.indexOf("MSIE ") > -1 || ua.indexOf("Trident/") > -1
}
jQuery(document).ready(function() {
    $('.dropdown-submenu a.test').on("click", function(e){
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });

    isIE() && swal("Navegador no soportado", "Para poder funcionar de forma optima y segura Aleph Manager necesita ser utilizado en navegador actual. Muchas de las funcionalidades se encuentran deshabilitadas en este navegador. Alguno de los navegadores soportados son: Chrome, Firefox, Edge y Opera", "warning"), setTimeout(function() {
        $(".alert-header") && $(".alert-header").remove()
    }, 1e4);

    $(document).on("click", ".colapsable-aleph", function() {
        arrow_span = $(this).find('span');

        if($(arrow_span).hasClass('glyphicon-chevron-right')){
            $(arrow_span).removeClass('glyphicon-chevron-right');
            $(arrow_span).addClass('glyphicon-chevron-down');
        }else{
            $(arrow_span).removeClass('glyphicon-chevron-down');
            $(arrow_span).addClass('glyphicon-chevron-right');
        }
    });

    $(".usuarios_autocomplete").autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: baseUrl+"/usuarios/autocomplete",
                type: 'post',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function( data ) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $(this).val(ui.item.label); // display the selected text
            return false;
        }
    });

    // notificaciones();
    // setInterval(()=>{
    //     notificaciones();
    // }, 5000);
});
var scroll_keys = {
    37: 1,
    38: 1,
    39: 1,
    40: 1
};

function preventDefault(e) {
    (e = e || window.event).preventDefault && e.preventDefault(), e.returnValue = !1
}

function preventDefaultForScrollKeys(e) {
    if (scroll_keys[e.keyCode]) return preventDefault(e), !1
}

function disableScroll() {
    original_position_x = $(window).scrollTop();
    original_position_y = $(window).scrollLeft();

    document.body.scrollTop = 0, document.documentElement.scrollTop = 0, $("body").addClass("stop-scrolling"), $("body").bind("touchmove", function(e) {
        e.preventDefault()
    })
}

function enableScroll() {
    $("body").removeClass("stop-scrolling"), $("body").unbind("touchmove");
    window.scroll(original_position_y,original_position_x);
}

function show_loading() {
    disableScroll(), $("#cargando").show()
}

function hide_loading() {
    enableScroll(), $("#cargando").hide()
}

function show_loading_select() {
    $('select, input').each(function() {
        // Guarda el estado de deshabilitación original
        $(this).data('originally-disabled', $(this).prop('disabled'));
        $(this).prop('disabled', true);
    });
}

function hide_loading_select() {
    $('select, input').each(function() {
        // Restaura el estado de deshabilitación original
        if ($(this).data('originally-disabled') !== undefined) {
            $(this).prop('disabled', $(this).data('originally-disabled'));
        }
    });
}

function validar_columna_importador(){
    //var valor_celda = '';
    //
    //var codigo = 0;
    //$('.celda-importador').each(function(){
    //    valor_celda = $(this).val();
    //
    //    var letras_permitidas = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "Y", "X", "Z",
    //                             "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "y", "x", "z"];
    //
    //    if(letras_permitidas.includes(valor_celda)){
    //        return true;
    //    }else{
    //        swal("Aviso", "Carácter no permitido","warning");
    //        return false;
    //    }
    //});
};

function validar_solo_letras_y_numeros(event){
    var key = event.keyCode;

    if(key == null){
        return true;
    }

    if((key >= 48 && key <= 57) || (key >= 65 && key <= 90) || (key >= 97 && key <= 122) || key == 8 || key == 32 || key == 16){
        return true;
    }else{
        swal("Aviso", "Caracter no permitido","warning");
        event.preventDefault();
        return false;
    }
};

function validar_solo_letras_palabra(input){
    var palabra = $(input).val();
    var code = "";

    for(var i = 0; i < palabra.length; i++){
        code = ascii(palabra.charAt(i));
        if((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || code == 32){
        }else{
            swal("Aviso", "Caracter '"+palabra.charAt(i)+"' no permitido","warning");
            event.preventDefault();
            $(input).val('');['tipo']
            return false;
        }
    }

}

function ascii(a){
    return a.charCodeAt(0);
}

function generate_data_table(table_id, table_title){
    $('#'+table_id).DataTable({
        "pageLength": 20,
        dom: 'Bfrtip',
        buttons: [
            {"extend": 'pdf', "text":'Export',"exportOptions":{columns: 'th:not(.noExport)'}, "className": 'btn btn-danger', title: table_title},
            {"extend": 'copy', "text":'Export',"exportOptions":{columns: 'th:not(.noExport)'}, "className": 'btn btn-primary', title: table_title},
            {"extend": 'excel', "text":'Export',"exportOptions":{columns: 'th:not(.noExport)'}, "className": 'btn btn-success', title: table_title},
            {"extend": 'print', "text":'Export',"exportOptions":{columns: 'th:not(.noExport)'}, "className": 'btn btn-secondary', title: table_title}
        ],
        initComplete: function () {
            $('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
            $('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
            $('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
            $('.buttons-print').html('<span class="glyphicon glyphicon-print" data-toggle="tooltip" title="'+table_title+'"/> Imprimir');
        },
        language: traduccion_datatable
    });
}

function download_from_uri(file_url){
    var download_button  = '<a href="'+file_url+'" download="" id="download_button"></a>';
    $("body").append(download_button);
    document.getElementById("download_button").click();
}

function cambiar_entidad(url){
    $.ajax({
        url : baseUrl+"/usuarios/generar_login_entidad",
        type: "POST",
        data: {url_destino:url},
        dataType: "JSON",
        success: function(data)
        {
            if(data){
                window.location.replace(url+"/usuarios/login?ucode="+data+"&path="+window.location.pathname);
            }else{
                swal("Aviso", "Ocurrió un error al intentar redirigir al ambiente seleccionado.", "warning");
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            show_ajax_error_message(jqXHR, textStatus, errorThrown);
        }
    });
}

function generar_acceso_foro(){
    $.ajax({
        url : baseUrl+"/usuarios/generar_acceso_foro",
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
            if(data){
                window.location.replace(data.url+"/usuarios/login?ucode="+data.ucode);
            }else{
                swal("Aviso", "Ocurrió un error al intentar redirigir al foro virtual.", "warning");
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            show_ajax_error_message(jqXHR, textStatus, errorThrown);
        }
    });
}

function validar_usuario(id_elemento){
    var usuario = $('#'+id_elemento).val();

    show_loading();
    $.ajax({
        url : baseUrl+"/usuarios/ajax_validar_usuario",
        type: "POST",
        data: {usuario:usuario},
        dataType: "JSON",
        success: function(data)
        {
            hide_loading();
            return true;
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            show_ajax_error_message(jqXHR, textStatus, errorThrown);
        }
    });
}

function show_ajax_error_message(jqXHR, textStatus = false, errorThrown = false, recarga_pagina = false){
    hide_loading();

    //Obtengo el estado
    if(jqXHR.status){
        switch (jqXHR.status) {
            case 524:
                var mensaje = "Se esta procesando la petición, recibirá una notificación al finalizar, si esto no ocurre contáctese con soporte@alephmanager.com";
                break;
            case 504:
                var mensaje = "Se esta procesando la petición, recibirá una notificación al finalizar, si esto no ocurre contáctese con soporte@alephmanager.com";
                break;
            case 500:
                var mensaje = "Ocurrió un error interno, recargue la página y vuelva a intentar más tarde, si este error persiste contáctese con soporte@alephmanager.com";
                break;
            default://Por defecto traigo el error del controller
                if(jqXHR.responseText){
                    //Si tiene mas de 500 caracteres posiblemente sea un redirect
                    if(jqXHR.responseText.length > 1000){
                        var mensaje = "Ocurrió un error interno, recargue la página y vuelva a intentar más tarde, si este error persiste contáctese con soporte@alephmanager.com";
                    }else{
                        if(jqXHR.responseText == 'Sesión de usuario caducada'){
                            swal("Aviso", jqXHR.responseText, "warning").then(function(){
                                location.replace(baseUrl);
                            });
                        }else{
                            mensaje = jqXHR.responseText;
                        }
                    }
                }else{
                    var mensaje = "Ocurrió un error interno, recargue la página y vuelva a intentar más tarde, si este error persiste contáctese con soporte@alephmanager.com";
                }
                
                break;
        }
    }else{
        var mensaje = "Ocurrió un error interno, recargue la página y vuelva a intentar más tarde, si este error persiste contáctese con soporte@alephmanager.com";
    }

    if(recarga_pagina){
        swal("Aviso", mensaje, "warning").then(function(){location.reload();});
    }else{
        swal("Aviso", mensaje, "warning");
    }
}

function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}
/**************************** NOTIFICACIONES ****************************/
function show_notification_box(){
    if($('#notification_box').is(":hidden")){
        $('#notification_box').show('fast');
    }else{
        $('#notification_box').hide('fast');
    }
}

$(document).mouseup(function(e){
    var container = $("#notification_box");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0){
        if($(e.target).prop("id") != 'notification_bell'){
            container.hide('fast');
        }
    }

    return true;
});

/* function notificaciones(){
    $.ajax({
        url : baseUrl+"/notificaciones/ajax_get_notifications",
        type: "POST",
        data: {script:true},
        dataType: "JSON",
        success:function(data){
            $('#notification_content').html('');
            notif_qty = data.length;

            if(notif_qty == 0){
                $("#notification-counter").hide();
            }else{
                $("#notification-counter").show();
                $("#notification-counter span").text(notif_qty);
            }

            if(!notif_initial_qty){
                notif_initial_qty = notif_qty;
            }

            var box_content = "";
            if(data.length > 0){
                box_content += '<ul class="notification-list">';
                var item_style = "";

                data.forEach((item, pos) =>{
                    if(item.status == 0){
                        item_style = 'opened';
                    }else{
                        item_style = 'unopened';
                    }

                    if(item.asunto){
                        asunto = item.asunto;
                    }else{
                        asunto = 'Aviso del sistema';
                    }

                    box_content += '<a class="notification-link '+item_style+'" id="notif_box_'+item.id+'" onclick="ver_detalle_noti('+item.id+')"><li class="notification-list-item"><span title="'+asunto+'" class="asunto">'+asunto+'</span><span class="status '+item_style+'"></span></li></a>';
                });

                box_content += '</ul>';

                $(".notification-bell").removeClass("notify" );

                if(notif_qty > notif_initial_qty){
                    $('#notification_sound')[0].play();
                    $(".notification-bell").addClass("notify" );
                }

                notif_initial_qty = notif_qty;

                $('#notification_content').html(box_content);
            }else{
                sin_notificaciones();
            }
        },
        error:function(){}
    });
} */

function marcar_notificaciones_leidas(){
    $.ajax({
        url : baseUrl+"/notificaciones/ajax_marcar_como_leidas",
        type: "GET",
        dataType: "JSON",
        success:function(data){
            sin_notificaciones();
        },
        error:function(){
            swal("Aviso", "Ocurrió un error, intentelo nuevamente más tarde" , "warning");
        }
    });
}

function sin_notificaciones(){
    $("#notification-counter").hide();
    var box_content = '<div class="sin_notif_bell"><i class="fas fa-bell"></i></div><div class="sin_notif_text">No tienes notificaciones sin leer</div>';

    $('#notification_content').html(box_content);
}

function ver_detalle_noti(id,vista_notificaciones = false){
    show_loading();

    $.ajax({
        url : baseUrl+"/notificaciones/ajax_detalle_noti/"+id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            hide_loading();

            if(vista_notificaciones){
                cambiar_estado_row_notificacion(id);
            }else{
                $('#notif_box_'+id).remove();

                //Remuevo 1 qty notif
                notif_qty--;

                if(notif_qty == 0){
                    $("#notification-counter").hide();
                }else{
                    $("#notification-counter").show();
                    $("#notification-counter span").text(notif_qty);
                }
            }

            $('#notif_content_fecha').text(data.fecha_creacion);
            if(data.emisor){
                $('#notif_content_usuario').text(data.emisor);
            }else{
                $('#notif_content_usuario').text('Mensaje del sistema');
            }

            if(data.asunto){
                $('#notif_content_asunto').text(data.asunto);
            }else{
                $('#notif_content_asunto').text('Aviso del sistema');
            }

            $('#notif_content_mensaje').html(data.mensaje);
            $('#modal_notificacion').modal('show');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            show_ajax_error_message(jqXHR, textStatus, errorThrown);
        }
    })
}

function crear_notificacion(es_respuesta = false){
    $('#modal_notificacion').modal('hide');
    $('#form-notif')[0].reset();

    $('#notif_destinatario').prop('disabled', false);
    //Primero cargo los usuarios
    var usuarios = $('#notif_destinatario');

    show_loading();
    $.ajax({
        url: baseUrl+"/usuarios/get_usernames?>",
        type: "POST",
        data:{length:'',start:0},
        dataType: 'JSON',
        success: function (data) {
            hide_loading();
            var fuente_user = new Array();
            //clear the current content of the select
            data.forEach((item, pos)=>{
                fuente_user.push(item.username);
            });
            if(fuente_user.length > 0){
                usuarios.autocomplete({
                    source:fuente_user
                });
            }else{
                usuarios.prop('placeholder', 'No hay usuarios disponibles');
                usuarios.prop('disabled', true);
            }
        },
        error: function () {
            hide_loading();
            usuarios.prop('placeholder', 'No hay usuarios disponibles');
            usuarios.prop('disabled', true);
        }
    });

    $('#modal_crear_notificacion').modal('show');
}

function marcar_todas_leidas(){
    if(notif_qty == 0){
        return true;
    }

    show_loading();

    $.ajax({
        url : baseUrl+"/notificaciones/ajax_marcar_leidas",
        type: "POST",
        dataType: "JSON",
        data:{
            'todas': true
        },
        success: function(data)
        {
            hide_loading();
            sin_notificaciones();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            show_ajax_error_message(jqXHR, textStatus, errorThrown);
        }
    });
}

function enviar_notificacion(){
    var user_emisor_id = $('#user_emisor_id').val();
    var notif_destinatario = $('#notif_destinatario').val();
    var asunto = $('#notif_asunto').val();
    var mensaje = $('#notif_mensaje').val();

    if(!user_emisor_id || !notif_destinatario || !asunto || !mensaje){
        swal("Aviso", "Ocurrió un error, recargue la página e intente nuevamente", "warning");
        return false;
    }

    $.ajax({
        url: baseUrl+"/notificaciones/ajax_enviar_noti",
        type:"POST",
        data:{
            user_emisor_id: user_emisor_id,
            notif_destinatario: notif_destinatario,
            asunto: asunto,
            mensaje: mensaje
        },
        dataType: "JSON",
        success: function(data){
            swal("Aviso", "Notificación enviada" , "success");
            $('#form-notif')[0].reset();
            $('#modal_crear_notificacion').modal('hide');
        },
        error:function(jqXHR, textStatus, errorThrown){
            var mensaje = "Ocurrió un error al enviar la notificación.";
            if(jqXHR.responseText){
                mensaje = jqXHR.responseText;
            }
            if(mensaje != ""){
                swal("Aviso", mensaje, "warning");
            }
        }
    });
}

/**************************** FIN NOTIFICACIONES ****************************/

function validar_fecha_dd_mm_yyyy(campo_fecha, valor_min = false){
    var fecha_validar = $(campo_fecha).val();
    var fecha_actual = new Date();

    var reg = /(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d/;
    if (fecha_validar.match(reg)){
        if(valor_min){
            fecha_validar = fecha_validar.split("/");
            var fecha_formulario = new Date( fecha_validar[2], fecha_validar[1] - 1, fecha_validar[0]);
            var time_fecha_form = fecha_formulario.getTime();
            var time_fecha_actual = fecha_actual.getTime();

            if(time_fecha_form < time_fecha_actual){
                swal("Aviso", "La fecha debe ser mayor a la actual", "warning");
                $(campo_fecha).val('');
                return false;
            }else{
                return true;
            }
        }

        return true;
    }else{
        swal("Aviso", "Debe ingresar la fecha en formato dd/mm/yyyy", "warning");
        $(campo_fecha).val('');
        return false;
    }
}