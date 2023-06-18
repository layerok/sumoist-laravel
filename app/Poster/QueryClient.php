<?php

namespace App\Poster;

class QueryClient
{
    public function fetch(Query $query)
    {
        return perRequestCache()->rememberForever($query->key, function () use ($query) {
            return call_user_func($query->queryFn);
        });
    }

    public function invalidateQuery(Query $query)
    {
        perRequestCache()->forget($query->key);
    }
}
