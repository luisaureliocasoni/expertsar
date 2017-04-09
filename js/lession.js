"use strict"

function carregaAnswerPage() {
    //Remove o título
    $("#title").remove();
    //Altera o texto
    $("#licao").html("");

    $("#licao").append("<h3>Faça o exercício:</h3>");
    $("#licao").append("<p>Faça uma consulta que retorne o nome e o id de cada fita presente na loja.</p>");
    $("#licao").append("<button class=\"btn\" id=\"dica\"> Mostrar Dica </button>");
    var $block = $(document.createElement("div")).addClass("row");
    var $panel = $(document.createElement("div")).addClass("col").addClass("s12");
    var $textArea = $(document.createElement("div")).addClass("col").addClass("s12");
    $textArea.append("<textarea id=\"area\"></textarea>");
    //cria um painel de botoes
    var botoesParaAdicionar = ["Π", "σ", "∪","∩","-","ϒ","φ","δ","⋀","⋁","⋈","⋉","⋊","X"];
    var $bt;
    for (var i = 0; i < botoesParaAdicionar.length; i++) {
        var id = "btPanel"+i;
        var button = document.createElement("button");
        button.setAttribute("id", id);
        $bt = $(button).addClass("btn").addClass("minusculo").text(botoesParaAdicionar[i]);
        $bt.click(function() {
            $("#area").val($("#area").val()+$(this).text());
        });
        $panel.append($bt);
    }

    //Adiciona o painel de botoes e a area de texto para o bloco
    $block.append($panel, $textArea);
    //Adiciona o bloco a página
    $("#licao").append($block);

    $("#dica").click(function(){
        $('#modal1').modal('open');
    });

    $("#next").click(null).addClass("disabled");

}

$(document).ready(function(){
    $("#next").click(carregaAnswerPage);
});
