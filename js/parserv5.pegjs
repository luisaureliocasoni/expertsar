/*Parser para Álgebra Relacional - Luís A. C. 24/05/2017
  Para compilar: pegjs -o parser.js --format globals  --export-var parser parser.pegjs
  Para converter a String de Álgebra Relacional para SQL: parser.parse(str);
  Use o PEG.js para compilar (pegjs.org)*/
{
    //Aqui, neste bloco, se declara as funções e variáveis auxiliares.
    var selects = 0;
    
    var stack = [];
    var result= null;

    var variables = {};

    /**Coloca uma instrução na pilha para salvar o value em variables no mapa**/
    /**https://stackoverflow.com/questions/43484626/save-variable-value-for-future-use-on-peg-js**/
    function save(chave, value, rest){
            stack.push(()=>{
              variables[chave] = value;
            });
            return rest;
    }
    
    /**Coloca uma instrução na pilha para salvar a string em result**/
    function saveStr(str){
    	stack.push(()=>{
              result = str;
        });
    }
   
    /**Função que avalia a pilha de instruções. Lendo da ordem correta e desejada
	   Depois, avalia os identificadores (cercados por aspas simples -  com \") 
	   Trocando os identificadores que se referem a uma referência a uma variável**/
    function evalStack() {
	//avalia a pilha de instruções
        for (var i = stack.length - 1; 0 <= i; i--) {
            stack[i]();
        }
        
        //Faz a troca de identificadores pelo valor de variables[$1]
        //$1 tem o valor que foi capturado no grupo ([a-z0-9_$]+), que representa o identificador
        //match tem o valor da string toda capturada
        //Ex: "SELECT * FROM \"Teste\"" => match = \"Teste\", $1 = Teste
        while (checkStrContainsVariables(result, variables) === true){
            result = result.replace(/\"([a-z0-9_$]+)\"/gi, function(match, $1, offset, s){
                if (variables[$1] === undefined){
                    return match; //se não tem, não faz a troca
                }
                return "("+variables[$1]+")alias"+(selects++);
            });
        }
        
        return result; //retorna o resultado final
    }

    function checkStrContainsVariables(str, names){
        for (name in names){
            if (str.search(name) != -1){
                return true;
            } 
        }
        return false;
    }
    
    /**Função que checa se uma string é uma instrução SELECT
    se não for, inclui dentro de str. Isso acontece quando o usuário só referencia
    nomes de tabelas e não especifica o operador. Por padrão, é entendido como uma seleção
    total da tabela.*/
    function checkStrIsSelect(str){
    	if (str.search("SELECT") == 0){
        	return str;
        }
        return "SELECT * FROM " + str;
    }
    
    //Função responsável por montar os selects
    function montaSelect(symb, cond, cols, rel){
    	/*Verifica se a relação anterior era um select
          Se sim, coloca um alias apropriado e parenteses na relação*/
        if (rel.search("SELECT") === 0){
        	rel = "("+rel+")alias"+selects++;
        }
        var query;
        //Pesquisa dentro de um IF qual foi o símblo para colocar o select apropriado
        if (symb === "Π"){
        	query = "SELECT "+cols+" FROM "+rel;
        }else if (symb === "σ"){
        	query = "SELECT * FROM "+rel+" WHERE "+cond;
        }else if (symb === "ϒ"){ //Agrupamento
        	//A primeira coluna representa o atributo de agrupamento
        	var colsList = cols.split(",");
            query = "SELECT "+cols+" FROM "+rel+" GROUP BY "+colsList[0];
        }else if (symb === "double"){
        	query = "SELECT "+cols+" FROM "+rel+" WHERE "+cond;
        }else{
        	throw new Error("Uso incorreto do parâmetro symb.");
        }

        return query;
    }

    //Verifica se a ordenação é aplicada a um select ou a uma tabela
    function montaOrdenacao(col, rel){
    	if (rel.search("SELECT") === 0){
        	//A relação anterior consiste em um select, assim basta adicionar
            // a cláusula ORDER BY no fim da query
        	return rel+" ORDER BY "+col;
        }else{
        	//Se não for, cria um select
            return "SELECT * FROM "+rel+" ORDER BY "+col;
        }
    }

    //Monta uma query com eliminador de duplicatas
    function montaEliminadorDeDuplicatas(rel){
    	if (rel.search("SELECT") === 0){
            //A relação anterior consiste em um select, assim basta adicionar
            // a cláusula DISTINCT entre o SELECT e o resto da query (a partir do 6º
            // caractere
            return "SELECT DISTINCT"+rel.substr(6);
        }else{
            //Se não for, cria um select
            return "SELECT DISTINCT * FROM "+rel;
        }
    }

    //funcao que monta a renomeação de uma relação ou uma tabela
    function montaRenomeacao(novoNome, text, type = "relacao"){
        var novaRelacao;
        if (type === "coluna"){
            if ((text.search("SELECT") !== 0) || (text.search("SELECT \\*") === 0)){
            	throw new Error("A operação de renomeação se aplica a projeções com colunas explícitas");
            }
            //Neste caso, text refere-se a um SELECT já montado, precisamos retirar as colunas
            //Primeiro, criamos um array desse SELECT, separando a String por espaços
            //Ex: SELECT x,y,z FROM a" -> ["SELECT", "x,y,z", "FROM", "a"]
            //Depois, neste array, pegamos o indice 1, com as colunas, e criamos um novo array, separando por vírgulas
            //[1] "x,y,z" -> ["x", "y", "z"]
            var listaColunasAtual = text.split(" ")[1].split(",");
            //novoNome representa uma nova lista de colunas, vamos separa-las
            var listaColunasNovas = novoNome.split(",");
            //Verifica se a qtd de colunas informadas a serem renomeadas não é igual a quantidade de colunas existentes
            //Se não for mesmo, dispara um erro.
            if (listaColunasAtual.length != listaColunasNovas.length){
            	throw new Error("A quantidade de colunas a serem renomeadas não batem com a quantidade existente de colunas");
            }
            var str = "";
            for (var i = 0; i < listaColunasAtual.length; i++){
            	str += (listaColunasAtual[i] + " AS " +  listaColunasNovas[i]);
                if (i+1 < listaColunasAtual.length){
                	str += ",";
                }
            }
            //Monta a query nova
            var restoQueryPos = text.search(" FROM");//Pega o resto da query
            return "SELECT "+str+text.substr(restoQueryPos);
        }else{ //se na verdade type == "relacao" (SELECT ou Tabela)
        	//Se for um select (Relacao)
        	if (text.search("SELECT") === 0){
        		//Exemplo: (SELECT * FROM X)novoNome
        		return "("+text+")"+novoNome;
        	}else{ //Se não for um select, e for uma tabela
        		//Exemplo: FUNCIONARIO AS F
        		return text + " AS " + novoNome;
                }
        }

    }

}

/*A primeira regra descrita é o ponto de partida do parser. Daí a medida que
consome os símbolos, ele procura as outras regras.

Cada regra é composta por subregras, por ordem de precedência, assim, a primeira
subregra declarada tem prioridade nas outras.

Como cada regra é atríbuida uma etiqueta e a regra retorna o que foi dado na etiqueta,
ex: rel:Relacao, rel é uma etiqueta da regra Relação. O parser aguarda o processamento
da regra interna para montar a query. Ex: start > Relacao > Identifier. A regra start
espera a Relação ser processada, que espera que Identifier seja processada. Assim, o
parser é semelhante a estratégia dividir-conquistar-combinar.

A primeira regra trata das atribuicoes. Se houver uma, vai armazenar em um objeto os
metadados da atribuição e o resto da query*/

start
 = expr
 {return evalStack();}
 
expr
 //Atribuicao, se tiver salva em um mapa
 //Ex: X ← r X S
 = _ id:IdentifierVar _ "←" _ toSave:UnionRelation __ newline _ rest:start
     {save(id, toSave, rest);}
 / _ rel:UnionRelation {return saveStr(rel);}


/*A segunda regra representa uma Relacao ou um conjunto de Relações, unidos pelo símbolo
de união/interseção/diferença
 Vai salvar em um objeto, com a relação do lado esquerdo convertido, o símbolo convertido
 e o resto da query*/
UnionRelation
 = _ rel:Relacao _ symb:SimboloUnion _ rest:UnionRelation
   {return checkStrIsSelect(rel) + symb + rest;}
 / rel:Relacao {return checkStrIsSelect(rel)};

//Se não houver uniões ou atribuições, só a query convertida é retornada.

//Início do conversor propriamente dito
//Representa uma projeção, seleção, renomeação, agrupamento,
//ordenação, produto cartesiano ou joins
Relacao
 //Π idcli, nome, cep (σ yg < 23 ⋀ ag = "30" (Resto))
 = _ "Π" _ col:Colunas _ "("_ "σ" _ cond:CondicaoStart _ "(" _ rel:Relacao _ ")" _ ")" _
   {return montaSelect("double", cond, col, rel);}
 //Π idcli, nome, cep (Resto)
 / _ "Π" _ col:Colunas _ "(" _ rel:Relacao _ ")" _
   {return montaSelect("Π", null, col, rel);}
 //σ yg < 23 ⋀ ag = "30" (Resto)
 / _ "σ" _ cond:CondicaoStart _ "(" _ rel:Relacao _ ")" _
   {return montaSelect("σ", cond, null, rel);}
 //Cliente X Resto
 / _ nome:Identifier _ "X" _ rel:Relacao _
   {return nome+","+rel}
 //Join: Cliente ⟗ (idcli = idcliloc) Resto
 / _ nome1:Identifier _ symb:SimboloJoin _ "(" _ col1:SuperIdentifier _ "=" _ col2:SuperIdentifier _ ")" _ rel:Relacao _
   {return "("+nome1+symb+rel+" ON "+col1+" = "+col2+")";}
 //Cliente * Resto
 / _ nome1:Identifier _ "*" _ rel:Relacao _
   {return nome1 + " NATURAL JOIN " + rel;}
 //ρ z (Resto)
 / _ "ρ" _ novoNome:Identifier _ "(" _ rel:Relacao _ ")" _
   {return montaRenomeacao(novoNome,rel);}
 //ρ [idcli, soma, sal] (Projecao_Resto)
 / _ "ρ" _ "[" _ cols:Colunas _ "]" _ "(" _ rel:Relacao _ ")" _
   {return montaRenomeacao(cols,rel,"coluna");}
 //Agrpamento: ϒ nproj, count(*), sum (TrabalhaEm)
 / _ "ϒ" _ col:Colunas _ "(" _ rel:Relacao _ ")" _
   {return montaSelect("ϒ", null, col, rel);}
 //Ordenação: τ idfil (Filme)
 / _ "τ" _ col:Colunas _ "(" _ rel:Relacao _ ")" _
   {return montaOrdenacao(col, rel);}
 //Eliminador de duplicatas
 //δ (Π idcli, nome, cep (σ yg < 23 ⋀ ag = "30" (Resto))) ou δ (Teste)
 / _ "δ" _ "(" _ rel:Relacao _ ")" _
   {return montaEliminadorDeDuplicatas(rel);}
 //Cliente
 / _ nome:Identifier _
   {return nome;}

//Um conjunto de condicoes, representados pelos símbolos E ou OU
//yg < 23 ⋀ ag = "30"
CondicaoStart
 = _ esq:Condicao _ symb:SimboloUnificadorCondicao _ dir:CondicaoStart _ {return esq +symb+ dir;}
 / cond:Condicao {return cond;}
 / rel: Relacao {return "("+rel+")";}

//Condicao
//yg < 23
Condicao
 = _ esq:Tipo _ symb:SimboloCondicao _ dir:Tipo {return esq+symb+dir;}

//Um dos tipos existentes
Tipo
 = SuperIdentifier
 / Float
 / Integer
 / String


//Símbolo E ou OU
//Entre aspas fica a mensagem que o parser devolve quando o não achar o símbolo
SimboloUnificadorCondicao "⋀, ⋁"
 = "⋀" {return " AND ";}
 / "⋁" {return " OR ";}


//Símbolo de União
SimboloUnion "símbolo de união"
 = "∪" {return " UNION ";}
 / "∩" {return " INTERSECT ";}
 / "-" {return " EXCEPT ";}


//Junções e suas traduções
//A juncao natural, como não exige coluna, nao está na lista
SimboloJoin "símbolo de junção"
 = "⨝" {return " INNER JOIN ";}
 / "⟗" {return " FULL OUTER JOIN ";}
 / "⟖" {return " RIGHT OUTER JOIN ";}
 / "⟕" {return " LEFT OUTER JOIN ";}

//Um simbolo de condição e suas traduções para SQL
SimboloCondicao "símbolo de condição"
 = ">" {return " > ";}
 / "<" {return " < ";}
 / "=" {return " = ";}
 / "≤" {return " <= ";}
 / "≥" {return " >= ";}
 / "≠" {return " <> ";}

//Funcoes agregadas
FuncaoAgregada "SUM, MAX, MIN, COUNT, AVG"
 = "SUM" {return "SUM";}
 / "sum" {return "SUM";}
 / "MAX" {return "MAX";}
 / "max" {return "MAX";}
 / "MIN" {return "MIN";}
 / "min" {return "MIN";}
 / "AVG" {return "AVG";}
 / "avg" {return "AVG";}
 / "COUNT" {return "COUNT";}
 / "count" {return "COUNT";}

//As regras definem uma lista de colunas
Colunas "lista de colunas"
 = _ id:IdentifierExtended _ "," _ resto:Colunas {return id+","+resto; }
 / _ id:IdentifierExtended _ {return id;}

//Representa um qualificador nomedatabela.tabela
SuperIdentifier "identificador"
 = _ table:Identifier _ "." _ id:Identifier _ {return table+"."+id;}
 / _ id:Identifier _ {return id;}

Identifier "identificador"
 //Um identificador inicia-se com letra ou underscore, e pode ser seguido de letra, underscore, sifrao ou dígito
 //Captura um array de caracteres, a função join junta em uma string
 = inicio:[a-zA-Z_]fim:[a-zA-Z0-9_$]+ {return "\""+inicio+fim.join("")+"\""; }
 / caract:[a-zA-Z_] {return "\""+caract+"\"";}

IdentifierVar "Variável"
 = inicio:[a-zA-Z_]fim:[a-zA-Z0-9_$]+ {return inicio+fim.join(""); }

IdentifierExtended "identificador, função agregada, operação matemática em um identificador"
 //Um identificador estendido é um identificador que permite o uso de parentesis e simbolos de matematica
 //Razao: permitir o uso de funcoes agregadas. \- == -
 //SUM(teste) => SUM(TESTE)
 = _ op:FuncaoAgregada _ "(" _ id:Identifier _ ")" _ {return op+"("+id+")";}
 / _ op:FuncaoAgregada _ "(" _ "*" _ ")" _ {return op+"(*)";}
 / _ id:SuperIdentifier _ symb:[+\-*/] _ num:Number _ {return id+" "+symb+" "+num;}
 / _ id:SuperIdentifier _ {return id;}


Number "float ou inteiro"
 = Float
 / Integer


Float "float"
  = [0-9.]+ { return parseFloat(text(), 10); }

Integer "inteiro"
  = [0-9]+ { return parseInt(text(), 10); }

//Procura uma string, ou seja, um texto com aspas
//No PostgreSQL, sao delimitados por aspas simples
String "string"
  = '"' text:TextoSemString* '"' {return "'"+text.join("")+"'";}

//Qualquer caractere que não for de fechamento, ou seja: aspas duplas
TextoSemString
 = !'"'char:.{return char;}

_ "espaço em branco"
  = [ \t\n\r]*

__ "espaço em branco"
  = [ \t\r]*

newline
  = [\n]*
