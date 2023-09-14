<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Mengimpor model User
use Illuminate\Database\Eloquent\SoftDeletes;

class NPCModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "npc";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'npc_name',
        'npc_profile',
        'nama_author',
        'npc_story',
        'image_npc',
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
