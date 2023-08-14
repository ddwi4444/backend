<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortofolioModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'thumbnail',
        'link',
    ];

    protected $table = "portofolio";

    public static function filters(){
        $instance = new static();
        return $instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
