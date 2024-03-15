<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_name', 'description', 'section_id'];

    public function section()
    {
        return $this->belongsTo('App\Section');
    }
}
