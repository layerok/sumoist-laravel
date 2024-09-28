<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TypiCMS\NestableTrait;

class Menu extends Model
{
    use NestableTrait;
    protected $table = 'menu';

    protected $fillable = ['name', 'slug', 'description', 'image', 'content', 'parent_id', 'sort_order', 'hidden'];

    protected $casts = [
        'parent_id' =>  'integer',
        'hidden'      =>  'boolean'
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if(empty($this->attributes['slug'])){
            $this->attributes['slug'] = str_slug($value);
        }

    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }


}
