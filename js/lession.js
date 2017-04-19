"use strict"


//http://stackoverflow.com/questions/1064089/inserting-a-text-where-cursor-is-using-javascript-jquery
function insertAtCaret(areaId, text) {
    var txtarea = document.getElementById(areaId);
    if (!txtarea) { return; }

    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
        "ff" : (document.selection ? "ie" : false ) );
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br == "ff") {
        strPos = txtarea.selectionStart;
    }

    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
        txtarea.focus();
        var ieRange = document.selection.createRange();
        ieRange.moveStart ('character', -txtarea.value.length);
        ieRange.moveStart ('character', strPos);
        ieRange.moveEnd ('character', 0);
        ieRange.select();
    } else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }

    txtarea.scrollTop = scrollPos;
}


function defineInsertButtonEvent() {
    //Evento de clique do botao de inserção no painel
    $(".insert").click(function() {
        //Acessa a proprieidade data do botão
        var dataFor = $(this).data().for.slice(1);
        //console.log(dataFor);
        var $for = $(dataFor);
        //console.log($for);
        //$for.val($for.val() + ($(this).text()));
        insertAtCaret(dataFor, $(this).text());
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

function buildErrorMessage(e) {
    return e.location !== undefined
      ? "Line " + e.location.start.line + ", column " + e.location.start.column + ": " + e.message
      : e.message;
}

//Responsável por armazenar as selects nomeadas
var querysSalvas = [];
//Pós-processamento da query processada pelo parser;
function processaQuery(query) {
    console.log(typeof query, query);
    if (typeof query === "string"){
        //Verifica se a relação anterior é na verdade um identificador
        //Se for, troca o identificador pela query salva
        if (querysSalvas[query] !== undefined){
        	return querysSalvas[query];
        }else if(query.search("SELECT ") !== 0){
        	return "SELECT * FROM " + query;
        }else{
        	return query;
        }
    }else if(typeof query === "object"){
        if (query.type === "attrib"){
            //Processa o valor a ser salvo e salva no mapa.
            querysSalvas[query.id] = processaQuery(query.value);
            //Processa em chama recursiva o resto da query
            return processaQuery(query.rest);
        }else if(query.type === "union"){
            var esq = processaQuery(query.relacao);
            return (esq + query.simbolo + processaQuery(query.rest));
        }else{
            throw new Error("Tipo incorreto de objeto!");
        }
    }else{
        throw new Error("Tipo incorreto!");
    }
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

        try{
            var parsed = parser.parse($textarea.val());
        }catch (e){
            Materialize.toast("Sua query possui um erro!", 4000);
            $("#result").html("<p>"+buildErrorMessage(e)+"</p>");
            return;
        }
        //faz o pós processamento da query gerada
        var str = processaQuery(parsed)+";";
        Materialize.toast("Resposta Incorreta!", 4000);
        $("#result").html("<code>"+str+"</code>");
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
