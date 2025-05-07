<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Mapping;

class RetrieveMappingValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_mapping_on_valid_our_param()
    {
        // Arrange: create a valid mapping record
        $mapping = Mapping::create([
            'keyword'   => 'shoes',
            'src'       => 'google',
            'creative'  => '123',
            'our_param' => 'AbC123',  // 6–12 alphanumeric characters
        ]);

        // Act
        $response = $this->getJson("/api/retrieve_original/{$mapping->our_param}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'keyword'  => $mapping->keyword,
                     'src'      => $mapping->src,
                     'creative' => $mapping->creative,
                 ]);
    }

    public function test_returns_422_when_format_is_invalid()
    {
        // Too short or contains invalid characters
        $response = $this->getJson('/api/retrieve_original/abc');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');
    }

    public function test_returns_422_when_our_param_not_found()
    {
        // Valid format but not found in the database
        $response = $this->getJson('/api/retrieve_original/XyZ789');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');
    }
}
