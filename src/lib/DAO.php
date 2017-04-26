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
    private static $conn;
    private static $instance;
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
    public function insert($obj, $ignoreNulls = \TRUE){
        if (self::$conn  != NULL){
            pg_close(self::$conn);
        }
        self::initialize();
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
                    $colunas->append($attr);
                    $valores->append(self::toStr($value));
                }
                
            }
        }
        //Pega o valor do atributo estático table.
        $table = $reflectObj->getProperty("table")->getValue($obj);
        
        $query = "INSERT INTO $table (".implode(",", $colunas).")";
        $query .= "VALUES (". implode(",", $valores).";";
        
        self::execute($query);
    }
    
    /**
     * Remove uma tupla de um determinado id da tabela
     * @param integer $id Id do elemento a ser removido
     * @param string $table String com a tabela a remover o objeto
     * @throws \Exception Caso haja erro na execução do SQL
     */
    public static function remove($id, $table){
        if (self::$conn !== NULL){
            pg_close(self::$conn);
        }
        self::initialize();
        
        $query = "DELETE FROM $table WHERE id = $id";
        self::execute($query);
    }
    
    /**
     * Atualiza um objeto na tabela
     * @param object $obj Objeto a ser atualizado no banco
     * @param string $cond Condição expressa em string sql para parametrizar a atualização
     * @param bool $ignoreNulls Booleano que indica se é para ignorar valores nulos
     * @throws \Exception Caso haja erro na execução do SQL
     */
    public static function update($obj, $cond, $ignoreNulls = \TRUE){
        if (self::$conn  != NULL){
            pg_close(self::$conn);
        }
        self::initialize();
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
                    $colunas->append($attr);
                    $valores->append(self::toStr($value));
                }
                
            }
        }
        $table = $reflectObj->getProperty("table")->getValue($obj);
        
        $query = "UPDATE $table SET ".self::generateSetString($colunas, $valores);
        $query .= "WHERE $cond;";
        
        self::execute($query);
    }
    
    /**
     * Verifica uma variável e retorna uma string com o valor SQL válido
     * Ex: "teste" => "\"teste\""
     *     TRUE => "TRUE"
     * @param mixed $x Valor a ser avaliado
     * @return string A string convertida
     */
    private static function toStr($x){
        $type = gettype($x);
        if ($type === "integer" || $type === "double"){
            return pg_escape_string($x);
        }else if ($type === "string"){
            return "\"".pg_escape_string($x)."\"";
        }else if ($type === "boolean") {
            return ($x ? "TRUE" : "FALSE");
        }else{
            return $type;
        }
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
}
