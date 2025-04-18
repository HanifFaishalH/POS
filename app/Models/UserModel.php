<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class UserModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = "user_id";

    // Add 'photo' to fillable fields
    protected $fillable = ['level_id', 'username', 'nama', 'password', 'profile_photo_path', 'created_at', 'update_at'];

    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];
    
    // Add accessor for photo URL
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url('img/' . $this->profile_photo_path);
        }
        return asset('default.png'); // fallback image
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
}