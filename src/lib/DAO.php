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
    
    private function __construct() {
        
    }
    
    private static function initialize() {
        try{
            $config = parse_ini_file("../../assets/conexao.ini");
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
    
    public function insert($obj, $ignoreNulls = TRUE){
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
        
        $query = "INSERT INTO $table (".implode(",", $colunas).")";
        $query .= "VALUES (". implode(",", $valores).";";
        
        $result = pg_query(self::$conn, $query);
        if ($result === FALSE){
            throw new \Exception("Erro na query: "+pg_last_error(self::$conn));
        }
    }
    
    private static function toStr($x){
        $type = gettype($x);
        if ($type === "string" || $type === "integer" || $type === "double"){
            return pg_escape_string($x);
        }else if ($type === "boolean") {
            return ($x ? "TRUE" : "FALSE");
        }else{
            return $type;
        }
    }

}
