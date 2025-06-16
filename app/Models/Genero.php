<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    
    protected $fillable = [
        'Titulo',
        'Icono',
        'user_Identificador'
    ];

    public function elements(){

        return $this->hasMany(Pelicula::class);
    }

    public function user() { 

        return $this->belongsTo(User::class);
    }
}
