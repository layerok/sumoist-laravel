<?php
namespace App\Libraries;
//** Работает только на сервере https */

use App\Models\Order;

class Telegram {
    public $token;
    public $chat_id;
    public $headline;
    public $data;

    public $translates = [
        'first_name' => 'Имя',
        'phone' => 'Тел',
        'email' => 'Почта',
        'comment' => 'Комментарий',
        'address' => 'Адрес',
        'products' => 'Товары',
        'total'   => 'Итого',
        'delivery'   => 'Доставка',
        'payment'   => 'Оплата',
        'payment_status'   => 'Статус оплаты',
    ];
    public $emojis = [
//        'first_name' => "\xE2\x9C\x8F",
//        'last_name'  => "\xE2\x9C\x92",
//        'phone' => "\xF0\x9F\x93\xB1",
//        'email' => "\xF0\x9F\x93\xA7",
//        'comment' => "\xF0\x9F\x93\x9D",
//        'address' => "\xF0\x9F\x93\xA6",
//        'products' => "\xF0\x9F\x8D\xA3",
//        'total'   => "\xF0\x9F\x92\xB5",
//        'drink'   => "\xF0\x9F\x8D\xB9",
//        'pizza'  => "\xF0\x9F\x8D\x95",
//        'spaghetti'   => "\xF0\x9F\x8D\x9C",
//        'snacks' => "\xF0\x9F\x8D\xA4"
    ];


    public function __construct($token, $chat_id){
        $this->token = $token;
        $this->chat_id = $chat_id;
    }

    public function send($headline, $data){
        $this->headline = $headline;
        $this->data = $data;
        // send message
        $txt="\xF0\x9F\x93\x83 <b>$this->headline</b> %0A %0A";
        $txt .= $this->key_value_list($data);
        //if(Config::ENABLE_PRODUCTION){
            return $sendToTelegram = fopen("https://api.telegram.org/bot{$this->token}/sendMessage?chat_id={$this->chat_id}&parse_mode=html&text={$txt}","r");
        //}

    }

    public function sendOrder($id){
        if(isset($id)){
            $order = Order::find($id);

            if($order){
                $products= [];

                foreach($order->products as $value){

                    $product = [
                        'name'      => $value->product->name,
                        'count'     => $value->quantity
                    ];

                    $products[] = $product;
                }

                $order_params = [
                    'first_name'        => $order->first_name,
                    'phone'             => $order->phone,
                    'email'             => $order->email,
                    'comment'           => $order->comment,
                    'address'           => $order->address,
                    'delivery'          => $order->delivery->name,
                    'payment'           => $order->payment->name,
                    'products'          => $products,
                    'total'             => $order->sum . 'грн',
                    'payment_status'    => $order->payment_status->name,
                ];
                $this->send('Новый заказ', $order_params);
            }
            return false;
        }
        return false;
    }

    public function key_value_list($arr = []){
        $txt = '';
        foreach($arr as $key => $value){
            if(is_array($value)){
                if($key !== 'products'){
                    $txt .= $this->key_value_list($value);
                }else{
                    $txt .= $this->product_line($value);
                }
            }else{
                $emoji = $this->emojis[$key] ?? "";
                $translate = $this->translates[$key] ?? $key;

                $txt .= $emoji ." <b>".$translate."</b>: ".$value.".%0A";
            }

        }
        return $txt;
    }
    public function product_line($products){
        $txt = "%0A<b>Товары в заказе</b> %0A";
        $emoji = $this->emojis['products'] ?? '';
        foreach($products as $product){
            $txt .= $emoji." - ".urlencode($product['name']." x".$product['count']).".%0A";
        }
        $txt .= "%0A%0A";
        return $txt;
    }
}
?>
