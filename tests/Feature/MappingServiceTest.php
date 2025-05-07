<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Mapping;
use App\Services\MappingService;

class MappingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MappingService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = app(MappingService::class);
    }

    public function test_getOrCreate_creates_new_mapping_if_not_exists(): void
    {
        // No existing mapping
        $this->assertDatabaseCount('mappings', 0);

        // Call the service to create a new mapping
        $map = $this->svc->getOrCreate('shoes', 'google', '123');

        // One record should be created
        $this->assertDatabaseCount('mappings', 1);

        // Verify returned values
        $this->assertEquals('shoes', $map->keyword);
        $this->assertEquals('google', $map->src);
        $this->assertEquals('123', $map->creative);
        $this->assertEquals(1, $map->version);
        $this->assertNotNull($map->our_param);

        // Database should contain the new mapping
        $this->assertDatabaseHas('mappings', [
            'keyword'   => 'shoes',
            'src'       => 'google',
            'creative'  => '123',
            'version'   => 1,
            'our_param' => $map->our_param,
        ]);
    }

    public function test_getOrCreate_returns_existing_mapping(): void
    {
        // Create an existing mapping manually
        $existing = Mapping::factory()->create([
            'keyword'   => 'hat',
            'src'       => 'bing',
            'creative'  => '999',
            'version'   => 5,
        ]);

        // Call service with the same parameters
        $map = $this->svc->getOrCreate('hat', 'bing', '999');

        // No additional records should be created
        $this->assertDatabaseCount('mappings', 1);

        // Should return the existing mapping instance
        $this->assertTrue($map->is($existing));
    }

    public function test_refresh_creates_new_version(): void
    {
        // Create an original mapping with version 2
        $original = Mapping::factory()->create([
            'keyword'  => 'bag',
            'src'      => 'yahoo',
            'creative' => '777',
            'version'  => 2,
        ]);

        // Refresh to create a new version
        $new = $this->svc->refresh($original);

        // Two records should exist now
        $this->assertDatabaseCount('mappings', 2);

        // New mapping should have version incremented by 1
        $this->assertEquals(3, $new->version);
        $this->assertEquals('bag', $new->keyword);
        $this->assertEquals('yahoo', $new->src);
        $this->assertEquals('777', $new->creative);

        // The new param should be different from original
        $this->assertNotEquals($original->our_param, $new->our_param);

        // Database should contain the refreshed mapping record
        $this->assertDatabaseHas('mappings', [
            'id'        => $new->id,
            'version'   => 3,
            'our_param' => $new->our_param,
        ]);
    }
}
