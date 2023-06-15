<?php

namespace App\Salesbox;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use \GuzzleHttp\Client;

class SalesboxApi {
    protected $guzzleClient;
    protected $openApiId;
    protected $companyId;
    protected $phone;
    protected $lang;
    protected $accessToken;

    public function __construct(array $config = [])
    {
        $this->openApiId = $config['open_api_id'];
        $this->phone = $config['phone'];
        $this->companyId = $config['company_id'];
        $this->lang = $config['lang'];

        $baseUrl ='https://prod.salesbox.me/api/' . $this->openApiId. '/';

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

        $baseConfig = [
            'base_uri' => $baseUrl,
            'handler' => $handler
        ];
        $this->guzzleClient = new Client($baseConfig);
    }

    protected function setAccessToken($token): void {
        $this->accessToken = $token;
    }

    public function getAccessToken(array $params = []): array {
        $res = $this->guzzleClient->post('auth', [
            'json' => [
                'phone' => $this->phone
            ],
            'query' => $params
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

    public function getCategories($params = [], array $guzzleOptions = []): array {
        $query = [
            'lang' => $this->lang
        ];
        $options = [
            'query' => array_merge($query, $params)
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->get('categories', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function createCategory($params = [], array $guzzleOptions = []): array {
        return $this->createManyCategories([
            'categories' => [$params['category']]
        ], $guzzleOptions);
    }

    public function updateCategory(array $params = [], array $guzzleOptions = []): array {
        return $this->updateManyCategories([
            'categories' => [$params['category']]
        ], $guzzleOptions);
    }

    public function deleteCategory(array $params = [], array $guzzleOptions = []): array {
        return $this->deleteManyCategories([
            'ids' => [$params['id']],
            'recursively' => $params['recursively']
        ], $guzzleOptions);
    }

    public function createManyCategories(array $params = [], array $guzzleOptions = []): array {
        $json = [
            'categories' => $params['categories']
        ];
        $options = [
            'json' => $json,
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('categories/createMany', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function updateManyCategories(array $params = [], array $guzzleOptions = []): array {
        $json = [
            'categories' => $params['categories']
        ];
        $options = [
            'json' => $json
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('categories/updateMany', $mergedOptions);
        return json_decode($res->getBody(), true);
    }


    public function deleteManyCategories(array $params = [], array $guzzleOptions = []): array {
        $json = [
            'ids' => $params['ids']
        ];
        $options = [
            'json' => $json
        ];
        if($params['recursively']) {
            $options['query'] = [
                'recursively' => true
            ];
        }
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->delete('categories', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function getOffers(array $params = [], array $guzzleOptions = []): array {
        // onlyAvailable, isGrouped, page, pageSize - query params
        $query = [
            'lang' => $this->lang
        ];

        $options = [
            'query' => array_merge($query, $params)
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->get('offers/filter', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function createManyOffers(array $params = [], array $guzzleOptions = []): array {
        $options = [
            'json' => [
                'offers' => $params['offers']
            ]
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('offers/createMany', $mergedOptions);
        return json_decode($res->getBody(), true);
    }

    public function getCategoryByExternalId($id): ?array {
        $categoriesRes = $this->getCategories();
        $collection = collect($categoriesRes['data']);
        return $collection->firstWhere('externalId', $id);
    }

    public function deleteCategoryByExternalId($id, $recursively = false): ?array {
        $category = $this->getCategoryByExternalId($id);

        if (!$category) {
            // todo: should I throw exception if category doesn't exist?
            return null;
        }

        return $this->deleteCategory([
            'id' => $category['id'],
            'recursively' => $recursively
        ], []);
    }
}
