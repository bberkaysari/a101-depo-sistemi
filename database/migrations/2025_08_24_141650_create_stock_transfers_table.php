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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_request_id'); // Stok isteği ID'si
            $table->unsignedBigInteger('product_id'); // Ürün ID'si
            $table->unsignedBigInteger('from_location_id'); // Gönderen lokasyon ID'si
            $table->unsignedBigInteger('to_location_id'); // Alıcı lokasyon ID'si
            $table->integer('transferred_quantity'); // Transfer edilen miktar
            $table->enum('status', ['pending', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->text('transfer_notes')->nullable(); // Transfer notları
            $table->unsignedBigInteger('transferred_by'); // Transferi yapan kullanıcı
            $table->timestamp('transferred_at')->nullable(); // Transfer zamanı
            $table->timestamp('received_at')->nullable(); // Alınma zamanı
            $table->timestamps();
            
            $table->foreign('stock_request_id')->references('id')->on('stock_requests')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('from_location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('to_location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('transferred_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'from_location_id', 'to_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
