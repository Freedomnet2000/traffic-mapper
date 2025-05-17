<?php

// tests/Feature/RetrieveApiTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Mapping;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RetrieveApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_handles_retrieve_and_logs()
    {
        Mapping::create([
            'keyword' => 'testkey',
            'src' => 'bing',
            'creative' => 'txtad',
            'our_param' => 'ret789',
            'version' => 1,
        ]);

        $response = $this->getJson('/api/retrieve_original/ret789');

        $response->assertStatus(200);
        $response->assertJsonStructure(['keyword', 'src', 'creative']);

        $this->assertDatabaseHas('request_logs', [
            'endpoint' => '/api/retrieve_original/ret789',
            'action'   => 'retrieve',
            'method'   => 'GET',
            'success'  => true,
        ]);
    }
}
