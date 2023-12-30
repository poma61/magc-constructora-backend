<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioRole extends Model
{
    use HasFactory;
    protected $table = "usuario_roles";

    protected $fillable = [
        'id_user',
        'id_role',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'status',
    ];
}//class
