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


function carregaNextPage() {
    //Pega a próximo item a ser exibido, o irmão do atual
    var $next = $(".atual").next();


    //Oculta o atual
    $(".atual").hide().addClass("hide").removeClass("atual");

    //Exibe a próxima página
    $next.removeClass("hide");
    $next.show();
    //Marca o box como atual
    $next.addClass("atual");

    //Verfica se a página que será exibida foi concluída, se não, desativa o botão
    //Para evitar desativar o botão direto, use data-islession
    if ($(".atual").data().concluido === undefined){
        $("#next").addClass("disabled");
    }
}

function carregaPreviousPage() {
    //Pega a próximo item a ser exibido, o anterior do atual
    var $prev = $(".atual").prev();

    //Rehabilita o botão de avançar
    $("#next").removeClass("disabled");

    //Se não houver mais páginas para exibir, volta a página inicial
    //O primeiro painel a ser exibido recebe a proprieidade data-isstart="yes"
    if ($(".atual").data() !== 0 && $(".atual").data().isstart === "yes"){
        //TODO perguntar ao usuário antes de sair
        window.location = "home.html";
        return;
    }

    //Oculta o atual
    $(".atual").hide().addClass("hide").removeClass("atual");

    //Exibe a página anterior
    $prev.removeClass("hide");
    $prev.show();
    //Marca o box como atual
    $prev.addClass("atual");
}

function submitResposta($button) {
    if ($button.data() === 0 || $button.data().for === undefined){
        throw "Qual textarea devo consultar? Deve ser definido a proprieidade data-for no botao. Nada Feito.";
    }

    var $textarea = $($button.data().for);
    if ($textarea.val() === "certo"){
        $("#next").removeClass("disabled");
        var $append = $(document.createElement("div"));
        $append.addClass("col s12 focus");
        $append.append("<div class=\"final\"><p>Query SQL</p><code class=\"final\">A query SQL aparecerá aqui</code></div>");
        $append.append("<div class=\"border final\"><table class=\"responsible-table striped centered\"><thead><tr><th>nomecli</th></tr><tbody><tr><td>João</td></tr><tr><td>Marcelo</td></tr><tr><td>(...)</td></tr></tbody></div>");
        $(".atual").append($append);
        //Marca a lição como concluido
        $(".atual").data("concluido", "yes");
        Materialize.toast("Resposta Correta!", 4000);
        $button.addClass("disabled");
        //$(".focus").focus();
    }else{
        Materialize.toast("Resposta Incorreta!", 4000);
    }

}

$(document).ready(function(){
    $("#next").click(carregaNextPage);
    $("#prev").click(carregaPreviousPage);
    $(".submit").click(function () {
        submitResposta($(this));
    });
    defineInsertButtonEvent();
});
