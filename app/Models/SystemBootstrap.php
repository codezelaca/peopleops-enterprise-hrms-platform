<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id', 'registered_by_email', 'completed_at'])]
class SystemBootstrap extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'completed_at' => 'immutable_datetime',
        ];
    }
}
