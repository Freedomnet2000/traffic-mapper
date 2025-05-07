<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Mapping;

class RetrieveOriginalTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_original_values_for_a_given_param()
    {
        $map = Mapping::create([
            'keyword'   => 'shoes',
            'src'       => 'google',
            'creative'  => '123',
            'our_param' => 'abc0000001',
        ]);

        // Act
        $response = $this->get("/api/retrieve_original/{$map->our_param}");

        // Assert
        $response->assertOk()
                 ->assertJson([
                     'keyword'  => 'shoes',
                     'src'      => 'google',
                     'creative' => '123',
                 ]);
    }

    public function test_it_returns_404_for_unknown_param()
    {
        // Arrange – false

        // Act
        $response = $this->get('/api/retrieve_original/doesNotExist123');

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');  
    }
}
