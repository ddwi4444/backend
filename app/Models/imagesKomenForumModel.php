<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class imagesKomenForumModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "images_komen_forum";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'komenForum_id',
        'images_komenForum_path',
    ];

    public function komenForum()
    {
        return $this->belongsTo(komenForumModel::class);
    }
}
