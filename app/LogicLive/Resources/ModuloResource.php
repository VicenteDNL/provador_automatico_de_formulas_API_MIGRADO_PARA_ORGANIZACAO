<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\ModuloModel;
use App\LogicLive\Config;

class ModuloResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct()
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('modulo');

        $auth = new Auth(['token' => $this->config->token()]);
        $this->client = new Client($auth);
    }

    /**
     * @return ?ModuloModel
     */
    public function get(): ?ModuloModel
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                return new ModuloModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @param  ModuloModel  $modulo
     * @return ?ModuloModel
     */
    public function create(ModuloModel $modulo): ?ModuloModel
    {
        try {
            $result = $this->client->post($this->url, $modulo->toArray());

            if ($result['status']) {
                return new ModuloModel($result['data']);
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
     * @param  int          $id
     * @param  ModuloModel  $modulo
     * @return ?ModuloModel
     */
    public function update($id, ModuloModel $modulo): ?ModuloModel
    {
        try {
            $result = $this->client->patch($this->url . '/' . $id, $modulo->toArray());

            if ($result['status']) {
                return new ModuloModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @return ModuloModel[]
     */
    public function all(): array
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                $items = [];

                foreach ($result['data'] as $data) {
                    $items[] = new ModuloModel($data);
                }
                return  $items ;
            }

            return [];
        } catch(HttpClientException $e) {
            return [];
        }
    }
}
