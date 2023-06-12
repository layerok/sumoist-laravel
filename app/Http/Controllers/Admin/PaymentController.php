<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Models\Payment;

class PaymentController extends BaseController
{
    public function index()
    {
        $records = Payment::all();

        $this->setPageTitle('Способы оплаты', 'Способы оплаты');
        return view('admin.payment.index', compact('records'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $this->setPageTitle('Способы оплаты', 'Создать способ оплаты');
        return view('admin.payment.create');
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

        $payment = Payment::create($merge->all());

        if (!$payment) {
            return $this->responseRedirectBack('Возникла ошибка создания способа оплаты.', 'error', true, true);
        }
        return $this->responseRedirect('admin.payment.index', 'Способ оплаты создан успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $targetRecord = Payment::findOrFail($id);

        //$categories = $this->categoryRepository->treeList();

        $this->setPageTitle('Способы оплаты', 'Редактировать способ оплаты : '.$targetRecord->name);
        return view('admin.payment.edit', compact('targetRecord'));
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

        $payment = Payment::findOrFail($params['id'])->update($merge->all());

        if (!$payment) {
            return $this->responseRedirectBack('Возникла ошибка обновления способа оплаты.', 'error', true, true);
        }
        return $this->responseRedirectBack('Способ оплаты обновлен успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $payment = Payment::findOrFail($id)->delete();

        if (!$payment) {
            return $this->responseRedirectBack('Возникла ошибка при удалении способа оплаты.', 'error', true, true);
        }
        return $this->responseRedirect('admin.payment.index', 'Способ оплаты удален успешно' ,'success',false, false);
    }
}
