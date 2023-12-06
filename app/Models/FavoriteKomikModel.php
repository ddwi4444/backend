<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FavoriteKomikModel extends Model
{
    use HasFactory;

    protected $table = "favorite_komik";
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'komik_uuid',
        'user_uuid',
    ];
}
