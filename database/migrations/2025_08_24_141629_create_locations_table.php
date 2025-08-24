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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Lokasyon adı (örn: Kayseri Ana Depo, Melikgazi Şubesi)
            $table->string('type'); // Lokasyon tipi: 'warehouse', 'store', 'branch'
            $table->text('address')->nullable(); // Adres bilgisi
            $table->unsignedBigInteger('parent_id')->nullable(); // Üst lokasyon ID'si
            $table->integer('level')->default(0); // Hiyerarşi seviyesi: 0=Ana Depo, 1=Şehir, 2=İlçe, 3=Mahalle
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('locations')->onDelete('cascade');
            $table->index(['type', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
