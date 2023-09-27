<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class imagesMerchandiseModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "images_merchandise";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchandise_id',
        'images_merchandise_path',
    ];

    public function merchandise()
    {
        return $this->belongsTo(MerchandiseModel::class);
    }

    public function imagesMerchandises()
    {
        return $this->hasMany(imagesMerchandiseModel::class);
    }
}
