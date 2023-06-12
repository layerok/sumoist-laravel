<?php

Route::group(['prefix'  =>  'webhook'], function () {
    Route::post('posterpos', 'PosterPos\WebhookController')->name('webhook.poster');
});
