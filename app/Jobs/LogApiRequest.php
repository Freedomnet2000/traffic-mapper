<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogApiRequest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $endpoint,
        public string $action,
        public string $method,
        public ?string $ip,
        public array $params,
        public int $status,
        public bool $success = true,
        public ?int $user_id = null
    ) {}


    public function handle(): void
    {
        DB::table('request_logs')->insert([
            'endpoint' => $this->endpoint,
            'action' => $this->action,
            'method' => $this->method,
            'ip' => $this->ip,
            'params' => json_encode($this->params),
            'status' => $this->status,
            'success' => $this->success,
            'user_id' => $this->user_id,
            'created_at' => now(),
        ]);

    }
}
