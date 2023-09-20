<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\NivelModel;
use App\LogicLive\Config;

class NivelResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct()
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('nivel');

        $auth = new Auth(['token' => $this->config->token()]);
        $this->client = new Client($auth);
    }

    /**
     * @return ?NivelModel
     */
    public function get(): ?NivelModel
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                return new NivelModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @param  NivelModel  $nivel
     * @return ?NivelModel
     */
    public function create(NivelModel $nivel): ?NivelModel
    {
        try {
            $result = $this->client->post($this->url, $nivel->toArray());

            if ($result['status']) {
                return new NivelModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $result = $this->client->delete($this->url . '/' . $id);

            if ($result['status']) {
                return true;
            }

            return false;
        } catch(HttpClientException $e) {
            return false;
        }
    }

    /**
     * @param  int         $id
     * @param  NivelModel  $nivel
     * @return ?NivelModel
     */
    public function update($id, NivelModel $nivel): ?NivelModel
    {
        try {
            $result = $this->client->patch($this->url . '/' . $id, $nivel->toArray());

            if ($result['status']) {
                return new NivelModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @return NivelModel[]
     */
    public function all(): array
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                $items = [];

                foreach ($result['data'] as $data) {
                    $items[] = new NivelModel($data);
                }
                return  $items ;
            }

            return [];
        } catch(HttpClientException $e) {
            return [];
        }
    }
}
