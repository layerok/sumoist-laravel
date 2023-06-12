<?php

/**
 *
 * Это пока попытка написать более удобный api для постера
 * Пока я узнал как реализовать цепочку ф-ций типо такого, но пока нет сил
 *
 * $poster = new Poster('{$s_token}')
 * $poster
 *      ->setUrl('incomingOrders.createIncomingOrder')
 *      ->setType('post')
 *      ->setProducts($arr_products)
 *      ->setName('Vova')
 *      ->setAddress('Шишкина 2')
 *      ->setPhone('+380669111095')
 *      ->setComment('Пароль на парадной 832')
 *      ->setEmail('kotopes231@gmail.com')
 *      ->sendToTelegram(true)
 *      ->sendOrder();
 *
 * либо
 *
 * $poster
 *      ->setOrderId(4)
 *      ->sendToTelegram()
 *      ->sendOrder()
 */

namespace App\Libraries;

use App\Models\Delivery;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class Poster {

    private $token;

    private $type;

    private $url;

    private $params;

    private $isJson;

    public function __construct($token){
        $this->token = $token;
        $this->type = 'get';
        $this->isJson = false;
    }

    public function sendRequest($url, $type = 'get', $params = [], $json = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($type == 'post' || $type == 'put') {
            curl_setopt($ch, CURLOPT_POST, true);

            if ($json) {
                $params = json_encode($params);

                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($params)
                ]);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Poster (http://joinposter.com)');

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function query($name){
        $url = "https://joinposter.com/api/{$name}"
            . '?token='.$this->token;

        $response = $this->sendRequest($url);

        return $response;
    }

    public function sendOrder($data) {
        $url = 'https://joinposter.com/api/incomingOrders.createIncomingOrder'
            . '?token='.$this->token;

        $response = $this->sendRequest($url, 'post', $data);

        return $response;
    }

    public function sendOrderById($id){

        if(isset($id)){
            $order = Order::with(['products.product', 'products.attributeValue', 'delivery', 'payment'])->find($id);

            if($order){

                $products = [];

                foreach($order->products as $value){


                    $product = [
                        'product_id' => $value->product->poster_id,
                        'count'      => $value->quantity
                    ];

                    $modificator = [];
                    if(isset($value->attribute_value_id)){
                        $modificator = [
                            'modificator_id' => $value->attributeValue->poster_id
                        ];
                    }

                    $products[] = array_merge($product, $modificator);
                }

                $payment = [];

                if($order->payment_status_id == 3){// id успешного статуса оплаты
                    $payment = [
                        'payment'=> [
                            'type' => 1,
                            'sum' => $order->sum,
                            'currency' => 'UAH'
                        ]
                    ];
                }

                $comment = "|| Способ оплаты: " . $order->payment->name
                    ." || Способ доставки: " . $order->delivery->name;

                $order_params = [
                    'spot_id' => 1,
                    'first_name' => $order->first_name,
                    'phone'      => $order->phone,
                    'email'      => $order->email,
                    'comment'    => $order->comment . $comment,
                    'address'    => $order->address,
                    'products'   => $products
                ];

                $order_params = array_merge($order_params, $payment);


                if(env('APP_ENV') == 'production'){
                    $bot = new Telegram(env('TELEGRAM_BOT_ID'), env('TELEGRAM_CHAT_ID'));
                    $bot->sendOrder($id);
                }




                $response = $this->sendOrder($order_params);

                $decoded_response = json_decode($response, true);



                if(!isset($decoded_response['error'])){

                    $order->update(['is_sent_to_poster' => 1]);
                    $order->phone = preg_replace('/[^\d+]*/', '', $order->phone);

                    $user = User::where("phone", "=", $order->phone)->first();

                    if(!$user){
                        $user = User::create([
                            'name' => $order->first_name,
                            'email' => $order->email,
                            'phone' => $order->phone,
                            'address' => $order->address,
                            'password' => bcrypt('lol_password')
                        ]);
                    }else{
                        if(!empty($order->email)){
                            $user->email = $order->email;
                        }
                        if(!empty($order->first_name)){
                            $user->name = $order->first_name;
                        }
                        if(!empty($order->address)){
                            $user->address = $order->address;
                        }

                        $user->save();
                    }


                    $order->update(['user_id' => $user->id]);
                    Log::channel('single')->debug('Заказа №'. $order->id . " отправлен на постер");


                } else {
                    Log::error($response);
                }

                return $response;

            }
            return false;
        }
        return false;
    }

    public function setType($type){
        $this->type = $type;
        return $this;
    }
    public function getType(){
        return $this->type;
    }

    public function setParams($params){
        $this->params = $params;
        return $this;
    }
    public function getParams(){
        return $this->params;
    }

    public function setUrl($url){
        $this->url = $url;
        return $this;
    }
    public function getUrl(){
        return $this->url;
    }

    public function setIsJson($isJson){
        $this->isJson = $isJson;
        return $this;
    }
    public function getIsJson(){
        return $this->isJson;
    }
}
