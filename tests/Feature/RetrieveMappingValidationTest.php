<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Mapping;

class RetrieveMappingValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_endpoint_validation_failure()
    {
        // Missing parameters
        $response = $this->getJson('/redirect');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['keyword', 'src', 'creative']);

        // Invalid characters / too short
        $response = $this->getJson('/redirect?keyword=a&src=*&creative=-');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['keyword', 'src', 'creative']);
    }

    public function test_redirect_endpoint_success()
    {
        $params = ['keyword' => 'ab', 'src' => 'cd', 'creative' => 'ef'];
        $response = $this->get('/redirect?' . http_build_query($params));
        $response->assertStatus(302)
                 ->assertHeader('Location');
    }

    public function test_retrieve_original_endpoint_validation_and_not_found()
    {
        // Invalid format
        $response = $this->getJson('/api/retrieve_original/!@#');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');

        // Valid format but not exists
        $response = $this->getJson('/api/retrieve_original/AbCd12');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');
    }

    public function test_retrieve_original_endpoint_success()
    {
        // Seed a mapping
        $mapping = Mapping::create([
            'keyword'   => 'shoes',
            'src'       => 'google',
            'creative'  => '12345',
            'our_param' => 'AbCd12',
        ]);

        $response = $this->getJson("/api/retrieve_original/{$mapping->our_param}");
        $response->assertStatus(200)
                 ->assertJson([
                     'keyword'  => $mapping->keyword,
                     'src'      => $mapping->src,
                     'creative' => $mapping->creative,
                 ]);
    }

    public function test_refresh_endpoint_validation_and_not_found()
    {
        // Missing our_param
        $response = $this->postJson('/api/refresh', []);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');

        // Invalid format
        $response = $this->postJson('/api/refresh', ['our_param' => '!@#']);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');

        // Valid format but not exists
        $response = $this->postJson('/api/refresh', ['our_param' => 'NoParam1']);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('our_param');
    }

    public function test_refresh_endpoint_success()
    {
        $mapping = Mapping::create([
            'keyword'   => 'shoes',
            'src'       => 'google',
            'creative'  => '12345',
            'our_param' => 'XyZ789',
        ]);

        $response = $this->postJson('/api/refresh', ['our_param' => $mapping->our_param]);
        $response->assertStatus(200)
                 ->assertJsonStructure(['new_param']);
    }
}
