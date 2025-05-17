<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\LogApiRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogApiRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_api_request_to_database()
    {
        $this->assertDatabaseCount('request_logs', 0);

        // Run job
        (new LogApiRequest(
            endpoint: '/api/refresh',
            action: 'refresh',
            method: 'POST',
            ip: '127.0.0.1',
            params: ['our_param' => 'abc123', 'note' => 'test'],
            status: 200,
            success: true,
            user_id: 42
        ))->handle();

        $this->assertDatabaseCount('request_logs', 1);
        $this->assertDatabaseHas('request_logs', [
            'endpoint' => '/api/refresh',
            'action' => 'refresh',
            'method' => 'POST',
            'ip' => '127.0.0.1',
            'status' => 200,
            'success' => true,
            'user_id' => 42,
        ]);
    }


    public function test_throws_if_action_is_empty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Action is required');

        (new LogApiRequest(
            endpoint: '/api/refresh',
            action: '', // שווה ל-"missing"
            method: 'POST',
            ip: '127.0.0.1',
            params: [],
            status: 200
        ))->handle();
    }
}
