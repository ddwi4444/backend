<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class imagesAnnouncementModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "images_announcement";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'announcement_id',
        'images_announcement_path',
    ];

    public function announcement()
    {
        return $this->belongsTo(AnnouncementModel::class);
    }
}
