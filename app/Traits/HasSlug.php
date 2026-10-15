<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->slug = self::generateUniqueSlug($model, $model->name);
        });

        static::updating(function ($model) {
            $model->slug = self::generateUniqueSlug($model, $model->name, $model->id);
        });
    }

    private static function generateUniqueSlug($model, $name, $id = null)
    {
        $slug = Str::slug($name, '-'); // Convert name to a slug
        $count = 0;

        while ($model::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $count++;
            $slug = Str::slug($name, '-') . '-' . $count; // Append a number if duplicate exists
        }

        return $slug;
    }
}