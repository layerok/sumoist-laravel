<?php

namespace App\Repositories;

use App\Models\Slider;
use App\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use App\Contracts\SliderContract;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Support\Str;

/**
 * Class ProductRepository
 *
 * @package \App\Repositories
 */
class SliderRepository extends BaseRepository implements SliderContract
{
    use UploadAble;

    /**
     * ProductRepository constructor.
     * @param Slider $model
     */
    public function __construct(Slider $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @return mixed
     */
    public function listSlides(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
    {
        return $this->all($columns, $order, $sort)->with('categories');
    }

    /**
     * @param int $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findSlideById(int $id)
    {
        try {
            return $this->findOneOrFail($id);

        } catch (ModelNotFoundException $e) {

            throw new ModelNotFoundException($e);
        }

    }

    /**
     * @param array $params
     * @return Slider|mixed
     */
    public function createSlide(array $params)
    {
        try {

            if(!isset($params['slug'])){
                $params['slug'] = Str::slug($params['name']);
            }

            $collection = collect($params);

            $image = null;


            if ($collection->has('image') && ($params['image'] instanceof UploadedFile)) {
                $image = $this->uploadOne($params['image'], 'slides');

            }

            $hidden = $collection->has('hidden') ? 0 : 1;

            $merge = $collection->merge(compact('hidden', 'image'));

            $record = new Slider($merge->all());


            $record->save();


            return $record;

        } catch (QueryException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function updateSlide(array $params)
    {
        if(!isset($params['slug'])){
            $params['slug'] = Str::slug($params['name']);
        }

        $record = $this->findSlideById($params['id']);

        $collection = collect($params)->except('_token');

        $image = $record->image;



        if ($collection->has('image') && ($params['image'] instanceof  UploadedFile)) {

            if ($record->image != null) {
                $this->deleteOne($record->image);
            }

            $image = $this->uploadOne($params['image'], 'slides');

        }

        $hidden = $collection->has('hidden') ? 0 : 1;

        $merge = $collection->merge(compact('hidden', 'image'));

        $record->update($merge->all());



        return $record;
    }

    /**
     * @param $id
     * @return bool|mixed
     */
    public function deleteSlide($id)
    {

        $record = $this->findSlideById($id);
        if ($record->image != null) {
            $this->deleteOne($record->image);
        }

        $record->delete();

        return $record;
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findSlideBySlug($slug)
    {
        $product = Product::where('slug', $slug)->first();

        return $product;
    }
}
