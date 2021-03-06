<?php

/**
 * ExpertsAR - Um Objeto de Aprendizagem para Álgebra Relacional
 * (C) 2017 - Luís Aurélio Casoni e Ademir Martinez Sanches
 * Este código-fonte está licenciado sob a licença MIT.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * */

namespace Lib;


/**
 * Classe de DAO para PostgreSQL no sistema;
 *
 * @author root
 */
class DAOPG {
    /**
     * Recurso que representa uma conexão ao Banco de Dados
     * @var resource
     */
    private static $conn;
    /**
     * Representa um caminho para o arquivo de configurações do sistema
     * @var string
     * @deprecated
     */
    private static $file = "../../assets/conexao.ini";

    /**
     * Função construtora do sistema
     */
    private function __construct() {

    }

    /**
     * Seta o path do arquivo de configuração do DAO
     * Aviso: Caso haja uma conexão existente, ela será fechada
     * @param type $file Path para o arquivo ini de configuração
     * @deprecated
     */
    public static function setFilePathConfig($file) {
        if (self::$conn !== NULL){
            pg_close(self::$conn);
            self::$conn = NULL;
        }
        self::$file = $file;
    }

    /**
     * Inicializa a conexão com o Banco de Dados
     * @throws \Exception Se o arquivo não for encontrado ou houver erro de conexão ao banco de dados
     */
    private static function initialize() {
        try{
            $config_array = require __DIR__."/../../db/postgres/phinx.php";
            $config = $config_array['environments'][$config_array['environments']['default_environment']];
            if ($config === FALSE){
                throw new \Exception("Não foi possível localizar as configurações do BD.");
            }
            self::$conn = pg_connect("host={$config["host"]} dbname={$config["name"]} user={$config["user"]} password={$config["pass"]}");
            if (self::$conn === FALSE){
                throw new \Exception("Não foi possível conectar ao Banco de Dados");
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Insere um objeto no BD
     * Esse objeto deve se referir a uma tabela no Banco de Dados, através do parâmetro
     * estático table. E todos atributos que forem acessíveis por get devem estar presentes
     * na tabela.
     * @param object $obj Objeto a ser inserido.
     * @param boolean $ignoreNulls Booleano que indica se é para ignorar valores nulos no objeto
     * @throws \Exception Caso haja erro na execução do SQL
     */
    public static function insert($obj, $ignoreNulls = TRUE){
        $result = self::extractColunasAndValues($obj, $ignoreNulls);
        $colunas = $result["colunas"];
        $valores = $result["valores"];
        $table = $result["table"];

        $query = "INSERT INTO \"$table\" (".implode(",", $colunas).")";
        $query .= "VALUES (". implode(",", $valores).");";

        self::execute($query);
    }

    /**
     * Remove uma tupla de um determinado id da tabela
     * @param integer $id Id do elemento a ser removido
     * @param string $table String com a tabela a remover o objeto
     * @throws \Exception Caso haja erro na execução do SQL
     */
    public static function removeById($id, $table){
        $query = "DELETE FROM \"$table\" WHERE \"id\" = $id;";
        self::execute($query);
    }

    /**
     * Remove tuplas de acordo com uma condição
     * @param Condicao $cond Condicao que determina quais elementos serão removidos
     * @param string $table String com a tabela a remover o objeto
     * @throws \Exception Caso haja erro na execução do SQL ou $cond não for uma instância de Condicao
     */
    public static function remove($cond, $table){
        if (!($cond instanceof Condicao)){
            throw new Exception("\$cond deve ser uma instância de condição!");
        }

        $query = "DELETE FROM \"$table\" WHERE ".$cond->toString();
        self::execute($query);
    }

    /**
     * Faz um select no banco de dados de acordo com uma condicao
     * @param string $class Nome da classe a ser instanciada
     * @param string $table Nome da tabela a ser consultada
     * @param Condicao $cond Condicao de parâmetro de seleção das colunas
     * @return \ArrayObject|\NULL ArrayObject com o resultado ou NULL caso nada for retornado
     * @throws Exception Caso dê erro de instanciação de classe ou no SQL
     */
    public static function select($class, $table, $cond){
        if (!($cond instanceof Condicao)){
            throw new Exception("\$cond deve ser uma instância de condição!");
        }
        $query = "SELECT * FROM \"$table\" WHERE ".$cond->toString().";";
        $result = self::execute($query);

        if (pg_affected_rows($result) === 0){
            return NULL;
        }

        $objs = new \ArrayObject();
        while ($arr = pg_fetch_array($result, null, PGSQL_ASSOC)){
            $objs->append(self::transformArrayInObject($arr, $class));
        }

        return $objs;
    }

    /**
     * Pega um objeto pelo ID do objeto
     * @param string $class Nome da classe a ser criada
     * @param string $table Nome da tabela
     * @param int $id Id a ser achado
     * @return Object Se o objeto for instanciado com êxito
     * @throws \Exception Caso dê erro na instanciação ou na query
     */
    public static function selectById($class, $table, $id){
        $query = "SELECT * FROM \"$table\" WHERE \"id\" = ".$id.";";
        $result = self::execute($query);

        if (pg_affected_rows($result) === 0){
            return NULL;
        }

        $arr = pg_fetch_array($result, null, PGSQL_ASSOC);
        $object = self::transformArrayInObject($arr, $class);

        return $object;
    }

    /**
     * Busca todos os objetos de uma tabela
     * @param string $class Classe a ser instanciada
     * @param string $table Tabela a ser procurada
     * @return \ArrayObject|\NULL Um array com todos os objetos encontrados ou NULL caso nada for retornado
     */
    public static function selectAll($class, $table){
        $query = "SELECT * FROM \"$table\";";
        $result = self::execute($query);

        if (pg_affected_rows($result) === 0){
            return NULL;
        }

        $objs = new \ArrayObject();
        while ($arr = pg_fetch_array($result, null, PGSQL_ASSOC)){
            $objs->append(self::transformArrayInObject($arr, $class));
        }

        return $objs;
    }

    /**
     * Atualiza um objeto na tabela
     * @param object $obj Objeto a ser atualizado no banco
     * @param Condicao $cond Condição para parametrizar a atualização
     * @param bool $ignoreNulls Booleano que indica se é para ignorar valores nulos
     * Quando um valor nulo é ignorado, não entra na query.
     * @throws \Exception Caso haja erro na execução do SQL ou $cond não for uma instância de Condicao.
     */
    public static function update($obj, $cond, $ignoreNulls = \TRUE){
        if (!($cond instanceof Condicao)){
            throw new Exception("\$cond deve ser uma instância de condição!");
        }

        $result = self::extractColunasAndValues($obj, $ignoreNulls);
        $colunas = $result["colunas"];
        $valores = $result["valores"];
        $table = $result["table"];

        $query = "UPDATE \"$table\" SET ".self::generateSetString($colunas, $valores);
        $query .= "WHERE ".$cond->toString().";";

        self::execute($query);
    }

    /**
     * Atualiza um objeto na tabela pelo id
     * @param object $obj Objeto a ser atualizado no banco
     * @param integer $id Condição para parametrizar a atualização
     * @param bool $ignoreNulls Booleano que indica se é para ignorar valores nulos
     * @throws \Exception Caso haja erro na execução do SQL ou $cond não for uma instância de Condicao.
     */
    public static function updateById($obj, $id, $ignoreNulls = \TRUE){
        $result = self::extractColunasAndValues($obj, $ignoreNulls);
        $colunas = $result["colunas"];
        $valores = $result["valores"];
        $table = $result["table"];

        $query = "UPDATE \"$table\" SET ".self::generateSetString($colunas, $valores);
        $query .= "WHERE \"id\" = $id;";

        self::execute($query);
    }


    /**
     * Extrai, via reflexão, as colunas e os valores de um objeto, e a sua tabela associada
     * @param object $obj Objeto a ser analisado
     * @return array Com os indices: <b>colunas</b>, um ArrayObject com os nomes dos atributos,
     * <b>valores</b>, um ArrayObject com os valores dos atributos e <b>table</b>, o nome da tabela associada.
     */
    private static function extractColunasAndValues($obj, $ignoreNulls){
        //Pega os dados do objeto, a procura de getters
        $reflectObj = new \ReflectionClass($obj);
        $colunas = [];
        $valores = [];
        $i = 0;
        foreach ($reflectObj->getMethods() as $method){
            //Pega todos os métodos, procurando gets
            if (strpos($method->name, "get") === 0){
                //Invoca o get
                $value = $method->invoke($obj);
                //Remove o get e transforma a primeira letra do atributo em minusculo
                $attr = lcfirst(str_replace("get", "", $method->name));
                if ($value !== \NULL || $ignoreNulls === \FALSE ){
                    $colunas[$i] = "\"$attr\"";
                    $valores[$i] = DAOUtilis::toStr($value);
                    $i++;
                }

            }
        }
        $table = $reflectObj->getProperty("table")->getValue($obj);
        $result = [];
        $result["colunas"] = $colunas;
        $result["valores"] = $valores;
        $result["table"] = $table;
        return $result;
    }


    /**
     * Executa uma query SQL
     * @param string $query Query SQL a ser executada
     * @return resource O resultado da query
     * @throws \Exception Caso a execução da query termine em falha
     */
    public static function execute($query) {
        if (self::$conn  !== NULL){
            pg_close(self::$conn);
        }
        self::initialize();

        if (self::$conn === FALSE){
            throw new Exception("A conexão com o Banco de Dados foi mal-sucedida!");
        }
        try{
            $result = pg_query(self::$conn, $query);
        } catch (Exception $ex) {
            throw $ex;
        }

        if ($result === FALSE){
            throw new \Exception(pg_last_error(self::$conn));
        }
        return $result;
    }

    /**
     * Retorna uma string escapada
     * @param string $str String a ser escapada
     * @return string String escapada
     */
    public static function escapeString($str){
        return pg_escape_string($str);
    }

    /**
     * Gera uma string para ser usada na clásula SET
     * Ex: ["id", "nome"] e [99, "Luis"]
     * Retorna: "id = 99, nome = Luis"
     * @param array $colunas Colunas da tabela
     * @param array $valores Valores da tabela
     * @return string Com os valores convertidos
     *
     */
    private static function generateSetString($colunas, $valores){
        if (count($colunas) != count($valores)){
            throw new \Exception("O número de colunas e de valores difere!");
        }
        $str = "";
        for ($i = 0; $i < count($colunas); $i++){
            $str .= "{$colunas[$i]}={$valores[$i]}";
            if ($i + 1 < count($colunas)){
                $str .= ",";
            }
        }
        return $str;
    }

    /**
     * Transforma um array associativo em um objeto
     * @param Array $array Array associativo com os valores a serem salvos
     * @param string $className Nome da classe da instância a ser criada
     * @return object Objeto instanciado e com os valores inseridos
     * @throws Exception Caso dê um ReflectionException: Classe ou método não existem
     */
    private static function transformArrayInObject($array, $className){
        try{
            $class = new \ReflectionClass($className);
            $instance = $class->newInstance($array["id"]);

            foreach ($array as $key => $value) {
                if ($key !== "id"){
                    $setMethod = "set".ucfirst($key);
                    $class->getMethod($setMethod)->invoke($instance, $value);
                }
            }

        } catch (Exception $ex) {
            throw new Exception("Houve um erro na criação da classe: ".$ex->getMessage());
        }
        return $instance;
    }

    /**
     * Transforma um recurso em um array associativo
     * @param resource $resource Um recurso do SGBD com os resultados
     * @return \ArrayObject|NULL ArrayObject com o array transformado ou NULL caso nada for encontrado
     */
    public static function transformResourceInArray($resource){
        if (pg_num_rows($resource) === 0){
            return NULL;
        }

        $array = new \ArrayObject();
        while ($arr = pg_fetch_array($resource, null, PGSQL_ASSOC)){
            $array->append($arr);
        }

        return $array;
    }
}
