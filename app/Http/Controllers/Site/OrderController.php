<?php

namespace App\Http\Controllers\Site;

use App\Libraries\Poster;
use App\Libraries\Telegram;
use App\Models\OrderProduct;
use App\Models\PaymentStatus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Payment;
use WayForPay;
use Crypt;
use Cart;
use \WayForPay\SDK\Domain\Product;
use \Maksa988\WayForPay\Collection\ProductCollection;
use \Maksa988\WayForPay\Domain\Client;
use \WayForPay\SDK\Domain\Transaction;
use Illuminate\Support\Facades\Log;



class OrderController extends Controller
{

    public function index(){


        $cart_items = Cart::getContent();
        $delivery   = DB::table('delivery')->where("hidden", "=", "0")->get();
        $payment    = DB::table('payment')->where("hidden", "=", "0")->get();

        return view('theme::site.pages.checkout', compact('cart_items', 'delivery', 'payment'));


    }

    public function handle(Request $request){
        Log::channel('single')->debug('wayforpay прислал ответ');
        $data = json_decode(file_get_contents('php://input'), TRUE);
//        foreach($data as $value){
//            Log::channel('single')->debug($value);
//        }
        return WayForPay::handleServiceUrl($data, function (WayForPay\SDK\Domain\TransactionService $transaction, $success) {
            Log::channel('single')->debug('Статус заказа №'. $transaction->getOrderReference() . ": " . $transaction->getStatus());
            $order = Order::find($transaction->getOrderReference());
            if($order){
                $payment_status = PaymentStatus::where('key', '=', "{$transaction->getStatus()}")->first();
                $order->update([
                    'payment_status_id' => $payment_status->id
                ]);
                if(!Order::find($order->id)->is_sent_to_poster){
                    $poster = new Poster(config('poster.access_token'));
                    $poster->sendOrderById($order->id);
                }

            }

            if($transaction->getReason()->isOK()) {

                return $success();
            }
            return "Error: ". $transaction->getReason()->getMessage();
        });

    }

    public function send(Request $request){

        if(Cart::isEmpty()){
            $response = [
                "cartIsEmpty" => true
            ];


            return json_encode($response);
        }


        $spot = DB::table('spots')->find(1);
        $data = $request->all();


        if($spot){

            $data['deliveryMethod'] = Delivery::find($data['delivery_id'])->name;
            $data['paymentMethod'] =  Payment::find($data['payment_id'])->name;

            $comment = '';
            if(!empty($data['comment'])){
                $comment .=  $data['comment'];
            }

            if(!empty($data['change'])){
                $comment .= " || Приготовить сдачу с: ".$data['change'];
            }
            if(!empty($data['sticks'])){
                $comment .= " || Кол-во палочек: ".$data['sticks'];
            }

            $order = Order::create([
                'email' => $data['email'],
                'first_name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'comment' => $comment,
                'payment_id' => $data['payment_id'],
                'delivery_id' => $data['delivery_id'],
                'sum'         => Cart::getTotal()
            ]);


            $way_products = [];

            foreach(Cart::getContent() as $value){

                OrderProduct::create([
                    'order_id'              => $order->id,
                    'product_id'            => $value->associatedModel->id,
                    'attribute_value_id'    => isset($value->attributes['active_modificator']) ? $value->attributes->attribute_value_id : null,
                    'quantity'              => $value->quantity,
                    'price'                 => $value->price,
                    'sum'                   => $value->price * $value->quantity
                ]);

                $way_products[] = new Product($value->name, $value->price, $value->quantity);

            }

            $way_products = new ProductCollection($way_products);
            $client = new Client($data['name'], null, $data['email'], $data['phone']);

            $form = WayForPay::purchase(
                $order->id,
                $order->sum,
                $client,
                $way_products,
                'UAH', null, 'RU', null,
                'https://sumoist.com.ua/thankyou',
                'https://sumoist.com.ua/order/handle'
            )->getAsString($submitText = 'Pay', $buttonClass = 'btn btn-primary'); // Get html form as string


            if($data["payment_id"] == 3){
                $response = [
                    "payment_id" => 3,
                    "form" => $form
                ];

                Cart::clear();

                return json_encode($response);
            }

            if(true){

                $poster = new Poster(config('poster.access_token'));
                $response = json_decode($poster->sendOrderById($order->id), true);

            }else{
                $response = ['response' => 'success'];
            }

            return json_encode($response);

        }else{
            // spot not found
        }
    }
}
