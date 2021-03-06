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

namespace ExpertsAR;

/**
 * Classe que representa uma lição no sistema
 *
 * @author Luís Aurélio Casoni
 */
class Licao {
    public static $table = "Licoes";
    
    private $id;
    private $nome;
    private $slug;
    private $textoLicao;
    private $idMantenedorCriou;
    private $idMantenedorAlterou;
    
    function getId() {
        return $this->id;
    }

    function getNome() {
        return $this->nome;
    }

    function getSlug() {
        return $this->slug;
    }

    function getTextoLicao() {
        return $this->textoLicao;
    }

    function getIdMantenedorCriou() {
        return $this->idMantenedorCriou;
    }

    function getIdMantenedorAlterou() {
        return $this->idMantenedorAlterou;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setNome($nome) {
        $this->nome = $nome;
        return $this;
    }

    function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    function setTextoLicao($textoLicao) {
        $this->textoLicao = $textoLicao;
        return $this;
    }

    function setIdMantenedorCriou($idMantenedorCriou) {
        $this->idMantenedorCriou = $idMantenedorCriou;
        return $this;
    }

    function setIdMantenedorAlterou($idMantenedorAlterou) {
        $this->idMantenedorAlterou = $idMantenedorAlterou;
        return $this;
    }
    
    function __construct($id = NULL) {
        $this->id = $id;
        return $this;
    }



}
