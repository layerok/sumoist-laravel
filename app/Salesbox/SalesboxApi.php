<?php

namespace App\Salesbox;

use App\Salesbox\meta\SalesboxApiResponse_meta;
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
    /**
     * @property HandlerStack $handler
     */
    protected $handler;

    public function __construct(array $config = [])
    {
        $this->openApiId = $config['open_api_id'];
        $this->phone = $config['phone'];
        $this->companyId = $config['company_id'];
        $this->lang = $config['lang'];

        $baseUrl ='https://prod.salesbox.me/api/' . $this->openApiId. '/';

        $this->handler = HandlerStack::create();

        $this->handler->push(Middleware::mapRequest(function (RequestInterface $request) {
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
            'handler' => $this->handler
        ];
        $this->guzzleClient = new Client($baseConfig);
    }

    public function getGuzzleHandler(): HandlerStack {
        return $this->handler;
    }

    protected function setAccessToken($token): void {
        $this->accessToken = $token;
    }

    /**
     * @param array $params
     * @return SalesboxApiResponse_meta
     */
    public function getAccessToken(array $params = []) {
        $res = $this->guzzleClient->post('auth', [
            'json' => [
                'phone' => $this->phone
            ],
            'query' => $params
        ]);
        return json_decode($res->getBody());
    }

    public function authenticate($providedToken = ''): string {
        if($providedToken) {
            $this->setAccessToken($providedToken);
            return $providedToken;
        }

        $authRes = $this->getAccessToken();

        $token = $authRes->data->token;

        $this->setAccessToken($token);
        return $token;
    }

    /**
     * @param $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function getCategories($params = [], array $guzzleOptions = []) {
        $query = [
            'lang' => $this->lang
        ];
        $options = [
            'query' => array_merge($query, $params)
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->get('categories', $mergedOptions);
        return json_decode($res->getBody());
    }

    /**
     * @param $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function createCategory($params = [], array $guzzleOptions = []) {
        return $this->createManyCategories([
            'categories' => [$params['category']]
        ], $guzzleOptions);
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function updateCategory(array $params = [], array $guzzleOptions = []) {
        return $this->updateManyCategories([
            'categories' => [$params['category']]
        ], $guzzleOptions);
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function deleteCategory(array $params = [], array $guzzleOptions = []) {
        return $this->deleteManyCategories([
            'ids' => [$params['id']],
            'recursively' => $params['recursively']
        ], $guzzleOptions);
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function createManyCategories(array $params = [], array $guzzleOptions = []) {
        $json = [
            'categories' => $params['categories']
        ];
        $options = [
            'json' => $json,
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('categories/createMany', $mergedOptions);
        return json_decode($res->getBody());
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function updateManyCategories(array $params = [], array $guzzleOptions = []) {
        $json = [
            'categories' => $params['categories']
        ];
        $options = [
            'json' => $json
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('categories/updateMany', $mergedOptions);
        return json_decode($res->getBody());
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function deleteManyCategories(array $params = [], array $guzzleOptions = []) {
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
        return json_decode($res->getBody());
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function getOffers(array $params = [], array $guzzleOptions = []) {
        // onlyAvailable, isGrouped, page, pageSize - query params
        $query = [
            'lang' => $this->lang
        ];

        $options = [
            'query' => array_merge($query, $params)
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->get('offers/filter', $mergedOptions);
        return json_decode($res->getBody());
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function createManyOffers(array $params = [], array $guzzleOptions = []) {
        $options = [
            'json' => [
                'offers' => $params['offers']
            ]
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('offers/createMany', $mergedOptions);
        return json_decode($res->getBody());
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function updateManyOffers(array $params = [], array $guzzleOptions = []) {
        $options = [
            'json' => [
                'offers' => $params['offers']
            ]
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->post('offers/updateMany', $mergedOptions);
        return json_decode($res->getBody());
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return SalesboxApiResponse_meta
     */
    public function deleteManyOffers(array $params = [], array $guzzleOptions = []) {
        $json = [
            'ids' => $params['ids']
        ];
        $options = [
            'json' => $json
        ];
        $mergedOptions = array_merge($options, $guzzleOptions);
        $res = $this->guzzleClient->delete('offers', $mergedOptions);
        return json_decode($res->getBody());
    }
}
