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

function posProcessamentoQuery(query, map, indexSave = undefined) {
    var separators = ["UNION", "INTERSECT", "EXCEPT"];
    if (index = query.indexOf("UNION") !== -1){

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

        var queries = $textarea.val().split("\n");
        var str = "";
        var queriesSaved[];
        for (var i = 0; i < queries.length; i++){
            try{
                var tmp;
                //Procura o sinal de atribuição na linha
                if (queries[i].indexOf("←") !== -1){ //Se tiver
                    var query = queries[i].split("←");
                    //Verifica se há um identificador na query
                    if (query[0].trim().length === 0){
                        throw new Error("A query "+query[1]+" requer um identificador!");
                    }
                    //Se tudo correr bem, converte a query e joga em um mapa.
                    queriesSaved[query[0]] = parser.parse(query[1]);
                }else{
                    tmp =  parser.parse(queries[i]);
                    //Pos-processamento
                }


            }catch (e){
                Materialize.toast("Sua query possui um erro!", 4000);
                $("#result").html("<p>"+buildErrorMessage(e)+"</p>");
                return;
            }

        }
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
