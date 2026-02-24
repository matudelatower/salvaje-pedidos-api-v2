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
        'role',
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

    /**
     * Verificar si el usuario es admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario tiene rol usuario
     */
    public function isUsuario(): bool
    {
        return $this->role === 'usuario';
    }

    /**
     * Verificar si el usuario puede gestionar pedidos y banners
     */
    public function canManageOrdersAndBanners(): bool
    {
        return $this->role === 'usuario' || $this->role === 'admin';
    }

    /**
     * Verificar si el usuario puede gestionar usuarios, categorías, unidades y productos
     */
    public function canManageFullSystem(): bool
    {
        return $this->role === 'admin';
    }
}
