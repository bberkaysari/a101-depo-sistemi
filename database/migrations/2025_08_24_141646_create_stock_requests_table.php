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
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Ürün ID'si
            $table->unsignedBigInteger('from_location_id'); // Gönderen lokasyon ID'si
            $table->unsignedBigInteger('to_location_id'); // Alıcı lokasyon ID'si
            $table->integer('requested_quantity'); // İstenen miktar
            $table->integer('approved_quantity')->nullable(); // Onaylanan miktar
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('request_notes')->nullable(); // İstek notları
            $table->text('response_notes')->nullable(); // Yanıt notları
            $table->unsignedBigInteger('requested_by'); // İsteği yapan kullanıcı
            $table->unsignedBigInteger('responded_by')->nullable(); // Yanıtlayan kullanıcı
            $table->timestamp('responded_at')->nullable(); // Yanıtlanma zamanı
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('from_location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('to_location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('responded_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'from_location_id', 'to_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
