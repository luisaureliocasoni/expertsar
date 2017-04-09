"use strict"

function defineInsertButtonEvent() {
    //Evento de clique do botao de inserção no painel
    $(".insert").click(function() {
        //Acessa a proprieidade data do botão
        var dataFor = $(this).data().for;
        //console.log(dataFor);
        var $for = $(dataFor);
        //console.log($for);
        $for.val($for.val() + ($(this).text()));
    });
}


function carregaAnswerPage() {
    //Oculta o título e o texto da lição
    $("#title").hide();
    $("#licao").hide();

    //Exibe a pergunta 1
    $("#pergunta1").removeClass("hide");
    $("#pergunta1").show();
    //Marca o box como atual
    $("#pergunta1").addClass("atual");

    $("#next").click(null).addClass("disabled");

}

$(document).ready(function(){
    $("#next").click(carregaAnswerPage);
    defineInsertButtonEvent();
});
