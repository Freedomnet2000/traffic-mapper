<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;   // Run migrations in memory (SQLite) before each test

    protected function setUp(): void
    {
        parent::setUp();

        // Define a fake affiliate URL for the local environment
        config(['app.affiliate_url' => 'https://affiliate.test']);
    }

    public function test_it_redirects_and_stores_mapping()
    {
        // 1. Send the request
        $response = $this->get('/redirect?keyword=shoes&src=google&creative=123');

        // 2. Assert status code 302
        $response->assertStatus(302);

        // 3. Assert that the Location header starts with the affiliate URL
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://affiliate.test?our_param=', $location);

        // 4. Decode our_param from the URL
        $ourParam = substr($location, strrpos($location, '=') + 1);

        // 5. Verify that the record exists in the database
        $this->assertDatabaseHas('mappings', [
            'keyword'   => 'shoes',
            'src'       => 'google',
            'creative'  => '123',
            'our_param' => $ourParam,
            'version'   => 1,
        ]);
    }
}
