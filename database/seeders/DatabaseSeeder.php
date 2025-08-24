<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo Users - Her mağaza için ayrı kullanıcı
        $kayseriUser = User::create([
            'name' => 'Kayseri Ana Mağaza',
            'email' => 'kayseri@a101.com',
            'password' => Hash::make('123456'),
            'location_id' => null, // Önce location_id'yi null olarak bırak
        ]);

        $hurriyetUser = User::create([
            'name' => 'Hürriyet Mahallesi Şubesi',
            'email' => 'hurriyet@a101.com',
            'password' => Hash::make('123456'),
            'location_id' => null, // Önce location_id'yi null olarak bırak
        ]);

        // Lokasyonlar oluştur (Hiyerarşik yapı)
        $kayseriDepo = Location::create([
            'name' => 'Kayseri Ana Depo',
            'type' => 'warehouse',
            'address' => 'Kayseri Organize Sanayi Bölgesi',
            'parent_id' => null,
            'level' => 0,
            'is_active' => true,
        ]);

        $kayseriMagaza = Location::create([
            'name' => 'Kayseri Ana Mağaza',
            'type' => 'store',
            'address' => 'Kayseri Merkez, Talas Caddesi',
            'parent_id' => $kayseriDepo->id,
            'level' => 1,
            'is_active' => true,
        ]);

        $melikgaziSube = Location::create([
            'name' => 'Melikgazi Şubesi',
            'type' => 'branch',
            'address' => 'Melikgazi İlçesi, Cumhuriyet Meydanı',
            'parent_id' => $kayseriMagaza->id,
            'level' => 2,
            'is_active' => true,
        ]);

        $hurriyetMahalle = Location::create([
            'name' => 'Hürriyet Mahallesi Şubesi',
            'type' => 'branch',
            'address' => 'Hürriyet Mahallesi, Atatürk Caddesi',
            'parent_id' => $melikgaziSube->id,
            'level' => 3,
            'is_active' => true,
        ]);

        // Kategoriler oluştur (Hiyerarşik yapı)
        $gida = Category::create([
            'name' => 'Gıda',
            'description' => 'Gıda ürünleri',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $sutUrunleri = Category::create([
            'name' => 'Süt Ürünleri',
            'description' => 'Süt ve süt ürünleri',
            'parent_id' => $gida->id,
            'is_active' => true,
        ]);

        $bebekBezi = Category::create([
            'name' => 'Bebek Bezi',
            'description' => 'Bebek bezi ve bakım ürünleri',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $temizlik = Category::create([
            'name' => 'Temizlik',
            'description' => 'Temizlik ve hijyen ürünleri',
            'parent_id' => null,
            'is_active' => true,
        ]);

        // Ürünler oluştur
        $sut = Product::create([
            'name' => 'Tam Yağlı Süt 1L',
            'description' => 'Günlük taze tam yağlı süt',
            'sku' => 'SUT001',
            'barcode' => '8680001234567',
            'category_id' => $sutUrunleri->id,
            'unit_price' => 15.50,
            'unit' => 'adet',
            'is_active' => true,
        ]);

        $peynir = Product::create([
            'name' => 'Beyaz Peynir 500g',
            'description' => 'Taze beyaz peynir',
            'sku' => 'PEY001',
            'barcode' => '8680001234568',
            'category_id' => $sutUrunleri->id,
            'unit_price' => 45.00,
            'unit' => 'adet',
            'is_active' => true,
        ]);

        $bebekBeziUrun = Product::create([
            'name' => 'Bebek Bezi 4 Numara (20 Adet)',
            'description' => '4 numara bebek bezi paketi',
            'sku' => 'BEZ001',
            'barcode' => '8680001234569',
            'category_id' => $bebekBezi->id,
            'unit_price' => 89.90,
            'unit' => 'paket',
            'is_active' => true,
        ]);

        $deterjan = Product::create([
            'name' => 'Çamaşır Deterjanı 5kg',
            'description' => 'Hassas ciltler için çamaşır deterjanı',
            'sku' => 'DET001',
            'barcode' => '8680001234570',
            'category_id' => $temizlik->id,
            'unit_price' => 75.00,
            'unit' => 'adet',
            'is_active' => true,
        ]);

        // Stoklar oluştur
        // Ana depoda stoklar
        Stock::create([
            'product_id' => $sut->id,
            'location_id' => $kayseriDepo->id,
            'quantity' => 1000,
            'min_quantity' => 100,
            'max_quantity' => 2000,
            'notes' => 'Ana depo stoku',
        ]);

        Stock::create([
            'product_id' => $peynir->id,
            'location_id' => $kayseriDepo->id,
            'quantity' => 500,
            'min_quantity' => 50,
            'max_quantity' => 1000,
            'notes' => 'Ana depo stoku',
        ]);

        Stock::create([
            'product_id' => $bebekBeziUrun->id,
            'location_id' => $kayseriDepo->id,
            'quantity' => 200,
            'min_quantity' => 20,
            'max_quantity' => 500,
            'notes' => 'Ana depo stoku',
        ]);

        Stock::create([
            'product_id' => $deterjan->id,
            'location_id' => $kayseriDepo->id,
            'quantity' => 300,
            'min_quantity' => 30,
            'max_quantity' => 800,
            'notes' => 'Ana depo stoku',
        ]);

        // Kayseri mağazasında stoklar
        Stock::create([
            'product_id' => $sut->id,
            'location_id' => $kayseriMagaza->id,
            'quantity' => 200,
            'min_quantity' => 50,
            'max_quantity' => 500,
            'notes' => 'Mağaza stoku',
        ]);

        Stock::create([
            'product_id' => $peynir->id,
            'location_id' => $kayseriMagaza->id,
            'quantity' => 100,
            'min_quantity' => 25,
            'max_quantity' => 200,
            'notes' => 'Mağaza stoku',
        ]);

        Stock::create([
            'product_id' => $bebekBeziUrun->id,
            'location_id' => $kayseriMagaza->id,
            'quantity' => 50,
            'min_quantity' => 10,
            'max_quantity' => 100,
            'notes' => 'Mağaza stoku',
        ]);

        // Melikgazi şubesinde stoklar
        Stock::create([
            'product_id' => $sut->id,
            'location_id' => $melikgaziSube->id,
            'quantity' => 100,
            'min_quantity' => 30,
            'max_quantity' => 200,
            'notes' => 'Şube stoku',
        ]);

        Stock::create([
            'product_id' => $peynir->id,
            'location_id' => $melikgaziSube->id,
            'quantity' => 50,
            'min_quantity' => 15,
            'max_quantity' => 100,
            'notes' => 'Şube stoku',
        ]);

        // Hürriyet mahallesinde stoklar (düşük stok)
        Stock::create([
            'product_id' => $sut->id,
            'location_id' => $hurriyetMahalle->id,
            'quantity' => 5, // Düşük stok
            'min_quantity' => 20,
            'max_quantity' => 100,
            'notes' => 'Mahalle şubesi stoku',
        ]);

        Stock::create([
            'product_id' => $bebekBeziUrun->id,
            'location_id' => $hurriyetMahalle->id,
            'quantity' => 0, // Stokta yok
            'min_quantity' => 5,
            'max_quantity' => 50,
            'notes' => 'Mahalle şubesi stoku',
        ]);

        // Kullanıcıların location_id'lerini güncelle
        $kayseriUser->update(['location_id' => $kayseriMagaza->id]);
        $hurriyetUser->update(['location_id' => $hurriyetMahalle->id]);

        $this->command->info('Örnek veriler başarıyla eklendi!');
        $this->command->info('Demo Kullanıcılar:');
        $this->command->info('Kayseri Ana Mağaza: kayseri@a101.com / 123456');
        $this->command->info('Hürriyet Şubesi: hurriyet@a101.com / 123456');
    }
}
