<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class imagesForumModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "images_forum";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'forum_id',
        'images_forum_path',
    ];

    public function forum()
    {
        return $this->belongsTo(ForumModel::class);
    }
}
