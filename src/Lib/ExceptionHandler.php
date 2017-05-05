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
 * Manipula uma exceção
 *
 * @author Luis Aurélio Casoni
 */
class ExceptionHandler {
    private $ex;
    private $page;
    
    function __construct(\Exception $ex, string $page) {
        $this->ex = $ex;
        $this->page = $page;
    }
    
    public function run(){
        http_response_code(500);
        
        $date = date('Y-m-d H:i:s');
        
        $msgLog = sprintf("[%s] [%s]: %s%s", $date, $this->page, $this->ex, PHP_EOL);
        
        file_put_contents("assets/error.log", $msgLog, FILE_APPEND);
        
        $render = new RenderTemplate("view/");
        $render->render("500.html");
        die();
    }

}