<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasTable('totp_authentications')) {
            Schema::create('totp_authentications', function (Blueprint $table) {
                $table->id();
                $table->morphs('authenticatable', '2fa_auth_type_auth_id_index');
                $table->text('shared_secret');
                $table->timestampTz('enabled_at')->nullable();
                $table->unsignedTinyInteger('digits')->default(6);
                $table->unsignedTinyInteger('seconds')->default(30);
                $table->string('algorithm', 16)->default('sha1');
                $table->text('recovery_codes')->nullable();
                $table->timestampTz('recovery_codes_generated_at')->nullable();
                $table->timestampsTz();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('totp_authentications');
    }
};