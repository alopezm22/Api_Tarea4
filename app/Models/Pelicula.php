<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    //
    protected $fillable = [
        'Titulo',
        'Descripcion',
        'Clasificacion',
        'Imagen',
        'Genero_Identificador'
    ];

    public function category() {

        return $this->belongsTo(Genero::class);
    }
}
