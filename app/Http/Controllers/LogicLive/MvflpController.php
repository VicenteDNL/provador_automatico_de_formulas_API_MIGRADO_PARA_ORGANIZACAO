<?php

namespace App\Http\Controllers\LogicLive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MvflpController extends Controller
{

    public function __construct( )
    {
        $this->configurador = new ConfiguracaoController;
    }

    public function salvarNivel()
    {     


        $this->configurador->criarGameEndModulos();


        $token = $this->configurador->token();
        $url = $this->configurador->url().'v1/nivel';
        $post = []; // Matriz de dados 

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
        // var_dump ($result); // Retorna os dados recebidos
    }
}
