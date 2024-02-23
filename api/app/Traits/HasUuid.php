<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->id = (string) Str::uuid();
    });
  }
}