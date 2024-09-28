<?php

namespace App\Http\Controllers\Site;

use Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class CartController extends Controller
{
    public function getCart()
    {
        $response = [
            'products'        => Cart::getContent(),
            'totalQuantity' => Cart::getTotalQuantity(),
            'total'         => Cart::getTotal(),
        ];
        return json_encode($response,true);
    }
    public function manipulate(Request $request)
    {
        $input = $request->input();

        $options = $request->except('_token', 'action', 'uid');

        $active_mod = isset($input['active_modificator'])? (int)$input['active_modificator'] : null;
        if(isset($active_mod)){

            $uid = $input['uid'][$active_mod];
            $options['modificator_id'] = $input['modificator_id'][$active_mod];
            $options['attribute_value_id'] = $input['attribute_value_id'][$active_mod];

        }else{
            $uid = $input['uid'];

        }




        //Cart::clear();
        //return json_encode($input);

        $response = [
            'message' => 'Помилка!',
            'status'  => 'error'
        ];


            switch($request->input('action')){
                case 'add': {
                    $product = Product::with('images')->find($request->input('product_id'));
                    if($product->exists()){
                        $options = array_merge($options, ['uid' => $uid]);
                        if(isset($active_mod)){
                            $name = $product->name . ' ' . $input['modificator_value'][$active_mod] ;
                            $price = $input['modificator_price'][$active_mod];
                        }else{
                            $name = $product->name;
                            $price = $product->price;
                        }


                        Cart::add([
                            'id' =>$uid,
                            'name' => $name,
                            'price' => $price ,
                            'quantity' => 1,
                            'attributes' => $options,
                            'associatedModel' => $product,
                        ]);
                        $response['message'] = "Товар доданий!";
                        $response['status']  = "success";
                    }
                    break;
                }
                case 'decrease': {
                    if(Cart::get($uid)->quantity == 1){
                        $response['message'] = "Товар видален!";
                        Cart::remove($uid);
                    }else{
                        $response['message'] = "";
                        Cart::update($uid, [
                            'quantity' => -1
                        ]);
                    }


                    $response['status']  = "success";
                    break;
                }
                case 'increase': {
                    Cart::update($uid, [
                        'quantity' => 1
                    ]);
                    $response['message'] = "";
                    $response['status']  = "success";
                    break;
                }
                case 'remove': {
                    Cart::remove($uid);
                    $response['message'] = "Товар видален!";
                    $response['status']  = "success";
                    break;
                }
            }


            return json_encode($response);



    }

    public function removeItem($id)
    {
        Cart::remove($id);

        if (Cart::isEmpty()) {
            return redirect('/');
        }
        return redirect()->back()->with('message', 'Item removed from cart successfully.');
    }

    public function clearCart()
    {
        Cart::clear();

        return redirect('/');
    }


}
