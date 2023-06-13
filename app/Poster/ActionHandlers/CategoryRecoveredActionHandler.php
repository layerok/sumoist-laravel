<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Entities\Category;
use App\Poster\PosterApiException;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;

class CategoryRecoveredActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        PosterApi::init([
            'account_name' => config('poster.account_name'),
            'access_token' => config('poster.access_token'),
        ]);

        $data = PosterApi::menu()->getCategory([
            'category_id' => $this->getObjectId()
        ]);

        if (!isset($data->response)) {
            throw new PosterApiException($data->error);
        }

        // todo: do I need this abstraction?
        $entity = new Category($data->response);

        $authRes = SalesboxApi::getToken();
        $authData = json_decode($authRes->getBody(), true);

        $token = $authData['data']['token'];
        SalesboxApi::setAccessToken($token);

        $recoveredCategory = [
            'available' => !$entity->isHidden(),
            'names' => [
                [
                    'name' => $entity->getName(),
                    'lang' => 'uk'
                ]
            ],
//                                    'previewURL' => $entity->getPhoto(),
            'externalId' => $this->getObjectId()
        ];

        SalesboxApi::createCategory($recoveredCategory);

        return true;
    }
}
