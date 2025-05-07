<?php

namespace Database\Factories;

use App\Models\Mapping;
use Illuminate\Database\Eloquent\Factories\Factory;

class MappingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Mapping::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'keyword'     => $this->faker->word(),
            'src'         => $this->faker->word(),
            'creative'    => $this->faker->word(),
            'our_param'   => null,
            'version'     => 1,
            'refreshed_at'=> null,
        ];
    }
}
