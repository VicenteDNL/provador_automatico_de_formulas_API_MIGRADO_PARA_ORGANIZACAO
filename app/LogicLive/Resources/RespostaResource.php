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

    public function __construct()
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('resposta');

        $auth = new Auth(['token' => $this->config->token()]);
        $this->client = new Client($auth);
    }

    /**
     * @param  RespostaModel  $nivel
     * @return ?RespostaModel
     */
    public function create(RespostaModel $nivel): ?RespostaModel
    {
        try {
            $result = $this->client->post($this->url, $nivel->toArray());

            if ($result['status']) {
                return new RespostaModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }
}
