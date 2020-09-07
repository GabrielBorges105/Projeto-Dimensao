<?php

namespace App\Models;

use Core\BaseModelEloquent;

class Chamado extends BaseModelEloquent
{
    public $table = "chamados";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = ['id','cliente_id','motivo', 'descricao', 'status'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'cliente_id','id');
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class,'equipe_id','id');
    }
}
