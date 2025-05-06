<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mappings', function (Blueprint $table) {
            $table->id();                      // BIGINT PK
            $table->string('keyword',255);
            $table->string('src',100);
            $table->string('creative',100);
            $table->string('our_param',10)->unique();
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('refreshed_at')->nullable();
            $table->timestamps();
            $table->unique(['keyword','src','creative','version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mappings');
    }
};
