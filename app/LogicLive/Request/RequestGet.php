<?php

namespace App\LogicLive\Request;

use App\LogicLive\Config\Configuracao;

class RequestGet
{
    private $configurador;

    public function __construct()
    {
        $this->configurador = new Configuracao();
    }

    public function httpget($url, $id = '', $hash = null)
    {
        $token = $hash == null ? $this->configurador->token() : $hash;
        $url = $this->configurador->url() . $url . $id;
        header('Content-Type: application/json'); // Especifique o tipo de dados
        $ch = curl_init($url); // Inicializar cURLL
        $authorization = 'Authorization: Bearer ' . $token; // Prepare o toke de autorização
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization ]); //Injete o token no cabeçalho
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Isso seguirá todos os redirecionamentos
        $result = curl_exec($ch); // Execute a instrução cURL
        curl_close($ch); // Feche a conexão cURL
        $result = json_decode($result, true);

        if ($result == null) {
            return  ['success' => false, 'msg' => 'Não foi possível conectar ao Logic Live', 'data' => ''];
        } elseif (!$result['status']) {
            return  ['success' => false, 'msg' => '', 'data' => $result['data']];
        } elseif ($result['status']) {
            return  ['success' => true, 'msg' => '', 'data' => $result['data']];
        } else {
            return  ['success' => false, 'msg' => 'Não foi possível conectar ao Logic Live', 'data' => ''];
        }
    }
}
