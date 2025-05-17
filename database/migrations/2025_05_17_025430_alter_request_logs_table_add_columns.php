<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->string('action')->after('endpoint');
            $table->boolean('success')->default(true)->after('status');
            $table->unsignedBigInteger('user_id')->nullable()->after('ip');
        });
    }

    public function down(): void {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->dropColumn(['action', 'success', 'user_id']);
        });
    }
};
