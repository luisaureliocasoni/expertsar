{
	var selects = 0;
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
        }else if (symb === "double"){
        	query = "SELECT "+cols+" FROM "+rel+" WHERE "+cond;
        }
        
        return query;
    }
}

start
 = _ rel:Relacao _ symb:SimboloUnion _ rest:start {return rel+symb+rest;}
 / rel:Relacao {return rel};

Relacao
 = _ "Π" _ col:Colunas _ "("_ "σ" _ cond:CondicaoStart _ "(" _ rel:Relacao _ ")" _ ")" _
   {return montaSelect("double", cond, col, rel);}
 / _ "Π" _ col:Colunas _ "(" _ rel:Relacao _ ")" _
   {return montaSelect("Π", null, col, rel);}
 / _ "σ" _ cond:CondicaoStart _ "(" _ rel:Relacao _ ")" _
   {return montaSelect("σ", cond, null, rel);}
 / _ nome:Identifier _ "X" _ rel:Relacao _
   {return nome+","+rel}
 / _ nome1:Identifier _ symb:SimboloJoin _ "(" _ col1:Identifier _ "=" _ col2:Identifier _ ")" _ rel:Relacao _
   {return nome1+symb+rel+" ON "+col1+" = "+col2;}
 / _ nome1:Identifier _ "*" _ rel:Relacao _
   {return nome1+" NATURAL JOIN "+rel}
 / _ "ρ" _ novoNome:Identifier _ "(" _ rel:Relacao _ ")" _
   {return "("+rel+")"+novoNome;}
 / _ nome:Identifier _
   {return nome;}


CondicaoStart
 = _ esq:Condicao _ symb:SimboloUnificadorCondicao _ dir:CondicaoStart _ {return esq +symb+ dir;}
 / cond:Condicao {return cond;}

Condicao
 = _ esq:NomeOuIntOuString _ symb:SimboloCondicao _ dir:NomeOuIntOuString {return esq+symb+dir;}

NomeOuIntOuString
 = Identifier
 / Integer
/* / String */

SimboloUnificadorCondicao "⋀ ou ⋁"
 = "⋀" {return " AND ";}
 / "⋁" {return " OR ";}
 
SimboloUnion "símbolo de união"
 = "∪" {return " UNION ";}
 / "∩" {return " INTERSECT ";}
 / "-" {return " EXCEPT ";}


//A juncao natural, como não exige coluna, nao está na lista
SimboloJoin "símbolo de junção"
 = "⨝" {return " INNER JOIN ";}
 / "⟗" {return " FULL OUTER JOIN ";}
 / "⟖" {return " RIGHT OUTER JOIN ";}
 / "⟕" {return " LEFT OUTER JOIN ";}

SimboloCondicao "símbolo de condição"
 = ">" {return " > ";}
 / "<" {return " < ";}
 / "=" {return " = ";}
 / "≤" {return " <= ";}
 / "≥" {return " >= ";}
 / "≠" {return " <> ";}

Colunas "lista de colunas"
 = _ id:IdentifierExtended _ "," _ resto:Colunas {return id+","+resto; }
 / _ id:IdentifierExtended _ {return id;}


Identifier "identificador"
 //Um identificador inicia-se com letra ou underscore, e pode ser seguido de letra, underscore, sifrao ou dígito
 //Captura um array de caracteres, a função join junta em uma string
 = inicio:[a-zA-Z_]fim:[a-zA-Z0-9_.$]+ {return inicio+fim.join(""); }
 
IdentifierExtended "identificador extendido"
 //Um identificador estendido é um identificador que permite o uso de parentesis e simbolos de matematica
 //Razao: permitir o uso de funcoes agregadas. \- == -
 = inicio:[a-zA-Z_]fim:[a-zA-Z0-9_.()+\-*/$]+ {return inicio+fim.join(""); }

Integer "inteiro"
  = [0-9]+ { return parseInt(text(), 10); } 

//Problema: Aspas...
String "string"
  = '"' text:[.*] '"' {return "\""+text+"\"";}

_ "espaço em branco"
  = [ \t\n\r]*
