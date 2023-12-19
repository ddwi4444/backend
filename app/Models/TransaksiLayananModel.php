<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiLayananModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "transaksi_layanan";

    protected $fillable = [
        'uuid',
        'user_id_servicer',
        'user_id_customer',
        'project_name',
        'customer_name',
        'offering_cost',
        'description',
        'storyboard',
        'contact_person',
        'buktiTf',
        'is_deal',
        'is_done',
        'confirm_buktiTf',
    ];

    public static function filters(){
        $instance = new static();
        return $instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Definisikan relasi 1:1 ke model ReviewLayanan
    public function reviewLayanan()
    {
        return $this->hasOne(ReviewLayananModel::class);
    }
}
