<?php
// tests/Feature/LogHelperTest.php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogHelperTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_full_request_correctly()
    {
        $request = Request::create('/api/refresh', 'POST', [
            'our_param' => 'xyz123',
        ]);

        LogHelper::fullLog('/api/refresh', 'refresh', $request, 200, true, [
            'note' => 'refresh test',
            'new_param' => 'abc987',
        ]);

        $this->assertDatabaseHas('request_logs', [
            'endpoint' => '/api/refresh',
            'action'   => 'refresh',
            'method'   => 'POST',
            'status'   => 200,
            'success'  => true,
        ]);

        $row = DB::table('request_logs')->first();
        $params = json_decode($row->params, true);

        $this->assertEquals('xyz123', $params['our_param']);
        $this->assertEquals('abc987', $params['new_param']);
        $this->assertEquals('refresh test', $params['note']);
    }
}
