<?php

namespace App\Poster;

use App\Poster\Exceptions\PosterApiException;

class Utils
{
    static public function assertResponse($response, $method)
    {
        if (!isset($response->response) || !$response->response) {
            throw new PosterApiException($method, $response);
        }
        return $response;
    }

    static public function poster_upload_url($path) {
        return config('poster.url') . $path;
    }

    static public function build_tree($list, $parentId = null)
    {
        $tree = [];
        $parents = [];
        foreach ($list as $node) {
            if($node['parent_id'] === $parentId) {
                $parents[] = $node;
            }
        }
        foreach ($parents as $parent) {
            $tree[] = [
                'parent_id' => $parent['parent_id'],
                'id' => $parent['id'],
                'children' => self::build_tree($list, $parent['id'])
            ];
        }
        return $tree;
    }

    static public function find_parents($list, $id) {
        $arr = [];
        foreach($list as $item) {
            if($item['id'] === $id) {
                return array_merge([$item['parent_id']], self::find_parents($list, $item['parent_id']));
            }
        }
        return $arr;
    }
}
