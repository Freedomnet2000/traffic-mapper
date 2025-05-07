<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Mapping;
use App\Services\MappingService;

class MappingRefreshTest extends TestCase
{
    use RefreshDatabase;

    /** @var MappingService */
    protected $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = app(MappingService::class);
    }

    public function test_refresh_retries_on_duplicate_and_increments_version(): void
    {
        // Create initial mapping at version 1
        $map = Mapping::factory()->create([
            'keyword'  => 'shoes',
            'src'      => 'google113',
            'creative' => '1234',
            'version'  => 1,
        ]);

        // First refresh => version 2
        $first = $this->svc->refresh($map);
        $this->assertEquals(2, $first->version);

        // Second refresh on the same original $map should increment version to 3
        $second = $this->svc->refresh($map);
        $this->assertEquals(3, $second->version);
    }
}
