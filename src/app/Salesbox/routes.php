<?php
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/salesbox-webhook', '\App\Salesbox\WebhookController');

