<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantSpecialization extends Model
{
    use HasFactory;

    protected $table = 'product_variant_specifications'; 

    protected $fillable = [
        'id',
        'product_id',
        'variant_id',
        'variant_value_id',
        'content_1',
        'content_2',
        'content_3',
        'content_4',
        'content_5',
        'content_6',
        'content_7',
        'content_8',
        'content_9',
        'content_10',
        'content_11',
        'content_12',
        'content_13',
        'content_14',
        'content_15',
        'content_16',
        'content_19',
        'created_at',
        'updated_at'
    ];
}
