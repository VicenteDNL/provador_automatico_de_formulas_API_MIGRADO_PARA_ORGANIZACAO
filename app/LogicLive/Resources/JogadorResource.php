<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\JogadorModel;
use App\LogicLive\Config;

class JogadorResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct(string $hash)
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('jogador');

        $auth = new Auth(['token' => $hash]);
        $this->client = new Client($auth);
    }

    /**
     * @param  string        $hash
     * @return ?JogadorModel
     */
    public function get(): ?JogadorModel
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                return new JogadorModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }
}
