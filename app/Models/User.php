<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_USER = 'user';
    const ROLE_STUDENT = 'student';
    const ROLE_ADMIN = 'admin';
    const ROLE_OSIS = 'osis';

    protected $table = "user";
    public $incrementing = false;
    protected $primaryKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'uuid',
        'image',
        'email',
        'nama_persona',
        'password',
        'is_verified'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Untuk mengecek role
    public function hasRole($role)
    {
    // check param $role dengan field usertype
    if ($role == $this->usertype) {
        return true;
    }return false;
    }

    public function komiks()
    {
        return $this->hasMany(KomikModel::class);
    }

    public function npcs()
    {
        return $this->hasMany(NPCModel::class);
    }

    public function merchandises()
    {
        return $this->hasMany(MerchandiseModel::class);
    }

    public function forums()
    {
        return $this->hasMany(ForumModel::class);
    }

    public function portofolios()
    {
        return $this->hasMany(PortofolioModel::class);
    }
}
