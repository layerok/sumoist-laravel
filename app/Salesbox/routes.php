<?php

Route::match(['get', 'post'], '/salesbox-webhook', function () {
    $foo = [];
    return response('ok');
});

