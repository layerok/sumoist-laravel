<?php

namespace App\Salesbox;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use \GuzzleHttp\Client;

class SalesboxApi {
    protected $guzzleClient;
    protected $baseUrl;
    protected $companyId;
    protected $phone;
    protected $lang;
    protected $accessToken;

    public function __construct(array $config = [])
    {
        $this->baseUrl = $config['base_url'];
        $this->phone = $config['phone'];
        $this->companyId = $config['company_id'];
        $this->lang = $config['lang'];

        $handler = HandlerStack::create();

        $handler->push(Middleware::mapRequest(function (RequestInterface $request) {
            if($this->accessToken) {
                return Utils::modifyRequest($request, [
                    'set_headers' => [
                        'Authorization' => sprintf('Bearer %s', $this->accessToken)
                    ]
                ]);
            }
            return $request;
        }));

        $baseUrl = $this->baseUrl . '/' . $this->companyId. '/';
        $baseConfig = [
            'base_uri' => $baseUrl,
            'handler' => $handler
        ];
        $this->guzzleClient = new Client($baseConfig);
    }

    public function setAccessToken($token): void {
        $this->accessToken = $token;
    }

    public function getAccessToken(): array {
        $res = $this->guzzleClient->post('auth', [
            'json' => [
                'phone' => $this->phone
            ]
        ]);
        return json_decode($res->getBody(), true);
    }

    public function authenticate($providedToken = ''): string {
        if($providedToken) {
            $this->setAccessToken($providedToken);
            return $providedToken;
        }

        $authRes = $this->getAccessToken();

        $token = $authRes['data']['token'];

        $this->setAccessToken($token);
        return $token;
    }

    public function getCategories(array $guzzleOptions = []): array {
        $query = [
            'lang' => $this->lang
        ];
        $options = [
            'query' => $query
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->get('categories', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function createManyCategories(array $categories, array $guzzleOptions = []): array {
        $json = [
            'categories' => $categories
        ];
        $options = [
            'json' => $json,
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('categories/createMany', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function createCategory(array $category, array $guzzleOptions = []): array {
        return $this->createManyCategories([$category], $guzzleOptions);
    }

    public function updateManyCategories(array $categories, array $guzzleOptions = []): array {
        $json = [
            'categories' => $categories
        ];
        $options = [
            'json' => $json
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('categories/updateMany', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function updateCategory(array $category): array {
        return $this->updateManyCategories([$category]);
    }

    public function deleteManyCategories($ids, array $guzzleOptions = [], $recursively = false): array {
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
        $res = $this->guzzleClient->delete('categories', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function deleteCategory($id, array $guzzleOptions = [], $recursively = false): array {
        return $this->deleteManyCategories([$id], $guzzleOptions, $recursively);
    }

//    public function getOffers(): ResponseInterface {
//
//    }
}
