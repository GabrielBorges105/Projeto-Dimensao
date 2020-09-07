<?php

namespace App\Controllers;

use App\Models\Chamado as ModelsChamado;
use App\Models\Cliente;
use Core\BaseController;
use Core\Redirect;
use Core\Session;
use Core\Validator;
use Exception;

class Chamado extends BaseController
{
    public function index($request)
    {
        $user = Session::get('user');
        if ($user['nv'] != 0) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->params->user = $user;

        $this->params->cliente = Cliente::find($user['id']);
        $itens_por_pagina = 10;
        if (isset($request->get->page)) {
            $this->params->pageAtual = $request->get->page;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->meusChamados = $this->params->cliente->chamado->skip($comecarDe)->take($itens_por_pagina);
            $this->params->chamadosCount = $this->params->cliente->chamado->count();
        } else {
            $this->params->pageAtual = 1;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->meusChamados = $this->params->cliente->chamado->skip($comecarDe)->take($itens_por_pagina);
            $this->params->chamadosCount = $this->params->cliente->chamado->count();
        }
        //verificando caso o usuário tente acessar uma paginação inexistente
        $this->params->num_paginas = ceil($this->params->chamadosCount / $itens_por_pagina);
        if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
            $chamados =  [];
            $this->params->chamadosCount = 0;
        }

        $this->setPageTitle("Meus Chamados");
        $this->renderView("chamado/index", "layout");
    }
    public function all($request)
    {
        $user = Session::get('user');
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        if ($user['nv'] == 0) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $itens_por_pagina = 10;
        $this->params->buscar = @$request->get->buscar;
        if (isset($request->get->page) && isset($request->get->buscar)) {
            $this->params->pageAtual = $request->get->page;
            $this->params->rowsPagina = $this->params->pageAtual;
            $this->params->buscar = @$request->get->buscar;
            if ($request->get->buscar != "") {
                $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
                $chamados =  ModelsChamado::get()->where('id', $request->get->buscar)->skip($comecarDe)->take($itens_por_pagina);
                $this->params->chamadosCount = ModelsChamado::get()->where('id', $request->get->buscar)->count();
            } else {
                $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
                $chamados =  ModelsChamado::get()->skip($comecarDe)->take($itens_por_pagina);
                $this->params->chamadosCount = ModelsChamado::all()->count();
            }
        } else {
            $this->params->pageAtual = 1;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $chamados =  ModelsChamado::get()->skip($comecarDe)->take($itens_por_pagina);
            $this->params->chamadosCount = ModelsChamado::all()->count();
        }
        $this->params->chamados = $chamados;
        $this->params->num_paginas = ceil($this->params->chamadosCount / $itens_por_pagina);
        if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
            $chamados =  [];
            $this->params->chamadosCount = 0;
        }


        $this->params->user = Session::get('user');
        $this->setPageTitle("Todos Chamados");
        $this->renderView("chamado/all", "layout");
    }
    public function show($id)
    {
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $user = Session::get('user');
        $this->params->chamado = ModelsChamado::find($id);
        if ($user['id'] != $this->params->chamado->cliente->id && $user['nv'] == 0 ) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->params->user = Session::get('user');
        $this->setPageTitle($this->params->chamado->motivo);
        $this->renderView("chamado/show", "layout");
    }
    public function create()
    {
        $user = Session::get('user');
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('inputs');
        $this->getSession('info');
        $this->params->user = $user;
        if ($user['nv'] > 0 ) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->setPageTitle("Adicionar Chamado");
        $this->renderView("chamado/create", "layout");
    }

    public function store($request)
    {
        $user = Session::get('user');
        if ($user['nv'] > 0 ) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $data = [
            'cliente_id' => $user['id'],
            'motivo' => $request->post->motivo,
            'descricao' => $request->post->descricao,
            'status' => "Pendente",
        ];

        $rules = [
            'motivo' => 'required',
            'descricao' => 'required',
        ];
        if (Validator::make($data, $rules)) {
            return Redirect::route('/chamados/create');
        }
        try {
            ModelsChamado::create($data);
            Redirect::route('/chamados/create', [
                "success" => ["Chamado registrado com sucesso"]
            ]);
        } catch (Exception $e) {
            Redirect::route('/chamados/create',  [
                "err" => ["Erro ao tentar registrar chamado"]
            ]);
        }
    }
    public function edit($id)
    {
        $user = Session::get('user');
        $this->params->chamado = ModelsChamado::find($id);
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        if ($user['id'] != $this->params->chamado->cliente->id || $this->params->chamado->status != "Pendente" || $user['nv'] > 0) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->params->user = Session::get('user');
        $this->setPageTitle('Editar Chamado');
        $this->renderView("chamado/edit", "layout");
    }
    public function update($id, $request)
    {
        $user = Session::get('user');
        $data = [
            'motivo' => $request->post->motivo,
            'descricao' => $request->post->descricao,
        ];

        $rules = [
            'motivo' => 'required',
            'descricao' => 'required',
        ];

        if (Validator::make($data, $rules)) {
            return Redirect::route('/chamados/' . $id . '/edit');
        }
        $chamado = ModelsChamado::find($id);
        if ($chamado->cliente->id != $user['id'] || $chamado->status != "Pendente" || $user['nv'] > 0) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        try {

            $chamado->update($data);
            Redirect::route('/chamados/' . $id . '/edit', [
                "success" => ["Chamado atualizado com sucesso!"]
            ]);
        } catch (Exception $e) {
            Redirect::route('/chamados/' . $id . '/edit',  [
                "err" => ['Erro ao tentar atualizar chamado']
            ]);
        }
    }
    
    public function delete($id)
    {
        $user = Session::get('user');
        try {
            $chamado = ModelsChamado::where('cliente_id', $user['id'])->find($id);
            if ($chamado && $chamado->status == "Pendente" && $user['nv'] == 0) {
                $chamado->delete();
                Redirect::route('/chamados', [
                    "success" => ["Chamado deletado com sucesso!"]
                ]);
            } else {
                Redirect::route('/chamados', [
                    "err" => ["Erro ao tentar remover chamado"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/chamados',  [
                "err" => ["Erro ao tentar remover chamado"]
            ]);
        }
    }

    public function checked($id)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        try {
            $chamado = ModelsChamado::find($id);
            if ($chamado && $chamado->status!="Pendente") {
                $chamado->status = 'Concluído';
                $chamado->save();
                Redirect::route('/agendamentos', [
                    "success" => ["Chamado concluído com sucesso"]
                ]);
            }else{
                Redirect::route('/agendamentos',  [
                    "err" => ["Erro ao tentar registrar concluir chamado"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/agendamentos',  [
                "err" => ["Erro ao tentar registrar concluir chamado"]
            ]);
        }
    }
}
