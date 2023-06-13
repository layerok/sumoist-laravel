<?php

namespace App\Salesbox;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \GuzzleHttp\Client;

class SalesboxApi {
    public $guzzleClient;
    public $accessToken;
    public $baseUrl;
    public $companyId;
    public $phone;
    public function __construct(array $config = [])
    {
        $this->baseUrl = $config['api_base_url'];
        $this->phone = $config['phone'];
        $this->companyId = $config['company_id'];

        $stack = HandlerStack::create();

        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            if($this->accessToken) {
                return $request->withHeader('Authorization', sprintf('Bearer %s', $this->accessToken));
            }
            return $request;
        }));

        $baseUrl = $this->baseUrl . '/' . $this->companyId. '/';
        $baseConfig = [
            'base_uri' => $baseUrl,
            'handler' => $stack
        ];
        $this->guzzleClient = new Client($baseConfig);
    }

    public function setAccessToken($token) {
        $this->accessToken = $token;
    }

    public function getToken(): ResponseInterface {
        return $this->guzzleClient->post('auth', [
            'json' => [
                'phone' => $this->phone
            ]
        ]);
    }

    public function getCategories(array $guzzleOptions = []): ResponseInterface {
        return $this->guzzleClient->get('categories?lang=ru', $guzzleOptions);
    }

    public function createManyCategories(array $categories, array $guzzleOptions = []): ResponseInterface {
        $json = [
            'categories' => $categories
        ];
        $options = [
            'json' => $json,
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        return $this->guzzleClient->post('categories/createMany', $mergedOptions);
    }

    public function createCategory(array $category, array $guzzleOptions = []): ResponseInterface {
        return $this->createManyCategories([$category], $guzzleOptions);
    }

    public function updateManyCategories(array $categories, array $guzzleOptions = []): ResponseInterface {
        $json = [
            'categories' => $categories
        ];
        $options = [
            'json' => $json
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        return $this->guzzleClient->post('categories/updateMany', $mergedOptions);
    }

    public function updateCategory(array $category): ResponseInterface {
        return $this->updateManyCategories([$category]);
    }

    public function deleteManyCategories($ids, array $guzzleOptions = []): ResponseInterface {
        $json = [
            'ids' => $ids
        ];
        $options = [
            'json' => $json
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        // ?recursively=true
        return $this->guzzleClient->delete('categories', $mergedOptions);
    }

    public function deleteCategory($id, array $guzzleOptions = []): ResponseInterface {
        return $this->deleteManyCategories([$id], $guzzleOptions);
    }
}
