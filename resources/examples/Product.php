<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Product extends Model
{
    use Translatable;

    protected $table = 'products';
    protected $primaryKey = 'id';

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = ['name', 'description', 'slug'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the related Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
