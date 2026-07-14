<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->string('buyer_name');
            $table->string('buyer_phone', 20);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('payment_status', ['menunggu_pembayaran', 'lunas', 'expired']);
            $table->enum('pickup_status', ['menunggu_approval', 'disetujui', 'selesai', 'ditolak'])->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->string('midtrans_transaction_id', 100)->nullable();
            $table->string('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
