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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ürün adı
            $table->text('description')->nullable(); // Ürün açıklaması
            $table->string('sku')->unique(); // Stok Kodu (Stock Keeping Unit)
            $table->string('barcode')->nullable(); // Barkod
            $table->unsignedBigInteger('category_id'); // Kategori ID'si
            $table->decimal('unit_price', 10, 2)->default(0); // Birim fiyat
            $table->string('unit')->default('piece'); // Birim (piece, kg, liter, etc.)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['name', 'sku', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
