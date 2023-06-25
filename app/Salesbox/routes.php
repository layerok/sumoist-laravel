<?php

Route::match(['get', 'post'], '/salesbox-webhook', '\App\Salesbox\WebhookController');

