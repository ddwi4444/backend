<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderProdukMerchandiseModel extends Model
{
    use HasFactory;

    protected $table = "order_produk_merchandise";

    protected $fillable = [
        'uuid',
        'uuidOrderMerchandise',
        'namaProduk',
        'UUIDProduk',
        'quantity',
        'notes',
    ];

    public function orderMerchandise()
    {
        return $this->belongsTo(orderMerchandiseModel::class);
    }
}
