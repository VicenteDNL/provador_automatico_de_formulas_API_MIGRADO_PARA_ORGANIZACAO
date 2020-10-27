<?php

namespace App\Http\Controllers\LogicLive\config;

class Configuracao
{
    /**
     * Credenciais Logic Live
     * 
     * usu_nome=Danilo Saraiva Vicente 
     * usu_login=arvore.de.refutacao@gmail.com 
     * usu_email=arvore.de.refutacao@gmail.com 
     * usu_senha=arvorederefutacao
     */

     
    private $ativar=true;
    private $token = "78b00e70344143a0895f106cce231f6bkxxKf9S9WvzPMFjmMWZvyNZ4XYmsm1yLzV0iqFA1X8iFACvnW4zHbfeABj6efOUD"; 
    private $url ='http://api.thelogiclive.com/api/v1/';
    private $linkHospedagem = 'http://localhost:4200/exercicio/validacao/';
    private $game =    ['gam_nome'=>'Árvore de Refutação', 'gam_descricao'=> 'Módulo de validação de fórmulas da lógica proposicional através do método de Árvore de Refutação', 'gam_ativo'=>1];
    private $modulo1 = ['mod_nome'=>'Módulo de Validação de Fórmulas da Lógica Proposicional', 'gam_codigo'=> '', 'mod_descricao'=>'Módulo de validação de Fórmulas da ', 'mod_ativo'=>1];
    private $modulo2 = ['mod_nome'=>'Módulo de Estudo dos conceitos', 'gam_codigo'=> '', 'mod_descricao'=>'Módulo de estudo dos conceitos do método de árvore de refutação', 'mod_ativo'=>1];
    private $modulo3 = ['mod_nome'=>'Criação Livre', 'gam_codigo'=> '', 'mod_descricao'=>'Módulo de criação livre do método de árvore de refutação', 'mod_ativo'=>1];
    public function __construct( )
    {
    }

    public function ativo(){
        return $this->ativar;
    }

    public function token(){
        return $this->token;
    }

    public function url(){
        return $this->url;
    }


    public function moduloValidacaoFormulas(){
        return $this->modulo1;
    }

    public function moduloValidacaoLivre(){
        return $this->modulo3;
    }

    public function moduloEstudoConceitos(){
        return $this->modulo2;
    }

    public function game(){
        return $this->game;
    }

    public function setIdGame($id){
        $this->modulo1['gam_codigo']=$id;
        $this->modulo2['gam_codigo']=$id;
        $this->modulo3['gam_codigo']=$id;
    }

    public function linkHospedagem(){
        return $this->linkHospedagem;
    }








    

}
