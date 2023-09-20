<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\RespostaModel;
use App\LogicLive\Config;

class RespostaResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct(string $hash)
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('respostas');

        $auth = new Auth(['token' => $hash]);
        $this->client = new Client($auth);
    }

    /**
     * @param  RespostaModel  $resposta
     * @return ?RespostaModel
     */
    public function create(RespostaModel $resposta): bool
    {
        try {
            $result = $this->client->post($this->url, $resposta->toArray());

            if ($result['status']) {
                return true;
            }

            return false;
        } catch(HttpClientException $e) {
            return false;
        }
    }
}
