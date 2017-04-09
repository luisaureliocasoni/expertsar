"use strict"
function carregaAnswerPage() {
    //Remove o título
    $("#title").remove();
    //Altera o texto
    $("#licao").html("<b>Teste</b>");

    $("#next").click(null).addClass("btn-disabled");

}

$(document).ready(function(){
    $("#next").click(carregaAnswerPage);
});
