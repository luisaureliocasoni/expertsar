$(document).ready(function() {
    $(".bt-remove").click(function (){
        var page = $(this).data("page");
        var id = $(this).data("id");
        var type = $(this).data("type");
        var resposta;
        resposta = window.alert("Tem certeza que deseja remover esta "+type+"?");
        console.log("resposta");
    });
    //Ativa o sideNav na página
    $(".button-collapse").sideNav();
    //Evento no botão do menu do Site
    $("#btMenu").click(function(){
        $('.button-collapse').sideNav('show');
    });
})
