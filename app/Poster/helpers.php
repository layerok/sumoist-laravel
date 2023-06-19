<?php

if(!function_exists('build_tree')) {
    function build_tree($list, $parentId = null)
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
                'children' => build_tree($list, $parent['id'])
            ];
        }
        return $tree;
    }
}

if(!function_exists('find_parents')) {
    function find_parents($list, $id) {
        $arr = [];
        foreach($list as $item) {
            if($item['id'] === $id) {
                return array_merge([$item['parent_id']], find_parents($list, $item['parent_id']));
            }
        }
        return $arr;
    }
}
