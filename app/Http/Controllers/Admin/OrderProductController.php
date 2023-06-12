<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Order;


class OrderProductController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderProducts(Request $request)
    {
        $order = Order::with(['products.product', 'products.attributeValue'])->findOrFail($request->input('id'));

        return response()->json($order);
    }
}
