<?php

namespace App\PosterPos\Actions;

use App\PosterPos\Entities\Category;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use poster\src\PosterApi;

class CategoryRecoveredAction extends AbstractAction  {
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
            // $errorCode = $data->error;
            // $message = $data->message;
        } else {

            $entity = new Category($data->response);
            $client = new Client([
                'base_uri' => config('salesbox.api_base_url') . '/' . config('salesbox.company_id') . '/'
            ]);

            try {
                $authRes = $client->post('auth', [
                    'json' => [
                        'phone' => config('salesbox.phone')
                    ]
                ]);
                $authData = json_decode($authRes->getBody(), true);


                if ($authData['success']) {
                    $res = $client->post('categories/createMany', [
                        'json' => [
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
                        ],
                        'headers' => [
                            'Authorization' => 'Bearer ' . $authData['data']['token']
                        ]
                    ]);
//                    $data = json_decode($res->getBody(), true);
//                    if(!$data['success']) {
//                        $errors = $data['errors'];
//                    }
                }
            } catch (ClientException $clientException) {
                // todo: should I log it?
            }

        }
    }
}
