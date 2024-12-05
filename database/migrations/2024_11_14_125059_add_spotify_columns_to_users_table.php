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
        Schema::table('users', function (Blueprint $table) {
            $table->string('spotify_id')->unique()->nullable();
            $table->string('spotify_token')->nullable();
            $table->string('spotify_refresh_token')->nullable();
            $table->integer('spotify_token_expires_in')->nullable();
            $table->string('spotify_avatar_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['spotify_id', 'spotify_token', 'spotify_refresh_token', 'spotify_token_expires_in']);
        });
    }

};
