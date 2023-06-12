<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentStatus;

class PaymentStatusController extends BaseController
{
    public function index()
    {
        $records = PaymentStatus::all();

        $this->setPageTitle('Статусы оплаты', 'Статусы оплаты');
        return view('admin.payment-status.index', compact('records'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $this->setPageTitle('Способы доставки', 'Создать статус оплаты');
        return view('admin.payment-status.create');
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required|max:191',
        ]);

        $params = $request->except('_token');

        $collection = collect($params)->except('_token');
        $hidden = $collection->has('hidden') ? 0 : 1;
        $merge = $collection->merge(compact('hidden'));

        $payment_status = PaymentStatus::create($merge->all());

        if (!$payment_status) {
            return $this->responseRedirectBack('Возникла ошибка создания статуса оплаты.', 'error', true, true);
        }
        return $this->responseRedirect('admin.payment-status.index', 'Статус оплаты создан успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $targetRecord = PaymentStatus::findOrFail($id);


        $this->setPageTitle('Статус оплаты', 'Редактировать статус оплаты : '.$targetRecord->name);
        return view('admin.payment-status.edit', compact('targetRecord'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required|max:191'
        ]);

        $params = $request->except('_token');

        $collection = collect($params)->except('_token');
        $hidden = $collection->has('hidden') ? 0 : 1;
        $merge = $collection->merge(compact('hidden'));

        $payment_status = PaymentStatus::where('id', '=', $params['id'])->update($merge->all());

        if (!$payment_status) {
            return $this->responseRedirectBack('Возникла ошибка обновления статуса оплаты.', 'error', true, true);
        }
        return $this->responseRedirectBack('Статус оплаты обновлен успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $payment_status = PaymentStatus::findOrFail($id)->delete();

        if (!$payment_status) {
            return $this->responseRedirectBack('Возникла ошибка при удалении статуса оплаты.', 'error', true, true);
        }
        return $this->responseRedirect('admin.payment-status.index', 'Статус оплаты удален успешно' ,'success',false, false);
    }
}
