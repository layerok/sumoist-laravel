<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\SliderContract;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreProductFormRequest;
use App\Models\Slider;
use Illuminate\Http\Request;


class SliderController extends BaseController
{
    protected $sliderRepository;

    public function __construct(
        SliderContract $sliderRepository
    )
    {
        $this->sliderRepository = $sliderRepository;
    }

    public function index()
    {
        $records = Slider::all();
        $this->setPageTitle('Слайдер', 'Список слайдов');
        return view('admin.slider.index', compact('records'));
    }

    public function create()
    {
        $this->setPageTitle('Продукты', 'Создать слайд');
        return view('admin.slider.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required|max:191',
            'image'     =>  'mimes:jpg,jpeg,png|max:1000'
        ]);

        $params = $request->except('_token');

        $product = $this->sliderRepository->createSlide($params);

        if (!$product) {
            return $this->responseRedirectBack('Возникла ошибка при создании слайда.', 'error', true, true);
        }
        return $this->responseRedirect('admin.slider.index', 'Слайд добавлен успешно' ,'success',false, false);
    }

    public function edit($id)
    {
        $record = $this->sliderRepository->findSlideById($id);



        $this->setPageTitle('Слайды', 'Редактировать слайд');
        return view('admin.slider.edit', compact( 'record'));
    }

    public function update(Request $request)
    {

        $request->validate( [
            'name'      =>  'required|max:191',
            'image'     =>  'mimes:jpg,jpeg,png|max:1000'
        ]);

        $params = $request->except('_token');

        $record = $this->sliderRepository->updateSlide($params);

        if (!$record) {
            return $this->responseRedirectBack('Возникла ошибка при обновлении слайда.', 'error', true, true);
        }
        return $this->responseRedirect('admin.slider.index', 'Слайд обновлен успешно' ,'success',false, false);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $record = $this->sliderRepository->deleteSlide($id);

        if (!$record) {
            return $this->responseRedirectBack('Возникла ошибка при удалении слайда.', 'error', true, true);
        }
        return $this->responseRedirect('admin.slider.index', 'Слайд удален успешно' ,'success',false, false);
    }
}
