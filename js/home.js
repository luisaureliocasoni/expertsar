$(document).ready(function() {
    $(".bt-remove").click(function (){
        var page = $(this).data("page");
        var id = $(this).data("id");
        var type = $(this).data("type");
        var resposta;
        resposta = window.confirm("Tem certeza que deseja remover esta "+type+"?\nA ação não pode ser revertida!");
        if (resposta === true){
            if (page.endsWith(".php")){
                window.location = page+"?id="+id;
            }else{
                window.location = page+"&id="+id;
            }
        }
    });
    //Ativa o sideNav na página
    $(".button-collapse").sideNav();
    //Evento no botão do menu do Site
    $("#btMenu").click(function(){
        $('.button-collapse').sideNav('show');
    });
    
    //Este código prevê que sistema não se deslogue de forma abrupta
    var refreshTime = 600000; // every 10 minutes in milliseconds
    window.setInterval( function() {
        $.ajax({
            cache: false,
            type: "GET",
            url: "refresh.php",
            success: function(data) {
            },
            error: function(data, status, error){
                console.log("Erro ao atualizar sessão: " + error);
            }
        });
    }, refreshTime );
})
