<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class likeSubKomikModel extends Model
{
    use HasFactory;

    protected $table = "like_sub_comic";
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subKomik_uuid',
        'user_uuid',
    ];
}
