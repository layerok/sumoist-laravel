<?php
namespace App\Contracts;

/**
 * Interface ProductContract
 * @package App\Contracts
 */
interface SliderContract
{
    /**
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @return mixed
     */
    public function listSlides(string $order = 'id', string $sort = 'desc', array $columns = ['*']);



    /**
     * @param array $params
     * @return mixed
     */
    public function createSlide(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateSlide(array $params);

    /**
     * @param $id
     * @return bool
     */
    public function deleteSlide($id);

    /**
     * @param $slug
     * @return mixed
     */
    public function findSlideBySlug($slug);
}
