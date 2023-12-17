<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubKomikModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "sub_komik";
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'komik_id',
        'user_id',
        'judul',
        'slug',
        'thumbnail',
        'content',
        'chapter',
        'post_by',
        'jumlah_view',
        'jumlah_like',
        'instagram_author',
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

    public function komik()
    {
        return $this->belongsTo(KomikModel::class);
    }

    public function komens()
    {
        return $this->hasMany(KomenModel::class);
    }
}
