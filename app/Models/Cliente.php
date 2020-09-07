<?php

namespace App\Models;

use Core\BaseModelEloquent;

class Cliente extends BaseModelEloquent
{
    public $table = "clientes";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = ['id','nome','email', 'senha', 'endereco'];
    public function chamado()
    {
        return $this->hasMany(Chamado::class,'cliente_id','id');
    }
}
