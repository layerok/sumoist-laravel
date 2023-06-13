<?php

namespace App\SalesBox;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \GuzzleHttp\Client;

class Api {
    public $guzzleClient;
    public $accessToken;
    public function __construct()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());

        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            if($this->accessToken) {
                return $request->withHeader('Authorization', sprintf('Bearer %s', $this->accessToken));
            }
            return $request;
        }));

        $baseUrl = config('salesbox.api_base_url') . '/' . config('salesbox.company_id') . '/';
        $baseConfig = [
            'base_uri' => $baseUrl,
            'handler' => $stack
        ];
        $this->guzzleClient = new Client($baseConfig);
    }

    public function setAccessToken($token) {
        $this->accessToken = $token;
    }

    public function createManyCategories($categories): ResponseInterface {
        return $this->guzzleClient->post('categories/createMany', [
            'json' => [
                'categories' => $categories
            ],
        ]);
    }

    public function createCategory($category): ResponseInterface {
        return $this->createManyCategories([$category]);
    }

    public function getToken(): ResponseInterface {
        return $this->guzzleClient->post('auth', [
            'json' => [
                'phone' => config('salesbox.phone')
            ]
        ]);
    }

    public function getCategories(): ResponseInterface {
        return $this->guzzleClient->get('categories?lang=ru');
    }

    public function updateManyCategories($categories): ResponseInterface {
        return $this->guzzleClient->post('categories/updateMany', [
            'json' => [
                'categories' => $categories
            ]
        ]);
    }

    public function updateCategory($category): ResponseInterface {
        return $this->updateManyCategories([$category]);
    }

    public function deleteCategories($ids): ResponseInterface {
        // ?recursively=true
        return $this->guzzleClient->delete('categories', [
            'json' => [
                'ids' => $ids
            ]
        ]);
    }

    public function deleteCategory($id, $token): ResponseInterface {
        return $this->deleteCategories([$id], $token);
    }
}
