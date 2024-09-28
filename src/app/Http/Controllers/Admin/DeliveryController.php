<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;

class DeliveryController extends BaseController
{

    public function index()
    {
        $records = Delivery::all();

        $this->setPageTitle('Способы доставки', 'Способы доставки');
        return view('admin.delivery.index', compact('records'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $this->setPageTitle('Способы доставки', 'Создать способ доставки');
        return view('admin.delivery.create');
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

        $delivery = Delivery::create($merge->all());

        if (!$delivery) {
            return $this->responseRedirectBack('Возникла ошибка создания способа доставки.', 'error', true, true);
        }
        return $this->responseRedirect('admin.delivery.index', 'Способ доставки создан успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $targetRecord = Delivery::findOrFail($id);


        $this->setPageTitle('Способы доставки', 'Редактировать способ доставки : '.$targetRecord->name);
        return view('admin.delivery.edit', compact('targetRecord'));
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

        $delivery = Delivery::where('id', '=', $params['id'])->update($merge->all());

        if (!$delivery) {
            return $this->responseRedirectBack('Возникла ошибка обновления способа доставки.', 'error', true, true);
        }
        return $this->responseRedirectBack('Способ доставки обновлен успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $delivery = Delivery::findOrFail($id)->delete();

        if (!$delivery) {
            return $this->responseRedirectBack('Возникла ошибка при удалении способа доставки.', 'error', true, true);
        }
        return $this->responseRedirect('admin.delivery.index', 'Способ доставки удален успешно' ,'success',false, false);
    }
}
