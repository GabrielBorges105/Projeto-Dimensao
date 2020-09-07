<?php

namespace App\Controllers;

use App\Models\Chamado;
use App\Models\Equipe as ModelsEquipe;
use App\Models\Funcionario;
use Core\BaseController;
use Core\Redirect;
use Core\Session;
use Core\Validator;
use Exception;

class Equipe extends BaseController
{
    public function index($request)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $this->params->user = $user;

        $itens_por_pagina = 10;
        if (isset($request->get->page)) {
            $this->params->pageAtual = $request->get->page;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->equipes = ModelsEquipe::get()->skip($comecarDe)->take($itens_por_pagina);
            $this->params->equipesCount = ModelsEquipe::get()->count();
        } else {
            $this->params->pageAtual = 1;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->equipes = ModelsEquipe::get()->skip($comecarDe)->take($itens_por_pagina);
            $this->params->equipesCount = ModelsEquipe::get()->count();
        }
        //verificando caso o usuário tente acessar uma paginação inexistente
        $this->params->num_paginas = ceil($this->params->equipesCount / $itens_por_pagina);
        if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
            $chamados =  [];
            $this->params->equipesCount = 0;
        }
        $this->setPageTitle("Equipes");
        $this->renderView("equipe/index", "layout");
    }

    public function store($request)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $data = [
            'nome' => $request->post->nome,
        ];

        $rules = [
            'nome' => 'required',
        ];
        if (Validator::make($data, $rules)) {
            return Redirect::route('/equipes');
        }
        try {
            $result = ModelsEquipe::where('nome', $request->post->nome)->first();
            if ($result) {
                Redirect::route('/equipes', [
                    "err" => ["Uma equipe com o mesmo nome já foi adicionada ao sistema."]
                ]);
            } else {
                $equipe = ModelsEquipe::create($data);
                Redirect::route('/equipes/' . $equipe->id . '/edit', [
                    "success" => ["Equipe registrada com sucesso!"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/equipes',  [
                "err" => ["Erro ao tentar registrar, tente novamente mais tarde!"]
            ]);
        }
    }
    public function show($request)
    {
        $user = Session::get('user');
        if ($user['nv'] == 0) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        if (!(isset($request->get->id))) {
            $funcionario = Funcionario::find($user['id']);
            if ($funcionario->equipe_id != null) {
                Redirect::route('/equipes/show?id=' . $funcionario->equipe_id);
            } else {
                Redirect::route('/',  [
                    "err" => ["Você ainda não faz parte de nenhuma equipe!"]
                ]);
            }
        } else {
            if ($user['nv'] == 1) {
                //verifica se ele está tentando outra equipe
                $funcionario = Funcionario::find($user['id']);
                if ($funcionario->equipe_id != $request->get->id) {
                    Redirect::route('/equipes/show?id' . $funcionario->equipe_id,  [
                        "err" => ["Você não tem permissão para completar essa ação!"]
                    ]);
                }
            }
            $equipe = ModelsEquipe::find($request->get->id);
            $this->getSession('success');
            $this->getSession('err');
            $this->getSession('info');
            $this->params->user = $user;
            if ($equipe) {
                $this->params->equipe = $equipe;
                $itens_por_pagina = 12;
                if (isset($request->get->page)) {
                    $this->params->pageAtual = $request->get->page;
                    $this->params->rowsPagina = $this->params->pageAtual;
                    $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
                    $this->params->chamados = Chamado::get()->where('equipe_id', $equipe->id)->skip($comecarDe)->take($itens_por_pagina);
                    $this->params->chamadosCount = Chamado::get()->where('equipe_id', $equipe->id)->count();
                } else {
                    $this->params->pageAtual = 1;
                    $this->params->rowsPagina = $this->params->pageAtual;
                    $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
                    $this->params->chamados = Chamado::get()->where('equipe_id', $equipe->id)->skip($comecarDe)->take($itens_por_pagina);
                    $this->params->chamadosCount = Chamado::get()->where('equipe_id', $equipe->id)->count();
                }
                //verificando caso o usuário tente acessar uma paginação inexistente
                $this->params->num_paginas = ceil($this->params->chamadosCount / $itens_por_pagina);
                if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
                    $chamados =  [];
                    $this->params->chamadosCount = 0;
                }
                $this->setPageTitle("Visualizando Equipe");
                $this->renderView("equipe/show", "layout");
            } else {
                Redirect::route('/',  [
                    "err" => ["Você não tem permissão para completar esta ação!"]
                ]);
            }
        }
    }
    public function edit($id, $request)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $this->params->user = $user;
        $this->params->equipe = ModelsEquipe::find($id);
        $this->params->funcionariosSemEquipe = Funcionario::get()->where('equipe_id', null);


        $itens_por_pagina = 5;
        if (isset($request->get->page)) {
            $this->params->pageAtual = $request->get->page;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->funcionariosDaEquipe = Funcionario::get()->where('equipe_id', $id)->skip($comecarDe)->take($itens_por_pagina);
            $this->params->membrosCount = Funcionario::get()->where('equipe_id', $id)->count();
        } else {
            $this->params->pageAtual = 1;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->funcionariosDaEquipe = Funcionario::get()->where('equipe_id', $id)->skip($comecarDe)->take($itens_por_pagina);
            $this->params->membrosCount = Funcionario::get()->where('equipe_id', $id)->count();
        }
        $this->params->num_paginas = ceil($this->params->membrosCount / $itens_por_pagina);
        if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
            $this->params->membrosCount = 0;
        }
        if ($this->params->equipe) {
            $this->setPageTitle("Equipes");
            $this->renderView("equipe/edit", "layout");
        } else {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
    }
    public function update($id, $request)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }

        $data = [
            'nome' => $request->post->nome,
        ];

        $rules = [
            'nome' => 'required',
        ];

        if (Validator::make($data, $rules)) {
            return Redirect::route('/equipes/' . $id . '/edit');
        }
        $equipe = ModelsEquipe::find($id);

        try {
            $equipe->update($data);
            Redirect::route('/equipes/' . $id . '/edit', [
                "success" => ["Equipe atualizada com sucesso!"]
            ]);
        } catch (Exception $e) {
            Redirect::route('/equipes',  [
                "err" => ['Erro ao tentar atualizar equipe']
            ]);
        }
    }
    public function delete($id)
    {
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        try {
            $equipe = ModelsEquipe::find($id);
            if ($equipe) {
                $equipe->delete();
                Redirect::route('/equipes', [
                    "success" => ["Equipe removida com sucesso!"]
                ]);
            } else {
                Redirect::route('/equipes',  [
                    "err" => ["Não foi possível remover equipe!"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/equipes',  [
                "err" => ["Não foi possível remover equipe!"]
            ]);
        }
    }

    public function addFuncionario($id, $request)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        try {
            $resultFuncionario = Funcionario::where('id', $request->post->funcionario)->first();
            if ($resultFuncionario) {
                $resultFuncionario->equipe_id = $id;
                $resultFuncionario->save();
                Redirect::route('/equipes/' . $id . '/edit',  [
                    "success" => ["Funcionário adicionado na equipe com sucesso"]
                ]);
            } else {
                Redirect::route('/equipes/' . $id . '/edit', [
                    "err" => ["Erro ao tentar adicionar funcionário na equipe."]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/equipes/' . $id . '/edit',  [
                "err" => ["Erro ao tentar adicionar funcionário na equipe."]
            ]);
        }
    }
    public function removeFuncionarioEquipe($id)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        try {
            $resultFuncionario = Funcionario::where('id', $id)->first();
            if ($resultFuncionario) {
                $idEquipe = $resultFuncionario->equipe_id;
                $resultFuncionario->equipe_id = null;
                $resultFuncionario->save();
                Redirect::route('/equipes/' . $idEquipe . '/edit',  [
                    "success" => ["Funcionário removido da equipe com sucesso"]
                ]);
            } else {
                Redirect::route('/equipes', [
                    "err" => ["Erro ao tentar remover funcionário da equipe."]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/equipes',  [
                "err" => ["Erro ao tentar remover funcionário da equipe."]
            ]);
        }
    }
}
