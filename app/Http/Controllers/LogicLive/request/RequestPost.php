<?php

namespace App\Http\Controllers\LogicLive\request;

use App\Http\Controllers\LogicLive\config\Configuracao;

class RequestPost
{
    public function __construct( )
    {
        $this->configurador = new Configuracao;
    }

    public function httppost($url, $post=''){
        $token = $this->configurador->token();
        $url = $this->configurador->url().$url;
        $post =  $post;// Matriz de dados 
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

        if($result==null){
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }
        elseif(!$result['status']){
            return  ['success'=>false ,'msg'=>"", 'data'=>$result['data']];
        }
        elseif($result['status']){
            return  ['success'=>true ,'msg'=>"", 'data'=>$result['data']];
        }
        else{
            return  ['success'=>false ,'msg'=>"Não foi possível conectar ao Logic Live", 'data'=>''];
        }
    }

}