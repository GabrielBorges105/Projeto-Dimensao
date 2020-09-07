<?php

namespace App\Controllers;

use App\Models\Chamado;
use App\Models\Equipe;
use App\Models\User;
use Core\BaseController;
use Core\Redirect;
use Core\Session;
use Core\Validator;
use Exception;

class Agendamento extends BaseController
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
        $this->params->user = Session::get('user');
        $this->params->equipes = Equipe::all();

        $itens_por_pagina = 10;
        $this->params->buscar = @$request->get->buscar;
        if (isset($request->get->page) && isset($request->get->buscar)) {
            $this->params->pageAtual = $request->get->page;
            $this->params->rowsPagina = $this->params->pageAtual;
            $this->params->buscar = @$request->get->buscar;
            if ($request->get->buscar != "") {
                $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
                $chamados =  Chamado::orderBy('status', 'ASC')->get()->where('id', $request->get->buscar)->skip($comecarDe)->take($itens_por_pagina);
                $this->params->chamadosCount = Chamado::get()->where('id', $request->get->buscar)->count();
            } else {
                $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
                $chamados =  Chamado::orderBy('status', 'ASC')->get()->skip($comecarDe)->take($itens_por_pagina);
                $this->params->chamadosCount = Chamado::count();
            }
        } else {
            $this->params->pageAtual = 1;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $chamados =  Chamado::orderBy('status', 'ASC')->get()->skip($comecarDe)->take($itens_por_pagina);
            $this->params->chamadosCount = Chamado::count();
        }
        $this->params->chamados = $chamados;
        $this->params->num_paginas = ceil($this->params->chamadosCount / $itens_por_pagina);
        if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
            $chamados =  [];
            $this->params->chamadosCount = 0;
        }


        $this->setPageTitle("Agendar Chamado");
        $this->renderView("agendamento/index", "layout");
    }

    public function create($page)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }

        $this->params->pageType = $page;
        if ($page != "reparo" && $page != "vistoria") {
            Redirect::route('/',  [
                "err" => ["Url informada está incorreta, verifique e tente novamente"]
            ]);
        }
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('inputs');
        $this->getSession('info');
        $this->params->user = Session::get('user');
        $buscarStts = ($page == "reparo") ? "Aguardando vistoria" : "Pendente";
        $this->params->chamados = Chamado::get()->where('status', '=', $buscarStts);
        $this->params->equipes = Equipe::all();
        $this->setPageTitle("Agendar Chamado");
        $this->renderView("agendamento/create", "layout");
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
            'chamado' => $request->post->chamado,
            'equipe' => $request->post->equipe,
            'data' => $request->post->data,
        ];

        $rules = [
            'chamado' => 'required',
            'equipe' => 'required',
            'data' => 'required',
        ];
        if (Validator::make($data, $rules)) {
            return Redirect::route('/agendamentos');
        }
        try {
            $chamado = Chamado::find($data['chamado']);
            if ($chamado && $chamado->status != "Concluído") {
                $chamado->equipe_id = $data['equipe'];
                $chamado->dataAgendamento = $data['data'];
                $chamado->status = $request->post->servico == "outro" ? 'Aguardando reparo' : 'Aguardando vistoria';
                $chamado->save();
                Redirect::route('/agendamentos', [
                    "success" => ["Agendamento registrado com sucesso"]
                ]);
            } else {
                Redirect::route('/agendamentos',  [
                    "err" => ["Erro ao tentar registrar Agendamento"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/agendamentos',  [
                "err" => ["Erro ao tentar registrar Agendamento"]
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
        } else {
            $data = [
                'equipe' => $request->post->equipe,
                'data' => $request->post->data,
            ];

            $rules = [
                'equipe' => 'required|numeric',
                'data' => 'required',
            ];

            if (Validator::make($data, $rules)) {
                return Redirect::route('/agendamentos');
            }

            try {
                $chamado = Chamado::find($id);
                if ($chamado && $chamado->status != "Concluído") {
                    $chamado->equipe_id = $data['equipe'];
                    $chamado->dataAgendamento = $data['data'];
                    $chamado->save();
                    Redirect::route('/agendamentos', [
                        "success" => ["Agendamento atualizado com sucesso!"]
                    ]);
                } else {
                    Redirect::route('/agendamentos',  [
                        "err" => ['Erro ao tentar atualizar agendamento']
                    ]);
                }
            } catch (Exception $e) {
                Redirect::route('/agendamentos',  [
                    "err" => ['Erro ao tentar atualizar agendamento']
                ]);
            }
        }
    }
    public function delete($id)
    {
        $user = Session::get('user');
        if ($user['nv'] != 2) {
            Redirect::route('/agendamentos',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        try {
            $chamado = Chamado::find($id);
            if ($chamado && $chamado->status != "Concluído") {
                $chamado->status = 'Pendente';
                $chamado->equipe_id = null;
                $chamado->dataAgendamento = null;
                $chamado->save();
                Redirect::route('/agendamentos', [
                    "success" => ["Desagendamento realizado com sucesso!"]
                ]);
            }else{
                Redirect::route('/agendamentos',  [
                    "err" => ["Erro ao tentar remover agendamento"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/agendamentos',  [
                "err" => ["Erro ao tentar remover agendamento"]
            ]);
        }
    }
}
