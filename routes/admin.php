<?php


Route::group(['prefix'  =>  'admin'], function () {

    Route::get('login',     'Admin\LoginController@showLoginForm')  ->name('admin.login');
    Route::post('login',    'Admin\LoginController@login')          ->name('admin.login.post');
    Route::get('logout',    'Admin\LoginController@logout')         ->name('admin.logout');

    Route::group(['middleware' => ['auth:admin']], function () {

        Route::get('/', function () {

            return view('admin.dashboard.index', [
                'products_count'            => App\Models\Product::all()->count(),
                'spots_count'               => App\Models\Spot::all()->count(),
                'attribute_values_count'    => App\Models\AttributeValue::all()->count(),
                'categories_count'          => App\Models\Category::all()->count(),
                'users_count'               => App\Models\User::all()->count()
            ]);
        })->name('admin.dashboard');
        Route::get('/settings',     'Admin\SettingController@index')    ->name('admin.settings');
        Route::post('/settings',    'Admin\SettingController@update')   ->name('admin.settings.update');

        Route::group(['prefix'  =>   'categories'], function() {

            Route::get('/',             'Admin\CategoryController@index')   ->name('admin.categories.index');
            Route::get('/create',       'Admin\CategoryController@create')  ->name('admin.categories.create');
            Route::post('/store',       'Admin\CategoryController@store')   ->name('admin.categories.store');
            Route::get('/{id}/edit',    'Admin\CategoryController@edit')    ->name('admin.categories.edit');
            Route::post('/update',      'Admin\CategoryController@update')  ->name('admin.categories.update');
            Route::get('/{id}/delete',  'Admin\CategoryController@delete')  ->name('admin.categories.delete');

        });

        Route::group(['prefix'  =>   'ingredients'], function() {

            Route::get('/',             'Admin\IngredientController@index') ->name('admin.ingredients.index');
            Route::get('/create',       'Admin\IngredientController@create')->name('admin.ingredients.create');
            Route::post('/store',       'Admin\IngredientController@store') ->name('admin.ingredients.store');
            Route::get('/{id}/edit',    'Admin\IngredientController@edit')  ->name('admin.ingredients.edit');
            Route::post('/update',      'Admin\IngredientController@update')->name('admin.ingredients.update');
            Route::get('/{id}/delete',  'Admin\IngredientController@delete')->name('admin.ingredients.delete');
        });


        Route::group(['prefix'  =>   'attributes'], function() {

            Route::get('/',             'Admin\AttributeController@index')  ->name('admin.attributes.index');
            Route::get('/create',       'Admin\AttributeController@create') ->name('admin.attributes.create');
            Route::post('/store',       'Admin\AttributeController@store')  ->name('admin.attributes.store');
            Route::get('/{id}/edit',    'Admin\AttributeController@edit')   ->name('admin.attributes.edit');
            Route::post('/update',      'Admin\AttributeController@update') ->name('admin.attributes.update');
            Route::get('/{id}/delete',  'Admin\AttributeController@delete')  ->name('admin.attributes.delete');

            Route::post('/get-values',      'Admin\AttributeValueController@getValues');
            Route::post('/add-values',      'Admin\AttributeValueController@addValues');
            Route::post('/update-values',   'Admin\AttributeValueController@updateValues');
            Route::post('/delete-values',   'Admin\AttributeValueController@deleteValues');

        });

        Route::group(['prefix'  =>   'brands'], function() {

            Route::get('/',             'Admin\BrandController@index')  ->name('admin.brands.index');
            Route::get('/create',       'Admin\BrandController@create') ->name('admin.brands.create');
            Route::post('/store',       'Admin\BrandController@store')  ->name('admin.brands.store');
            Route::get('/{id}/edit',    'Admin\BrandController@edit')   ->name('admin.brands.edit');
            Route::post('/update',      'Admin\BrandController@update') ->name('admin.brands.update');
            Route::get('/{id}/delete',  'Admin\BrandController@delete') ->name('admin.brands.delete');

        });

        Route::group(['prefix' => 'products'], function () {

            Route::get('/',             'Admin\ProductController@index')    ->name('admin.products.index');
            Route::get('/create',       'Admin\ProductController@create')   ->name('admin.products.create');
            Route::post('/store',       'Admin\ProductController@store')    ->name('admin.products.store');
            Route::get('/edit/{id}',    'Admin\ProductController@edit')     ->name('admin.products.edit');
            Route::post('/update',      'Admin\ProductController@update')   ->name('admin.products.update');
            Route::get('/{id}/delete',  'Admin\ProductController@delete')   ->name('admin.products.delete');


            Route::post('images/upload',        'Admin\ProductImageController@upload')->name('admin.products.images.upload');
            Route::get('images/{id}/delete',    'Admin\ProductImageController@delete')->name('admin.products.images.delete');

            Route::get('attributes/load',       'Admin\ProductAttributeController@loadAttributes');
            Route::post('attributes',           'Admin\ProductAttributeController@productAttributes');
            Route::post('attributes/values',    'Admin\ProductAttributeController@loadValues');
            Route::post('attributes/add',       'Admin\ProductAttributeController@addAttribute');
            Route::post('attributes/delete',    'Admin\ProductAttributeController@deleteAttribute');

            Route::get('photos/sync',    'Admin\ProductController@syncPhotos') ->name('admin.products.sync-photos');
            Route::get('prices/sync',    'Admin\ProductController@syncPrices') ->name('admin.products.sync-prices');
            Route::get('menu/sync',    'Admin\ProductController@syncProducts') ->name('admin.products.sync-menu');
            Route::get('ingredients/sync',    'Admin\ProductController@syncIngredients') ->name('admin.products.sync-ingredients');

        });
        Route::group(['prefix' => 'delivery'], function () {
            Route::get('/',             'Admin\DeliveryController@index')   ->name('admin.delivery.index');
            Route::get('/create',       'Admin\DeliveryController@create')  ->name('admin.delivery.create');
            Route::post('/store',       'Admin\DeliveryController@store')   ->name('admin.delivery.store');
            Route::get('/edit/{id}',    'Admin\DeliveryController@edit')    ->name('admin.delivery.edit');
            Route::post('/update',      'Admin\DeliveryController@update')  ->name('admin.delivery.update');
            Route::get('/{id}/delete',  'Admin\DeliveryController@delete')  ->name('admin.delivery.delete');
        });

        Route::group(['prefix' => 'payment'], function () {
            Route::get('/',             'Admin\PaymentController@index')    ->name('admin.payment.index');
            Route::get('/create',       'Admin\PaymentController@create')   ->name('admin.payment.create');
            Route::post('/store',       'Admin\PaymentController@store')    ->name('admin.payment.store');
            Route::get('/edit/{id}',    'Admin\PaymentController@edit')     ->name('admin.payment.edit');
            Route::post('/update',      'Admin\PaymentController@update')   ->name('admin.payment.update');
            Route::get('/{id}/delete',  'Admin\PaymentController@delete')   ->name('admin.payment.delete');
        });

        Route::group(['prefix' => 'payment-status'], function () {
            Route::get('/',             'Admin\PaymentStatusController@index')  ->name('admin.payment-status.index');
            Route::get('/create',       'Admin\PaymentStatusController@create') ->name('admin.payment-status.create');
            Route::post('/store',       'Admin\PaymentStatusController@store')  ->name('admin.payment-status.store');
            Route::get('/edit/{id}',    'Admin\PaymentStatusController@edit')   ->name('admin.payment-status.edit');
            Route::post('/update',      'Admin\PaymentStatusController@update') ->name('admin.payment-status.update');
            Route::get('/{id}/delete',  'Admin\PaymentStatusController@delete') ->name('admin.payment-status.delete');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/',             'Admin\OrderController@index')  ->name('admin.orders.index');
            Route::get('/create',       'Admin\OrderController@create') ->name('admin.orders.create');
            Route::post('/store',       'Admin\OrderController@store')  ->name('admin.orders.store');
            Route::get('/edit/{id}',    'Admin\OrderController@edit')   ->name('admin.orders.edit');
            Route::post('/update',      'Admin\OrderController@update') ->name('admin.orders.update');
            Route::get('/{id}/delete',  'Admin\OrderController@delete') ->name('admin.orders.delete');

            Route::post('products',     'Admin\OrderProductController@orderProducts');
        });

        Route::group(['prefix' => 'pages'], function () {
            Route::get('/',             'Admin\PageController@index')    ->name('admin.pages.index');
            Route::get('/create',       'Admin\PageController@create')   ->name('admin.pages.create');
            Route::post('/store',       'Admin\PageController@store')    ->name('admin.pages.store');
            Route::get('/edit/{id}',    'Admin\PageController@edit')     ->name('admin.pages.edit');
            Route::post('/update',      'Admin\PageController@update')   ->name('admin.pages.update');
            Route::get('/{id}/delete',  'Admin\PageController@delete')   ->name('admin.pages.delete');
        });

        Route::group(['prefix' => 'slider'], function () {
            Route::get('/',             'Admin\SliderController@index')    ->name('admin.slider.index');
            Route::get('/create',       'Admin\SliderController@create')   ->name('admin.slider.create');
            Route::post('/store',       'Admin\SliderController@store')    ->name('admin.slider.store');
            Route::get('/edit/{id}',    'Admin\SliderController@edit')     ->name('admin.slider.edit');
            Route::post('/update',      'Admin\SliderController@update')   ->name('admin.slider.update');
            Route::get('/{id}/delete',  'Admin\SliderController@delete')   ->name('admin.slider.delete');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/',             'Admin\UserController@index')   ->name('admin.users.index');
            Route::get('/create',       'Admin\UserController@create')  ->name('admin.users.create');
            Route::post('/store',       'Admin\UserController@store')   ->name('admin.users.store');
            Route::get('/edit/{id}',    'Admin\UserController@edit')    ->name('admin.users.edit');
            Route::post('/update',      'Admin\UserController@update')  ->name('admin.users.update');
            Route::get('/{id}/delete',  'Admin\UserController@delete')  ->name('admin.users.delete');
            Route::get('/export',       'Admin\UserController@export')  ->name('admin.users.export');
            Route::get('/export-poster', 'Admin\UserController@exportPosterUsers')  ->name('admin.users.export-poster');
            Route::post('/{id}/orders',       'Admin\UserController@orders')  ->name('admin.users.orders');
        });

        Route::group(['prefix' => 'ajax'], function () {
            Route::post('change-state', 'Admin\AjaxController@changeState')->name('admin.ajax.change-state');
        });

    });

});


