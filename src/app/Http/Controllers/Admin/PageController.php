<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Str;


class PageController extends BaseController
{
    public function index()
    {
        $records = Page::all();

        $this->setPageTitle('Информационная страница', 'Информационная страница');
        return view('admin.pages.index', compact('records'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setPageTitle('Информационные страницы', 'Создать инф. страницу');
        return view('admin.pages.create');
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


        if(!isset($params['slug'])){
            $params['slug'] = Str::slug($params['name']);
        }


        $collection = collect($params)->except('_token');



        $hidden = $collection->has('hidden') ? 0 : 1;

        $merge = $collection->merge(compact('hidden'));

        $page = Page::create($merge->all());

        if (!$page) {
            return $this->responseRedirectBack('Возникла ошибка создания инф. страница.', 'error', true, true);
        }
        return $this->responseRedirect('admin.pages.index', 'Способ оплаты создан успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $targetRecord = Page::findOrFail($id);

        //$categories = $this->categoryRepository->treeList();

        $this->setPageTitle('Инф. страницы', 'Редактировать инф. страницы : '.$targetRecord->name);
        return view('admin.pages.edit', compact('targetRecord'));
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

        if(!isset($params['slug'])){
            $params['slug'] = Str::slug($params['name']);
        }


        $collection = collect($params)->except('_token');


        $hidden = $collection->has('hidden') ? 0 : 1;
        $merge = $collection->merge(compact('hidden'));

        $page = Page::findOrFail($params['id'])->update($merge->all());

        if (!$page) {
            return $this->responseRedirectBack('Возникла ошибка обновления инф. страницы', 'error', true, true);
        }
        return $this->responseRedirectBack('Инф. страница обновлена успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $page = Page::findOrFail($id)->delete();

        if (!$page) {
            return $this->responseRedirectBack('Возникла ошибка при удалении инф. страницы.', 'error', true, true);
        }
        return $this->responseRedirect('admin.pages.index', 'Информационная страница удалена успешно' ,'success',false, false);
    }
}
