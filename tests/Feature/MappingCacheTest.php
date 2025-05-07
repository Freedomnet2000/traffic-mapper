<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\MappingService;

class MappingCacheTest extends TestCase
{
    use RefreshDatabase;

    private MappingService $svc;
    private string $cacheKey;

    protected function setUp(): void
    {
        parent::setUp();

        // Use the array cache driver in tests to avoid involving the DB cache
        config(['cache.default' => 'array']);

        $this->svc      = app(MappingService::class);
        $this->cacheKey = 'mapping:shoes:google:123';
    }

    public function test_getOrCreate_caches_result(): void
    {
        // Ensure cache is clear
        Cache::forget($this->cacheKey);

        // Enable query logging to check if DB queries are executed
        DB::enableQueryLog();

        // 1. First call: cache miss => at least one DB query should occur
        $map1 = $this->svc->getOrCreate('shoes', 'google', '123');
        $queries1 = DB::getQueryLog();
        $this->assertNotEmpty($queries1, 'Expected DB queries on cache miss');
        $this->assertTrue(Cache::has($this->cacheKey), 'Cache should now contain the mapping');

        // 2. Reset the query log
        DB::flushQueryLog();

        // 3. Second call: cache hit => no new DB queries
        $map2 = $this->svc->getOrCreate('shoes', 'google', '123');
        $queries2 = DB::getQueryLog();
        $this->assertEmpty($queries2, 'Expected no DB queries on cache hit');

        // 4. Ensure the exact same object is returned
        $this->assertTrue($map2->is($map1));
    }

    public function test_refresh_invalidates_and_re_caches(): void
    {
        // 1. Create initial mapping and populate cache
        $orig = $this->svc->getOrCreate('bag', 'yahoo', '777');
        $oldParam = $orig->our_param;
        $key = 'mapping:bag:yahoo:777';

        $this->assertTrue(Cache::has($key), 'Cache should contain original mapping');

        // 2. Enable query logging
        DB::enableQueryLog();

        // 3. Call refresh => invalidates cache, creates a new record, and repopulates cache
        $new = $this->svc->refresh($orig);
        $queries = DB::getQueryLog();
        $this->assertNotEmpty($queries, 'Expected DB queries on refresh');
        $this->assertTrue(Cache::has($key), 'Cache should be repopulated after refresh');

        // 4. Ensure our_param has changed
        $this->assertNotEquals($oldParam, $new->our_param);
    }
}
