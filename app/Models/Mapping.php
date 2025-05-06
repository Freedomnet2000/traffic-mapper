<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    protected $fillable = [
        'keyword',
        'src',
        'creative',
        'our_param',
        'version',
        'refreshed_at',
    ];
}