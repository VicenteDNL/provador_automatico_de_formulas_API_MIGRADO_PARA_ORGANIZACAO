<?php

namespace App\LogicLive\Common\HttpClient;

use App\LogicLive\Common\Enums\Actions;
use App\LogicLive\Common\Exceptions\HttpClientException;
use App\LogicLive\Common\HttpClient\Auth\Auth;
use Throwable;

class Client
{
    private ?Auth $auth;

    public function __construct(?Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param  string              $path
     * @param  ?array              $body
     * @return array
     * @throws HttpClientException
     */
    public function get(string $path, ?array $body = null)
    {
        return $this->resquest($path, Actions::GET, $body);
    }

    /**
     * @param  string              $path
     * @param  array               $body
     * @return array
     * @throws HttpClientException
     */
    public function post(string $path, array $body)
    {
        return $this->resquest($path, Actions::POST, $body);
    }

    /**
     * @param  string              $path
     * @param  array               $body
     * @return array
     * @throws HttpClientException
     */
    public function put(string $path, array $body)
    {
        return $this->resquest($path, Actions::PUT, $body);
    }

    /**
     * @param  string              $path
     * @param  array               $body
     * @return array
     * @throws HttpClientException
     */
    public function patch(string $path, array $body)
    {
        return $this->resquest($path, Actions::PATCH, $body);
    }

    /**
     * @param  string              $path
     * @return array
     * @throws HttpClientException
     */
    public function delete(string $path)
    {
        return $this->resquest($path, Actions::DELETE, null);
    }

    /**
     * @param  string              $path
     * @param  Actions             $action
     * @param  ?array              $body
     * @return array
     * @throws HttpClientException
     */
    private function resquest(string $path, Actions $action, ?array $body)
    {
        try {
            header('Content-Type: application/json');
            $ch = curl_init($path);

            if (!is_null($this->auth)) {
                $authorization = 'Authorization: Bearer ' . $this->auth->getToken();
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization ]);
            }

            if (!is_null($body)) {
                $body = json_encode($body);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action->descricao());
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result === false) {
                throw new HttpClientException(curl_error($ch));
            }

            return json_decode($result, true);
        } catch(HttpClientException $e) {
            throw $e;
        } catch(Throwable $e) {
            throw new HttpClientException('Não foi possível conectar ao Logic Live');
        }
    }
}
