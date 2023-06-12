<?php

namespace App\PosterPos\Actions;

use App\SalesBox\Entities\Category as SalesBoxCategory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class CategoryRemovedAction extends AbstractAction  {
    public function handle(): void
    {
        $client = new Client([
            'base_uri' => config('salesbox.api_base_url') . '/' . config('salesbox.company_id') . '/'
        ]);

        try {
            $response = $client->get('categories?lang=ru');
            $categories = json_decode($response->getBody(), true);
            foreach ($categories['data'] as $category) {
                $salesBoxEntity = new SalesBoxCategory($category);
                $foo = [];
            }
            $foo = [];
        } catch (ClientException $clientException) {
            $foo = [];
        }
    }
}
