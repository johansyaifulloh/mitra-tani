<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midtrans_settings', function (Blueprint $table) {
            $table->id();
            $table->string('server_key');
            $table->string('client_key');
            $table->enum('mode', ['sandbox', 'production'])->default('sandbox');
            $table->string('notification_url');
            $table->string('finish_url');
            $table->string('unfinish_url');
            $table->string('error_url');
            $table->unsignedInteger('expiry_duration')->default(1440);
            $table->boolean('is_active')->default(true);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_settings');
    }
};
