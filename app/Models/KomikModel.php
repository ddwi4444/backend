<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomikModel extends Model
{
    use HasFactory;

    protected $table = "komik";
    public $incrementing = false;
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'genre',
        'thumbnail',
        'content',
        'chapter',
        'volume',
        'post_by',
        'jumlah_view',
        'jumlah_like',
        'nama_author',
        'instagram_author',
        'status'
    ];

    public static function filters(){
        $instance = new static();
        return $instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable());
    }
}
