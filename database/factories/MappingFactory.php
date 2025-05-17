<?php

namespace Database\Factories;

use App\Models\Mapping;
use Illuminate\Database\Eloquent\Factories\Factory;

class MappingFactory extends Factory
{
    protected $model = Mapping::class;

    public function definition(): array
    {
        return [
            'url' => 'https://example.com/path',
            'action' => 'retrieve_original',
            'status_code' => 200,
            'response_time_ms' => 123,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}