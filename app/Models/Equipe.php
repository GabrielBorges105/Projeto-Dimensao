<?php

namespace App\Models;

use Core\BaseModelEloquent;

class Equipe extends BaseModelEloquent
{
    public $table = "equipes";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = ['id', 'nome' ];
    public function chamado()
    {
        return $this->hasMany(Chamado::class,'equipe_id','id');
    }
}
