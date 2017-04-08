$(document).ready(function() {
    //Ativa o sideNav na página
    $(".button-collapse").sideNav();
    //Evento no botão do menu do Site
    $("#btMenu").click(function(){
        $('.button-collapse').sideNav('show');
    });
})
