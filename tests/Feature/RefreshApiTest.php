<?php
// tests/Feature/RefreshApiTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Mapping;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RefreshApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_handles_refresh_and_logs()
    {
        // צור mapping קיים
        $original = Mapping::create([
            'keyword' => 'testkey',
            'src' => 'google',
            'creative' => 'banner1',
            'our_param' => 'abc123',
            'version' => 1,
        ]);

        $response = $this->postJson('/api/refresh', [
            'our_param' => 'abc123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['new_param']);

        $this->assertDatabaseHas('request_logs', [
            'endpoint' => '/api/refresh',
            'action'   => 'refresh',
            'method'   => 'POST',
            'status'   => 200,
            'success'  => true,
        ]);
    }
}
