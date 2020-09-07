<?php

namespace App\Models;

use Core\BaseModelEloquent;

class Funcionario extends BaseModelEloquent
{
    public $table = "funcionarios";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = ['id','equipe_id','nome','email', 'senha', 'funcao','nv_acesso'];
    public function equipe(){
        return $this->belongsTo(Equipe::class,'equipe_id','id');
    }
}
