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

namespace lib;


/**
 * Classe de DAO no sistema;
 *
 * @author root
 */
class DAO {
    /**
     * Recurso que representa uma conexão ao Banco de Dados
     * @var resource
     */
    private static $conn;
    /**
     * Representa um caminho para o arquivo de configurações do sistema
     * @var string
     */
    private static $file = "../../assets/conexao.ini";
    
    /**
     * Função construtora do sistema
     */
    private function __construct() {
        
    }
    
    /**
     * Seta o path do arquivo de configuração do DAO
     * @param type $file Path para o arquivo ini de configuração
     */
    public static function setFilePathConfig($file) {
        self::$file = $file;
    }
    
    /**
     * Inicializa a conexão com o Banco de Dados
     * @throws \Exception Se o arquivo não for encontrado ou houver erro de conexão ao banco de dados
     */
    private static function initialize() {
        try{
            $config = parse_ini_file(self::$file);
            if ($config === FALSE){
                throw new \Exception("Não foi possível localizar as configurações do BD.");
            }
            self:$conn = pg_connect("host={$config["server"]} dbname={$config["database"]} user={$config["user"]} password={$config["password"]}");
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
    public static function insert($obj, $ignoreNulls = \TRUE){
        if (self::$conn  != NULL){
            pg_close(self::$conn);
        }
        self::initialize();
        
        $result = self::extractColunasAndValues($obj);
        $colunas = $result["colunas"];
        $valores = $result["valores"];
        $table = $result["table"];
        
        $query = "INSERT INTO \"$table\" (".implode(",", $colunas).")";
        $query .= "VALUES (". implode(",", $valores).";";
        
        self::execute($query);
    }
    
    /**
     * Remove uma tupla de um determinado id da tabela
     * @param integer $id Id do elemento a ser removido
     * @param string $table String com a tabela a remover o objeto
     * @throws \Exception Caso haja erro na execução do SQL
     */
    public static function removeById($id, $table){
        if (self::$conn !== NULL){
            pg_close(self::$conn);
        }
        self::initialize();
        
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
        if (self::$conn !== NULL){
            pg_close(self::$conn);
        }
        self::initialize();
        
        $query = "DELETE FROM \"$table\" WHERE ".$cond->toString();
        self::execute($query);
    }
    
    /**
     * Faz um select no banco de dados de acordo com uma condicao
     * @param string $class Nome da classe a ser instanciada
     * @param string $table Nome da tabela a ser consultada
     * @param Condicao $cond Condicao de parâmetro de seleção das colunas
     * @return \ArrayObject Com o resultado
     * @throws Exception Caso dê erro de instanciação de classe ou no SQL
     */
    public static function select($class, $table, $cond){
        if (!($cond instanceof Condicao)){
            throw new Exception("\$cond deve ser uma instância de condição!");
        }
        $query = "SELECT * FROM \"$table\" WHERE \"id\" = ".$cond->toString().";";
        $result = self::execute($query);
        
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
     * @throws \Exception Caso haja erro na execução do SQL ou $cond não for uma instância de Condicao.
     */
    public static function update($obj, $cond, $ignoreNulls = \TRUE){
        if (!($cond instanceof Condicao)){
            throw new Exception("\$cond deve ser uma instância de condição!");
        }
        if (self::$conn  != NULL){
            pg_close(self::$conn);
        }
        self::initialize();
        
        $result = self::extractColunasAndValues($obj);
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
        if (self::$conn  != NULL){
            pg_close(self::$conn);
        }
        self::initialize();
        
        $result = self::extractColunasAndValues($obj);
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
    private static function extractColunasAndValues($obj){
        //Pega os dados do objeto, a procura de getters
        $reflectObj = new \ReflectionClass($obj);
        $colunas = new \ArrayObject();
        $valores = new \ArrayObject();
        foreach ($reflectObj->getMethods() as $method){
            //Pega todos os métodos, procurando gets
            if (strpos($method->name, "get") === 0){
                //Invoca o get
                $value = $method->invoke($obj);
                //Remove o get e transforma a primeira letra do atributo em minusculo
                $attr = lcfirst(str_replace("get", "", $method->name));
                if ($value !== \NULL || $ignoreNulls === \FALSE ){
                    $colunas->append("\"$attr\"");
                    $valores->append(DAOUtilis::toStr($value));
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
    private static function execute($query) {
        $result = pg_query(self::$conn, $query);
        if ($result === FALSE){
            throw new \Exception("Erro na query: "+pg_last_error(self::$conn));
        }
        return $result;
    }

    /**
     * Gera uma string para ser usada na clásula SET
     * Ex: ["id", "nome"] e [99, "Luis"]
     * Retorna: "id = 99, nome = Luis"
     * @param \ArrayObject $colunas Colunas da tabela
     * @param \ArrayObject $valores Valores da tabela
     * @return string Com os valores convertidos
     * @throws \Exception Caso o número de colunas e de valores difira.
     */
    private static function generateSetString($colunas, $valores){
        if ($colunas->count() != $valores->count()){
            throw new \Exception("O número de colunas e de valores difere!");
        }
        $str = "";
        for ($i = 0; $i < $colunas->count(); $i++){
            $str .= "{$colunas[$i]}={$valores[$i]}";
            if ($i + 1 < $colunas->count()){
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
            $instance = $class->newInstance(array($array["id"]));

            foreach ($arr as $key => $value) {
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
}
