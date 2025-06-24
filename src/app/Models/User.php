<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedProducts()
    {
        return $this->belongsToMany(Product::class, 'likes');
    }
    // 1人のユーザーは、複数の商品を「いいね」できる
    // 1つの商品は、複数のユーザーに「いいね」される

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchasedProducts()
    {
        return $this->hasManyThrough(
            Product::class,  //目的モデル
            Purchase::class,  //中間モデル
            'user_id',  //Purchaseの
            'id',  //Productの
            'id',  //Userの
            'product_id'  //Purchaseの
        );
    }
}
