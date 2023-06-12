<?php

namespace App\PosterPos\Actions;

use App\PosterPos\Entities\Category;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use poster\src\PosterApi;

class CategoryAddedAction extends AbstractAction {
    public function handle(): void
    {
        PosterApi::init([
            'account_name' => config('poster.account_name'),
            'access_token' => config('poster.access_token'),
        ]);


        $data = PosterApi::menu()->getCategory([
            'category_id' => $this->getObjectId()
        ]);

        if (!isset($data->response)) {
            // todo: should I log it?
            $errorCode = $data->error;
            $message = $data->message;
        } else {

            $entity = new Category($data->response);
            $client = new Client([
                'base_uri' => config('salesbox.api_base_url') . '/' . config('salesbox.company_id') . '/'
            ]);

            try {
                $config = [
                    'json' => [
                        'phone' => config('salesbox.phone')
                    ]
                ];

                $authRes = $client->post('auth', $config);
                $authData = json_decode($authRes->getBody(), true);


                if (!$authData['success']) {
                    // todo: failed to get token
                    $foo = [];
                } else {
                    $token = $authData['data']['token'];
                    $headers = [
                        'Authorization' => 'Bearer ' . $token
                    ];
                    $json = [
                        'categories' => [
                            [
                                'available' => !$entity->isHidden(),
                                'names' => [
                                    [
                                        'name' => $entity->getName(),
                                        'lang' => 'uk'
                                    ]
                                ],
//                                    'previewURL' => $entity->getPhoto(),
                                'externalId' => $this->getObjectId()
                            ]
                        ]
                    ];
                    $config = [
                        'json' => $json,
                        'headers' => $headers
                    ];
                    $res = $client->post('categories/createMany', $config);
                    $data = json_decode($res->getBody(), true);
                    if(!$data['success']) {
                        $errors = $data['errors'];
                    }
                    $foo = [];
                }


                $foo = [];
            } catch (ClientException $clientException) {
                // todo: should I log it?
                $foo = [];
            }

        }
    }
}
