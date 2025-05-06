<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = "user_id";

    protected $fillable = ['level_id', 'username', 'nama', 'password', 'profile_photo_path', 'created_at', 'update_at'];

    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];
    
    // Accessor untuk URL foto profil
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value 
                ? asset('img/' . $value) // Jika ada foto, buat URL dari folder img
                : asset('img/default.png') // Jika tidak ada foto, gunakan default.png
        );
    }


    public function level(): BelongsTo {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    public function getRoleName(): string {
        return $this->level->level_nama;
    }

    public function hasRole($role): bool {
        return $this->level->level_nama === $role;
    }

    public function getRole() {
        return $this->level->level_kode;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}