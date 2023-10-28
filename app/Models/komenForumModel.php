<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class komenForumModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "komen_forum";
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'forum_id',
        'komenForum_parent_id',
        'isi',
        'komen_by',
        'is_reported',
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

    public function komenForums()
    {
        return $this->belongsTo(komenForumModel::class);
    }

    public function forums()
    {
        return $this->hasMany(ForumModel::class);
    }

    public function imagesForumKomen()
    {
        return $this->hasMany(imagesKomenForumModel::class);
    }
}
