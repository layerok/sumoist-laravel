<?php

namespace App\Salesbox;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \GuzzleHttp\Client;

class SalesboxApi {
    protected $guzzleClient;
    protected $baseUrl;
    protected $companyId;
    protected $phone;
    protected $lang;
    protected $guzzleHandler;
    protected $headers = [];

    public function __construct(array $config = [])
    {
        $this->baseUrl = $config['base_url'];
        $this->phone = $config['phone'];
        $this->companyId = $config['company_id'];
        $this->lang = $config['lang'];

        $this->guzzleHandler = HandlerStack::create();

        $this->guzzleHandler->push(Middleware::mapRequest(function (RequestInterface $request) {
            if(count($this->headers) > 0) {
                return Utils::modifyRequest($request, [
                    'set_headers' => $this->headers
                ]);
            }
            return $request;
        }));

        $baseUrl = $this->baseUrl . '/' . $this->companyId. '/';
        $baseConfig = [
            'base_uri' => $baseUrl,
            'handler' => $this->guzzleHandler
        ];
        $this->guzzleClient = new Client($baseConfig);
    }

    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    public function getGuzzleHandler(): HandlerStack {
        return $this->guzzleHandler;
    }

    public function getToken(): ResponseInterface {
        return $this->guzzleClient->post('auth', [
            'json' => [
                'phone' => $this->phone
            ]
        ]);
    }

    public function getCategories(array $guzzleOptions = []): ResponseInterface {
        $query = [
            'lang' => $this->lang
        ];
        $options = [
            'query' => $query
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        return $this->guzzleClient->get('categories', $mergedOptions);
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

    public function deleteManyCategories($ids, array $guzzleOptions = [], $recursively = false): ResponseInterface {
        $json = [
            'ids' => $ids
        ];
        $options = [
            'json' => $json
        ];
        if($recursively) {
            $options['query'] = [
                'recursively' => true
            ];
        }
        $mergedOptions = array_merge($options, $guzzleOptions);
        return $this->guzzleClient->delete('categories', $mergedOptions);
    }

    public function deleteCategory($id, array $guzzleOptions = [], $recursively = false): ResponseInterface {
        return $this->deleteManyCategories([$id], $guzzleOptions, $recursively);
    }
}
