<?php

namespace App\PosterPos\Handlers;

use App\PosterPos\Entities\Category;
use poster\src\PosterApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class CategoryHandler extends AbstractHandler {
    public function handle(): void {
        if($this->isRemoved()) {
            PosterApi::init([
                'account_name' => config('poster.account_name'),
                'access_token' => config('poster.access_token'),
            ]);

            $data = PosterApi::menu()->getCategory([
                'category_id' => $this->getObjectId()
            ]);

            if(isset($data->response)) {
                $categoryEntity = new Category($data->response);
                $companyId = config('salesbox.company_id');
                $client = new Client([
                    'base_uri' => 'https://prod.salesbox.me/api/' . $companyId . '/'
                ]);

                try {
                    $res = $client->get('categories?lang=ru');
                    $name = $categoryEntity->getName();
                    $photo = $categoryEntity->getPhoto();
                    $hidden = $categoryEntity->isHidden();
                    $sort_order = $categoryEntity->getSortOrder();
                    $left = $categoryEntity->getLeft();
                    $right = $categoryEntity->getRight();
                    $level = $categoryEntity->getLevel();
                    $category_tag = $categoryEntity->getTag();
                    $tax_id = $categoryEntity->getTaxId();
                    $no_discount = $categoryEntity->getNoDiscount();
                    $parent_category = $categoryEntity->getParentCategory();
                    $category_id = $categoryEntity->getId();
                    $category_color = $categoryEntity->getColor();
                    $fiscal = $categoryEntity->getFiscal();

                    $visible = $categoryEntity->getVisible();
                    foreach ($visible as $value) {
                        $spot_id = $value->spot_id;
                        $visible = (bool)$value->visible;
                    }
                } catch (ClientException $clientException) {
                    $foo = [];
                }




            } else {
                // todo: should I log it?
                $errorCode = $data->error;
                $message = $data->message;
            }

        }

        if($this->isAdded()) {
            // add product
        }

        if($this->isChanged()) {
            // change product
        }
    }
}

