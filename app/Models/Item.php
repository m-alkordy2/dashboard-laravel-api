<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'image',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Assuming $this->image is in the format 'public/category_images/filename.png'
            $basePath = 'images';
            $imagePath = $this->image;

            return url("$basePath/$imagePath");
        }

        return null;
    }
}
