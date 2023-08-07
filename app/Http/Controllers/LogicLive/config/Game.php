<?php

namespace App\Http\Controllers\LogicLive\config;

use App\Http\Controllers\LogicLive\request\RequestGet;
use App\Http\Controllers\LogicLive\request\RequestPost;

class Game
{
    private $config;
    private $post;
    private $get;

    public function __construct( )
    {
        $this->config = new Configuracao;
        $this->post = new RequestPost;
        $this->get = new RequestGet;
    }

    /**
     * Cadastra o Game e os Três módulos na plataforma LOGIC LIVE
     */
    public function criarGameEndModulos(){
        
        $recompensa1 = $this->post->httppost('recompensa', $this->config->recompenExerciMod2());
        $recompensa2 = $this->post->httppost('recompensa', $this->config->recompenExerciMod3());
        if(!$recompensa1['success']|| !$recompensa2['success'] ){
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }


        $game = $this->post->httppost('game', $this->config->game());
        if(!$game['success']){
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }

        $this->config->setIdGame($game['data']['gam_codigo']);

        $modulo1 = $this->post->httppost('modulo', ($this->config->moduloValidacaoFormulas()));
        $modulo2 = $this->post->httppost('modulo', ($this->config->moduloValidacaoLivre()));
        $modulo3 = $this->post->httppost('modulo', ($this->config->moduloEstudoConceitos()));

        if(!$modulo1['success']|| !$modulo2['success'] || !$modulo3['success']){
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }

        $this->config->setIdModulo2InNivel($modulo3['data']['mod_codigo']); //a ordem entre 2 e 3 foi trocada no model configuracao
        $this->config->setIdModulo3InNivel($modulo2['data']['mod_codigo']);//a ordem entre 2 e 3 foi trocada no model configuracao

        $nivel_mod_2 = $this->post->httppost('nivel', $this->config->getIdModulo2InNivel());
        $nivel_mod_3 = $this->post->httppost('nivel', $this->config->getIdModulo3InNivel());
        if(!$nivel_mod_2['success']|| !$nivel_mod_3['success'] ){
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }


        $this->config->setRecompenEndNivelInExerci1Mod2($recompensa1['data']['rec_codigo'], $nivel_mod_2['data']['niv_codigo']);
        $this->config->setRecompenEndNivelInExerci2Mod2($recompensa1['data']['rec_codigo'], $nivel_mod_2['data']['niv_codigo']);
        $this->config->setRecompenEndNivelInExerciMod3($recompensa2['data']['rec_codigo'], $nivel_mod_3['data']['niv_codigo']);
        $this->config->setIdLinkInExerci1Mod2('01');
        $this->config->setIdLinkInExerci2Mod2('02');
        $this->config->setIdLinkInExerciMod3('01');

        $exerc1_mod_2 = $this->post->httppost('exercicio', $this->config->exercicio1Conceitos());
        $exerc2_mod_2 = $this->post->httppost('exercicio', $this->config->exercicio2Conceitos());
        $exerc_mod_3 = $this->post->httppost('exercicio', $this->config->exercicioLivre());

        if(!$exerc1_mod_2['success']|| !$exerc_mod_3['success'] || !$exerc2_mod_2['success'] ){
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }

     
        return ['success'=>true ,'msg'=>"", 'data'=>[
            'game'=>$game['data'],
            'modulos'=>[ $modulo1['data'], $modulo2['data'], $modulo3['data']],
            'niveis'=>[ $nivel_mod_2['data'], $nivel_mod_3['data']],
            'exercicios'=>[ $exerc1_mod_2['data'], $exerc2_mod_2['data'], $exerc_mod_3['data']],
            'recompensas'=>[ $recompensa1['data'], $recompensa2['data'], $exerc_mod_3['data']]
            ]
        ];
    }



    /**
     * Busca o Game na plataforma LOGIC LIVE
     */
    public function getGame(){
        return  $this->get->httpget('game');
    }

    /**
     * Busca o Modulo por ID na plataforma LOGIC LIVE
     */
    public function getModuloId($id){
        return  $this->get->httpget('modulo/', $id);
    }

    /**
     * Busca todos os mudulos na plataforma LOGIC LIVE
     */
    public function getModulo(){
        return  $this->get->httpget('modulo');
    }
}