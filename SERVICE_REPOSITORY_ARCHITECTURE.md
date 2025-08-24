# Service Repository Mimarisi Dokümantasyonu

Bu proje artık %100 Service Repository mimarisine uygun hale getirilmiştir.

## Mimari Yapısı

### 1. Repository Katmanı

#### Interface'ler (`app/Repositories/Interfaces/`)
- `LocationRepositoryInterface` - Lokasyon işlemleri için
- `CategoryRepositoryInterface` - Kategori işlemleri için  
- `ProductRepositoryInterface` - Ürün işlemleri için
- `StockRepositoryInterface` - Stok işlemleri için
- `StockRequestRepositoryInterface` - Stok istek işlemleri için
- `UserRepositoryInterface` - Kullanıcı işlemleri için

#### Implementasyonlar (`app/Repositories/Eloquent/`)
- `LocationRepository` - LocationRepositoryInterface implementasyonu
- `CategoryRepository` - CategoryRepositoryInterface implementasyonu
- `ProductRepository` - ProductRepositoryInterface implementasyonu
- `StockRepository` - StockRepositoryInterface implementasyonu
- `StockRequestRepository` - StockRequestRepositoryInterface implementasyonu
- `UserRepository` - UserRepositoryInterface implementasyonu

### 2. Service Katmanı (`app/Services/`)
- `LocationService` - Lokasyon iş mantığı
- `CategoryService` - Kategori iş mantığı
- `ProductService` - Ürün iş mantığı
- `StockService` - Stok iş mantığı
- `StockRequestService` - Stok istek iş mantığı
- `UserService` - Kullanıcı iş mantığı

### 3. Controller Katmanı (`app/Http/Controllers/`)
Tüm controller'lar artık service'leri kullanmaktadır:
- `LocationController` → `LocationService`
- `CategoryController` → `CategoryService`
- `ProductController` → `ProductService`
- `StockController` → `StockService`
- `StockRequestController` → `StockRequestService`

## Mimari Prensipleri

### 1. Separation of Concerns
- **Repository**: Sadece veri erişimi (CRUD operasyonları)
- **Service**: İş mantığı, validation, business rules
- **Controller**: HTTP request/response handling

### 2. Dependency Injection
- Tüm dependency'ler constructor injection ile sağlanır
- Interface'ler concrete class'lara bind edilir (`AppServiceProvider`)

### 3. Single Responsibility
- Her repository sadece bir model için sorumludur
- Her service sadece bir domain için sorumludur
- Her controller sadece HTTP handling için sorumludur

### 4. Interface Segregation
- Her repository için ayrı interface tanımlanmıştır
- Service'ler sadece ihtiyaç duydukları repository'leri kullanır

## Kullanım Örneği

### Controller'da Service Kullanımı
```php
class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function store(Request $request): RedirectResponse
    {
        $result = $this->productService->createProduct($request->all());

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return redirect()->back()->withErrors($result['errors']);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }
}
```

### Service'de Repository Kullanımı
```php
class ProductService
{
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function createProduct(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            // ... diğer validation kuralları
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $product = $this->productRepository->create($data);
            return [
                'success' => true,
                'data' => $product
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ürün oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }
}
```

## Avantajlar

### 1. Testability
- Service'ler ve Repository'ler kolayca mock'lanabilir
- Unit test'ler yazılabilir

### 2. Maintainability
- Kod tekrarı azalır
- İş mantığı merkezi bir yerde toplanır

### 3. Scalability
- Yeni özellikler kolayca eklenebilir
- Mevcut kod değiştirilmeden genişletilebilir

### 4. Code Reusability
- Service'ler farklı controller'larda kullanılabilir
- Repository'ler farklı service'lerde kullanılabilir

## Dependency Binding

`AppServiceProvider`'da tüm interface'ler concrete class'lara bind edilmiştir:

```php
public function register(): void
{
    // Repository bindings
    $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
    $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
    $this->app->bind(StockRequestRepositoryInterface::class, StockRequestRepository::class);
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
}
```

## Sonuç

Bu proje artık modern Laravel best practice'lerine uygun olarak Service Repository mimarisini tam olarak uygulamaktadır. Tüm business logic service katmanında, veri erişimi repository katmanında ve HTTP handling controller katmanında yapılmaktadır.
