<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mapping extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'keyword',
        'src',
        'creative',
        'our_param',
        'version',
        'refreshed_at',
    ];

    /**
     * Boot the model and attach creating event listener.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Mapping $model) {
            // Generate random our_param only if not provided
            if (empty($model->our_param)) {
                do {
                    // 10-character random string (alphanumeric)
                    $param = Str::random(10);
                } while (self::where('our_param', $param)->exists());

                $model->our_param = $param;
            }
        });
    }

    /**
     * Use our_param for route model binding instead of ID.
     */
    public function getRouteKeyName(): string
    {
        return 'our_param';
    }
}
