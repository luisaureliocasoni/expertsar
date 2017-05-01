$(document).ready(function() {
    $(".bt-remove").click(function (){
        var page = $(this).data("page");
        var id = $(this).data("id");
        var type = $(this).data("type");
        var resposta;
        resposta = window.confirm("Tem certeza que deseja remover esta "+type+"?\nA ação não pode ser revertida!");
        if (resposta === true){
            window.location = page+"?id="+id;
        }
    });
    //Ativa o sideNav na página
    $(".button-collapse").sideNav();
    //Evento no botão do menu do Site
    $("#btMenu").click(function(){
        $('.button-collapse').sideNav('show');
    });
})
