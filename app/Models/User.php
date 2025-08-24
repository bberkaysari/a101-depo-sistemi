<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Kullanıcının yaptığı stok istekleri
    public function stockRequests()
    {
        return $this->hasMany(StockRequest::class, 'requested_by');
    }

    // Kullanıcının yanıtladığı stok istekleri
    public function respondedStockRequests()
    {
        return $this->hasMany(StockRequest::class, 'responded_by');
    }

    // Kullanıcının yaptığı stok transferleri
    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'transferred_by');
    }

    /**
     * Get the user's location
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
