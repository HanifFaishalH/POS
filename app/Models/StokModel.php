<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'stok_id';
    protected $table = 't_stok';

    protected $fillable = [
        'barang_id',
        'supplier_id',
        'user_id',
        'stok_tanggal',
        'stok_jumlah'
    ];

    public $timestamps = false; // Your table doesn't have created_at/updated_at

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }
}