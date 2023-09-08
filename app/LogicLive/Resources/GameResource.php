<?php

namespace App\LogicLive\Resources;

use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use App\LogicLive\Common\HttpClient\Client;
use App\LogicLive\Common\Models\GameModel;
use App\LogicLive\Config;

class GameResource
{
    private Client $client;
    private Config $config;
    private string $url;

    public function __construct()
    {
        $this->config = new Config();
        $this->url = $this->config->urlAPI('game');

        $auth = new Auth(['token' => $this->config->token()]);
        $this->client = new Client($auth);
    }

    /**
     * @return ?GameModel
     */
    public function get(): ?GameModel
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                return new GameModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @param  GameModel  $game
     * @return ?GameModel
     */
    public function create(GameModel $game): ?GameModel
    {
        try {
            $result = $this->client->post($this->url, $game->toArray());

            if ($result['status']) {
                return new GameModel($result['data']);
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
     * @param  int        $id
     * @param  GameModel  $game
     * @return ?GameModel
     */
    public function update($id, GameModel $game): ?GameModel
    {
        try {
            $result = $this->client->patch($this->url . '/' . $id, $game->toArray());

            if ($result['status']) {
                return new GameModel($result['data']);
            }

            return null;
        } catch(HttpClientException $e) {
            return null;
        }
    }

    /**
     * @return GameModel[]
     */
    public function all(): array
    {
        try {
            $result = $this->client->get($this->url);

            if ($result['status']) {
                $items = [];

                foreach ($result['data'] as $data) {
                    $items[] = new GameModel($data);
                }
                return  $items ;
            }

            return [];
        } catch(HttpClientException $e) {
            return [];
        }
    }
}
