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

    public $producao=true;
    private $ativar=true;
    private $token = "78b00e70344143a0895f106cce231f6bkxxKf9S9WvzPMFjmMWZvyNZ4XYmsm1yLzV0iqFA1X8iFACvnW4zHbfeABj6efOUD"; 
    private $url ='http://api.thelogiclive.com/api/v1/';
    private $meuDominio ='https://arvore-refutacao.thelogiclive.com/#/';

    private $urlExercicioValidacao = 'exercicio/validacao/';
    private $urlExercicioLivre = 'exercicio/livre/';
    private $urlExercicioConceitos = 'exercicio/conceitos/';


    private $game =    ['gam_nome'=>'Árvore de Refutação', 'gam_descricao'=> 'Módulo de validação de fórmulas da lógica proposicional através do método de Árvore de Refutação', 'gam_ativo'=>1];
    private $modulo1 = ['mod_nome'=>'Módulo de Validação de Fórmulas da Lógica Proposicional', 'gam_codigo'=> '', 'mod_descricao'=>'Módulo de validação de Fórmulas da ', 'mod_ativo'=>1];
    private $modulo2 = ['mod_nome'=>'Módulo de Estudo dos conceitos', 'gam_codigo'=> '', 'mod_descricao'=>'Módulo de estudo dos conceitos do método de árvore de refutação', 'mod_ativo'=>1];
    private $modulo3 = ['mod_nome'=>'Módulo de Estudo Livre', 'gam_codigo'=> '', 'mod_descricao'=>'Módulo de estudo livre do método de árvore de refutação', 'mod_ativo'=>1];
    
    private $recomp_exerc_mod_2 = ['rec_nome'=>'Recompensa do modulo de conceitos', 'rec_pontuacao'=> 50,'rec_imagem'=>'vazio'];
    private $recomp_exerc_mod_3 = ['rec_nome'=>'Recompensa do modulo livre', 'rec_pontuacao'=> 0,'rec_imagem'=>'vazio'];

    private $nivel_mod_2 = ['mod_codigo'=>'','niv_nome'=> 'Estudo dos conceitos', 'niv_descricao'=>'Nivel do estudo dos conceitos', 'niv_ativo'=>1];
    private $nivel_mod_3 = ['mod_codigo'=>'','niv_nome'=> 'Estudo Livre', 'niv_descricao'=>'Nivel do estudo livre', 'niv_ativo'=>1];
   
    private $exerc1_mod_2 = ['rec_codigo'=>'', 'niv_codigo'=> '', 'exe_nome'=>'Árvore de Refutação', 'exe_descricao'=>'Exercicio para estudo dos conceitos', 'exe_link'=>'' ,'exe_ativo'=>1];
    private $exerc2_mod_2 = ['rec_codigo'=>'', 'niv_codigo'=> '', 'exe_nome'=>'Regras da Árvore de Refutação', 'exe_descricao'=>'Exercicio para estudo dos conceitos', 'exe_link'=>'' ,'exe_ativo'=>1];
    
    private $exerc_mod_3 = ['rec_codigo'=>'', 'niv_codigo'=> '', 'exe_nome'=>'Estudo livre', 'exe_descricao'=>'Exercicio para estudo livre', 'exe_link'=>'' ,'exe_ativo'=>1];
    
    
    public function __construct( )
    {
        $this->meuDominio =  $this->producao==true ?'https://arvore-refutacao.thelogiclive.com/#/':'http://localhost:4200/#/';
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


    public function recompenExerciMod2(){
        return $this->recomp_exerc_mod_2;
    }

    public function recompenExerciMod3(){
        return  $this->recomp_exerc_mod_3;
    }






    public function setRecompenEndNivelInExerci1Mod2($idRec, $idNivel){
        $this->exerc1_mod_2['rec_codigo']=$idRec;
        $this->exerc1_mod_2['niv_codigo']=$idNivel;
    }

    public function setRecompenEndNivelInExerci2Mod2($idRec, $idNivel){
        $this->exerc2_mod_2['rec_codigo']=$idRec;
        $this->exerc2_mod_2['niv_codigo']=$idNivel;
    }

    public function setRecompenEndNivelInExerciMod3($idRec, $idNivel){
        $this->exerc_mod_3['rec_codigo']=$idRec;
        $this->exerc_mod_3['niv_codigo']=$idNivel;
    
    }


    public function setIdLinkInExerci1Mod2($id){
        $this->exerc1_mod_2['exe_link'] = $this->meuDominio.$this->urlExercicioConceitos.$id;

    }

    public function setIdLinkInExerci2Mod2($id){
        $this->exerc2_mod_2['exe_link'] = $this->meuDominio.$this->urlExercicioConceitos.$id;

    }

    public function setIdLinkInExerciMod3($id){
        $this->exerc_mod_3['exe_link'] = $this->meuDominio.$this->urlExercicioLivre.$id;
    
    }


    public function setIdGame($id){
        $this->modulo1['gam_codigo']=$id;
        $this->modulo2['gam_codigo']=$id;
        $this->modulo3['gam_codigo']=$id;
    }

    public function setIdModulo2InNivel($id){
        $this->nivel_mod_2['mod_codigo']=$id;
    }

    public function setIdModulo3InNivel($id){
        $this->nivel_mod_3['mod_codigo']=$id;
    
    }



    public function getIdModulo2InNivel(){
        return  $this->nivel_mod_2;
    
    }

    public function getIdModulo3InNivel(){
        return $this->nivel_mod_3;
    
    }
    

    
    public function exercicio1Conceitos(){
        return  $this->exerc1_mod_2;
    
    }

    public function exercicio2Conceitos(){
        return  $this->exerc2_mod_2;
    
    }

    public function exercicioLivre(){
        return $this->exerc_mod_3;
    
    }

    public function urlExercicioValidacao(){
        return $this->meuDominio.$this->urlExercicioValidacao;
    }

    public function urlExercicioLivre(){
        return $this->meuDominio.$this->urlExercicioLivre;
    }

    public function urlExercicioConceitos(){
        return $this->meuDominio.$this->urlExercicioConceitos;
    }



    

}
