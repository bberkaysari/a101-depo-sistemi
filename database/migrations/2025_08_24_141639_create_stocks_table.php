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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Ürün ID'si
            $table->unsignedBigInteger('location_id'); // Lokasyon ID'si
            $table->integer('quantity')->default(0); // Stok miktarı
            $table->integer('min_quantity')->default(0); // Minimum stok seviyesi
            $table->integer('max_quantity')->nullable(); // Maksimum stok seviyesi
            $table->text('notes')->nullable(); // Notlar
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->unique(['product_id', 'location_id']); // Bir ürün bir lokasyonda sadece bir kez bulunabilir
            $table->index(['location_id', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
