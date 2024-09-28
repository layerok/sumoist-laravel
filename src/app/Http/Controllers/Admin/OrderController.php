<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class OrderController extends BaseController
{
    public function index()
    {


        $records = Order::orderBy('updated_at', 'desc')->get();

        /*foreach($records as $order){

            $clearPhone = preg_replace('/[^\d+]/', '', $order->phone);

            $user = User::where('phone', '=', $clearPhone);

            if(!$user->exists()){
                $user->create([
                    'name' => $order->first_name,
                    'phone' => $clearPhone,
                    'email' => $order->email,
                    'address' => $order->address,
                    'password' => bcrypt('lolpassword')
                ]);
            }


            $order->update([
                'user_id' => $user->first()->id
            ]);



        }*/

        $this->setPageTitle('Заказы', 'Заказы');
        return view('admin.orders.index', compact('records'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $delivery = Delivery::all();
        $payment = Payment::all();
        $payment_statuses = PaymentStatus::all();

        $this->setPageTitle('Заказы', 'Создать заказ');
        return view('admin.orders.create', compact('delivery', 'payment', 'payment_statuses'));
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone'      =>  'required',
        ]);

        $params = $request->except('_token');

        $collection = collect($params)->except('_token');


        $order = Order::create($collection->all());

        if (!$order) {
            return $this->responseRedirectBack('Возникла ошибка создания заказа.', 'error', true, true);
        }
        return $this->responseRedirect('admin.orders.index', 'Заказ создан успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $targetRecord = Order::findOrFail($id);

        $delivery = Delivery::all();
        $payment = Payment::all();
        $payment_statuses = PaymentStatus::all();


        $this->setPageTitle('Заказы', 'Редактировать заказ : '.$targetRecord->name);
        return view('admin.orders.edit', compact('targetRecord', 'delivery', 'payment', 'payment_statuses'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {

        $this->validate($request, [
            'phone'     =>  'required'
        ]);

        $params = $request->except('_token');
        $collection = collect($params)->except('_token');


        $order = Order::find($params['id'])->update($collection->all());

        if (!$order) {
            return $this->responseRedirectBack('Возникла ошибка обновления заказа.', 'error', true, true);
        }
        return $this->responseRedirectBack('Заказ обновлен успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $order = Order::findOrFail($id)->delete();

        if (!$order) {
            return $this->responseRedirectBack('Возникла ошибка при удалении заказа.', 'error', true, true);
        }
        return $this->responseRedirect('admin.orders.index', 'Заказ удален успешно' ,'success',false, false);
    }
}
