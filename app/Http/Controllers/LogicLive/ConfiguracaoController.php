<?php

namespace App\Http\Controllers\LogicLive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    private $ativar=true;
    private $token = "87891c84ce0ab019fbae7f0a517faf591lt7to75RkkynGLkr/rh+Cz/dkRujWwFkBVT+0EoPa7oAj3DP285hdrhiumv5HxM";
    private $url ='http://api.thelogiclive.com/api/';

    private $game =    ['gam_nome'=>'Árvore de Refutação', 'gam_descricao'=> 'Módulo de validação de Fórmulas da lógica proposicional através do método de Árvore de Refutação', 'gam_ativo'=>1];
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


    public function criarGameEndModulos(){

        $token = $this->token();
        $url = $this->url().'v1/game';
        $post =  $this->game;// Matriz de dados 
        header('Content-Type: application/json'); // Especifique o tipo de dados
        $ch = curl_init($url); // Inicializar cURLL
        $post = json_encode($post); // Codifique a matriz de dados em uma string JSON
        $authorization = "Authorization: Bearer ".$token; // Prepare o toke de autorização
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); //Injete o token no cabeçalho
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Especifique o método de solicitação como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Seta os campos postados
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Isso seguirá todos os redirecionamentos
        $result = curl_exec($ch); // Execute a instrução cURL
        curl_close($ch); // Feche a conexão cURL
        $result= json_decode($result, true);

        if($result['status']){
            $resposta = $this->criarModulos($result['data']);
            return  $resposta;
        }
        else{
            return false;
        }
        var_dump ($result['data']); // Retorna os dados recebidos
    }

    public function criarModulos($game){

        $token = $this->token();
        $url = $this->url().'v1/modulo';

        $this->modulo1['gam_codigo'] = $game['gam_codigo'];
        $post = $this->modulo1 ; // Matriz de dados 
        header('Content-Type: application/json'); // Especifique o tipo de dados
        $ch = curl_init($url); // Inicializar cURLL
        $post = json_encode($post); // Codifique a matriz de dados em uma string JSON
        $authorization = "Authorization: Bearer ".$token; // Prepare o toke de autorização
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); //Injete o token no cabeçalho
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Especifique o método de solicitação como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Seta os campos postados
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Isso seguirá todos os redirecionamentos
        $result = curl_exec($ch); // Execute a instrução cURL
        curl_close($ch); // Feche a conexão cURL
        $result= json_decode($result, true);
        if($result['status']==false){
            return false;
        }


        $this->modulo2['gam_codigo'] = $game['gam_codigo'];
        $post = $this->modulo2; // Matriz de dados 
        header('Content-Type: application/json'); // Especifique o tipo de dados
        $ch = curl_init($url); // Inicializar cURLL
        $post = json_encode($post); // Codifique a matriz de dados em uma string JSON
        $authorization = "Authorization: Bearer ".$token; // Prepare o toke de autorização
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); //Injete o token no cabeçalho
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Especifique o método de solicitação como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Seta os campos postados
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Isso seguirá todos os redirecionamentos
        $result = curl_exec($ch); // Execute a instrução cURL
        curl_close($ch); // Feche a conexão cURL
        $result= json_decode($result, true);
        if($result['status']==false){
            return false;
        }

        $this->modulo3['gam_codigo'] = $game['gam_codigo'];
        $post =$this->modulo3 ; // Matriz de dados 
        header('Content-Type: application/json'); // Especifique o tipo de dados
        $ch = curl_init($url); // Inicializar cURLL
        $post = json_encode($post); // Codifique a matriz de dados em uma string JSON
        $authorization = "Authorization: Bearer ".$token; // Prepare o toke de autorização
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); //Injete o token no cabeçalho
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Especifique o método de solicitação como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Seta os campos postados
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Isso seguirá todos os redirecionamentos
        $result = curl_exec($ch); // Execute a instrução cURL
        curl_close($ch); // Feche a conexão cURL
        $result= json_decode($result, true);
        if($result['status']==false){
            return false;
        }

        return true;
    

    }

    

}
