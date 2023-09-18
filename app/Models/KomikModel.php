<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Mengimpor model User
use Illuminate\Database\Eloquent\SoftDeletes;

class KomikModel extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "komik";
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'judul',
        'genre',
        'thumbnail',
        'jumlah_like',
        'volume',
        'nama_author',
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

    public function subkomiks()
    {
        return $this->hasMany(SubKomikModel::class);
    }
}
