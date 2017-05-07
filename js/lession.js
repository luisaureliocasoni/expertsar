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
    $(".insert").click(function(e) {
        e.preventDefault();
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

/*Função que trata o resultado*/
function tratarResultado(exito, $textarea, $button, resposta){
    if (exito === true){ //Se foi considerado um exito
        //verifica se estamos em uma pergunta
        if ($button.data().ispergunta === 1){
            //pega a resposta correta
            var correto = $($button.data().fieldcheck).val();
            //verifica se a resposta bate
            if (correto === resposta){
                Materialize.toast("A resposta está correta!");
                $textarea.addClass("valid");
                $button.addClass("disabled");
                $(".atual").data("concluido", "yes"); //marca o painel como concluido
                $("#next").removeClass("disabled"); //ativa o botão do proximo
            }else{
                Materialize.toast("A resposta está incorreta!");
                $textarea.addClass("invalid");
            }
        }else{ //Se não está, estamos em uma edição
            $textarea.addClass("valid");
            $("input#resposta").val(resposta);
            $("#submitPergunta").removeClass("disabled");
        }
    }else{
        $textarea.addClass("invalid");
        //verifica se não estamos em uma pergunta
        if ($button.data().ispergunta !== 1){
            $("input#resposta").val(null);
            $("#submitPergunta").addClass("disabled");
        }
        
    }
}

function submitResposta($button) {
    if ($button.data() === 0 || $button.data().for === undefined){
        throw "Qual textarea devo consultar? Deve ser definido a proprieidade data-for no botao. Nada Feito.";
    }
    
    var $resultPanel = $($button.data().showon);
    
    $resultPanel.html("<p>Aguarde...</p><div class=\"progress\"><div class=\"indeterminate\"></div></div>");

    var $textarea = $($button.data().for);
    $textarea.removeClass("invalid valid");

    try{
        var parsed = parser.parse($textarea.val());
        //faz o pós processamento da query gerada
        var str = processaQuery(parsed)+";";
    }catch (e){
        Materialize.toast("Sua query possui um erro!", 4000);
        $resultPanel.html("<p>"+buildErrorMessage(e)+"</p>");
        tratarResultado(false, $textarea, $button);
        return;
    }



    var test = $.ajax("/api/api.php", {
        method: "POST",
        success: function(objs, textStatus, xhr){
            $resultPanel.html("<div class=\"card final\"><p>Sua query:</p><code>"+str+"</code></div>");
            if (objs === null){
                $resultPanel.append("<p>0 linhas afetadas. Nada a exibir.</p>");
                return;
            }
            //Se não for null, cria a tabela
            //Primeiro, pega os cabecalhos do objeto
            var table = "<div class=\"card final\"><table class=\"responsive-table centered striped\"><thead><tr>";
            for (var key in objs[0]) {
                if (objs[0].hasOwnProperty(key)) {
                    table += "<th>"+key+"</th>";
                }
            }
            table += "</tr></thead><tbody>";
            //Corpo da tabela
            //Itera nas proprieidades do superobjeto
            for (var indice in objs) {
                if (objs.hasOwnProperty(indice)) {
                    var obj = objs[indice];
                    table += "<tr>";
                    //Itera nas proprieidades do objeto
                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            table += "<td>"+obj[key]+"</td>";
                        }
                    }
                    table += "</tr>";
                }
            }
            table += "</tbody></table></div>";
            $resultPanel.append(table);

            //Pega o texto original do resultado da consulta e coloca como a resposta
            tratarResultado(true, $textarea, $button, xhr.responseText);
            //this.resposta = xhr.responseText;
        },
        data: {query: str},
        dataType: "json",
        error: function(msg){
            Materialize.toast("Houve um erro na interpretação SQL da Query!", 4000);
            $resultPanel.html("<p>Sua query:</p><code>"+str+"</code>");
            $resultPanel.append("<p>"+msg.responseText+"</p>");
            tratarResultado(false, $textarea, $button);
        },

    });
}

$(document).ready(function(){
    $("#next").click(carregaNextPage);
    $("#prev").click(carregaPreviousPage);
    $(".submit").click(function (e) {
        e.preventDefault();
        submitResposta($(this));
    });
    defineInsertButtonEvent();
});
