<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\ExercicioModel;
use App\LogicLive\Config;

class ExercicioResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct()
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('exercicio');

        $auth = new Auth(['token' => $this->config->token()]);
        $this->client = new Client($auth);
    }

    /**
     * @return ?ExercicioModel
     */
    public function get(): ?ExercicioModel
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                return new ExercicioModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @param  ExercicioModel  $exercicio
     * @return ?ExercicioModel
     */
    public function create(ExercicioModel $exercicio): ?ExercicioModel
    {
        try {
            $result = $this->client->post($this->url, $exercicio->toArray());

            if ($result['status']) {
                return new ExercicioModel($result['data']);
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
     * @param  int             $id
     * @param  ExercicioModel  $exercicio
     * @return ?ExercicioModel
     */
    public function update($id, ExercicioModel $exercicio): ?ExercicioModel
    {
        try {
            $result = $this->client->patch($this->url . '/' . $id, $exercicio->toArray());

            if ($result['status']) {
                return new ExercicioModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @return ExercicioModel[]
     */
    public function all(): array
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                $items = [];

                foreach ($result['data'] as $data) {
                    $items[] = new ExercicioModel($data);
                }
                return  $items ;
            }

            return [];
        } catch(HttpClientException $e) {
            return [];
        }
    }
}
