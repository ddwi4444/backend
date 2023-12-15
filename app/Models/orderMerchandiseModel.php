<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class orderMerchandiseModel extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $table = "order_merchandise";

    protected $fillable = [
        'uuid',
        'user_id',
        'nama',
        'order_by',
        'alamat',
        'tlp',
        'totalPrice',
        'buktiTf',
        'noResi',
        'status'
    ];

    public static function filters(){
        $instance = new static();
        return $instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderProdukMerchandises()
    {
        return $this->hasMany(orderProdukMerchandiseModel::class);
    }
}
