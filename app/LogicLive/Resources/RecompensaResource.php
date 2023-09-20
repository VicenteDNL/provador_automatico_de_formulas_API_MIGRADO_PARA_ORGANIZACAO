<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\RecompensaModel;
use App\LogicLive\Config;

class RecompensaResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct()
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('recompensa');

        $auth = new Auth(['token' => $this->config->token()]);
        $this->client = new Client($auth);
    }

    /**
     * @return ?RecompensaModel
     */
    public function get(): ?RecompensaModel
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                return new RecompensaModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @param  RecompensaModel  $nivel
     * @return ?RecompensaModel
     */
    public function create(RecompensaModel $nivel): ?RecompensaModel
    {
        try {
            $result = $this->client->post($this->url, $nivel->toArray());

            if ($result['status']) {
                return new RecompensaModel($result['data']);
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
     * @param  int              $id
     * @param  RecompensaModel  $nivel
     * @return ?RecompensaModel
     */
    public function update($id, RecompensaModel $nivel): ?RecompensaModel
    {
        try {
            $result = $this->client->patch($this->url . '/' . $id, $nivel->toArray());

            if ($result['status']) {
                return new RecompensaModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @return RecompensaModel[]
     */
    public function all(): array
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                $items = [];

                foreach ($result['data'] as $data) {
                    $items[] = new RecompensaModel($data);
                }
                return  $items ;
            }

            return [];
        } catch(HttpClientException $e) {
            return [];
        }
    }
}
