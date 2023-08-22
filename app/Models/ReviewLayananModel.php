<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewLayananModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "review_layanan";

    protected $fillable = [
        'user_id_reviewer',
        'user_id_student',
        'post_by',
        'rating',
        'isi',
    ];

    public static function filters(){
        $instance = new static();
        return $instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
