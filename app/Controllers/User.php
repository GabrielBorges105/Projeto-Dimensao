<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\Equipe;
use App\Models\Funcionario;
use Core\BaseController;
use Core\Redirect;
use Core\Session;
use Core\Validator;
use Exception;

class User extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->setPageTitle('Entrar');
        $this->getSession('inputs');
        $this->renderView('login/index');
    }

    public function register()
    {
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $this->setPageTitle('Registrar');
        $this->renderView('login/register');
    }

    public function store($request)
    {
        $data = [
            'nome' => $request->post->nome,
            'email' => $request->post->email,
            'senha' => $request->post->senha,
        ];

        $rules = [
            'nome' => 'required',
            'email' => 'required|email',
            'senha' => 'required|min:8',
        ];
        if (Validator::make($data, $rules)) {
            return Redirect::route('/register');
        }
        $data['senha'] = password_hash($request->post->senha, PASSWORD_BCRYPT);
        try {
            $resultCliente = Cliente::where('email', $request->post->email)->first();
            $resultFuncionario = Funcionario::where('email', $request->post->email)->first();
            if ($resultCliente || $resultFuncionario) {
                Redirect::route('/register', [
                    "err" => ["Email já registrado no sistema!"]
                ]);
            } else {
                Cliente::create($data);
                Redirect::route('/login', [
                    "success" => ["Registrado com sucesso!"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/register',  [
                "err" => ["Erro ao tentar se registrar, tente novamente mais tarde!"]
            ]);
        }
    }

    public function auth($request)
    {
        $data = [
            'email' => $request->post->email,
            'senha' => $request->post->senha,
        ];
        $rules = [
            'email' => 'required|email',
            'senha' => 'required',
        ];
        if (Validator::make($data, $rules)) {
            Redirect::route('/login');
        }
        try {
            $resultCliente = Cliente::where('email', $request->post->email)->first();
            if ($resultCliente && password_verify($request->post->senha, $resultCliente->senha)) {
                $user = [
                    'nv' => 0, // nivel de acesso para cliente
                    'id' => $resultCliente->id,
                    'nome' => $resultCliente->nome,
                    'email' => $resultCliente->email
                ];
                Session::set('user', $user);
                Redirect::route('/', [
                    "success" => ["Bem Vindo"]
                ]);
            } else {
                $resultFuncionario = Funcionario::where('email', $request->post->email)->first();
                if ($resultFuncionario && password_verify($request->post->senha, $resultFuncionario->senha)) {
                    $user = [
                        'nv' => $resultFuncionario->nv_acesso, // nivel de acesso para funcionário, podendo ser 0 ou 1
                        'id' => $resultFuncionario->id,
                        'nome' => $resultFuncionario->nome,
                        'email' => $resultFuncionario->email
                    ];
                    Session::set('user', $user);
                    Redirect::route('/', [
                        "success" => ["Bem Vindo"]
                    ]);
                } else {
                    Redirect::route('/login', [
                        "err" => ["Email ou senha incorretos!"],
                        "inputs" => ['email' => $request->post->email]
                    ]);
                }
            }
        } catch (Exception $e) {
            Redirect::route('/login',  [
                "err" => ['Erro ao tentar concluir esta ação.']
            ]);
        }
    }
    public function profile()
    {
        $this->getSession('success');
        $user = Session::get('user');
        $this->getSession('err');
        $this->getSession('info');
        $this->setPageTitle('Meu Perfil');
        $this->getSession('inputs');
        $this->params->user = $user;
        if ($user['nv'] == 0) {
            $this->params->cliente = Cliente::find($user['id']);
            $this->renderView('user/cliente', 'layout');
        } else {
            $this->params->funcionario = Funcionario::find($user['id']);
            $this->renderView('user/funcionario', 'layout');
        }
    }
    public function update($request)
    {
        $user = Session::get('user');
        try {
            if ($user['nv'] == 0) {
                $data = [
                    'nome' => $request->post->nome,
                    'endereco' => $request->post->endereco,
                ];
                $rules = [
                    'nome' => 'required',
                ];
                if (Validator::make($data, $rules)) {
                    Redirect::route('/login');
                }
                $cliente = Cliente::find($user['id']);
                $cliente->nome = $request->post->nome;
                $cliente->endereco = $request->post->endereco;
                $cliente->save();
            } else {
                $data = [
                    'nome' => $request->post->nome,
                    'funcao' => $request->post->funcao,
                ];
                $rules = [
                    'nome' => 'required',
                    'funcao' => 'required',
                ];
                if (Validator::make($data, $rules)) {
                    Redirect::route('/login');
                }
                $funcionario = Funcionario::find($user['id']);
                $funcionario->nome = $request->post->nome;
                $funcionario->funcao = $request->post->funcao;
                $funcionario->save();
            }
            Redirect::route('/perfil',  [
                "success" => ['Dados atualizados com sucesso']
            ]);
        } catch (Exception $e) {
            Redirect::route('/perfil',  [
                "err" => ['Erro ao tentar concluir esta ação.']
            ]);
        }
    }
    public function resetPass()
    {
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $this->params->user = Session::get('user');
        $this->setPageTitle('Alterar Senha');
        $this->renderView('user/senha', 'layout');
    }
    public function passUpdate($request)
    {
        $this->getSession('inputs');
        $user = Session::get('user');
        $data = [
            'senha' => $request->post->senha,
            'senha' => $request->post->senha,
        ];
        $rules = [
            'senha' => 'required|min:8',
        ];
        if (Validator::make($data, $rules)) {
            Redirect::route('/perfil/resetPass');
        }
        try {
            if ($data['senha'] == $request->post->confSenha) {
                if ($user['nv'] == 0) {
                    $userLogado = Cliente::find($user['id'])->first();
                } else {
                    $userLogado = Funcionario::find($user['id'])->first();
                }
                if ($userLogado) {
                    $userLogado->senha = password_hash($data['senha'], PASSWORD_BCRYPT);
                    $userLogado->save();
                    Redirect::route('/perfil', [
                        "success" => ['Senha alterada com sucesso']
                    ]);
                } else {
                    Redirect::route('/perfil/resetPass',  [
                        "err" => ['Erro ao tentar alterar senha']
                    ]);
                }
            } else {
                Redirect::route('/perfil/resetPass',  [
                    "err" => ['Suas senhas não coincidem'],
                    "inputs" => ['senha' => $request->post->senha, 'confSenha' => $request->post->confSenha]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/perfil/resetPass',  [
                "err" => ['Erro ao tentar concluir esta ação.']
            ]);
        }
    }

    public function funcionarios($request)
    {
        $user = Session::get('user');
        $this->params->user = $user;
        $itens_por_pagina = 10;
        if (isset($request->get->page)) {
            $this->params->pageAtual = $request->get->page;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->funcionarios = Funcionario::get()->skip($comecarDe)->take($itens_por_pagina);
            $this->params->funcionariosCount = Funcionario::get()->count();
        } else {
            $this->params->pageAtual = 1;
            $this->params->rowsPagina = $this->params->pageAtual;
            $comecarDe = ($this->params->pageAtual - 1) * $itens_por_pagina;
            $this->params->funcionarios = Funcionario::get()->skip($comecarDe)->take($itens_por_pagina);
            $this->params->funcionariosCount = Funcionario::get()->count();
        }
        $this->params->num_paginas = ceil($this->params->funcionariosCount / $itens_por_pagina);
        if ($this->params->pageAtual < 1 || $this->params->pageAtual > $this->params->num_paginas) {
            $chamados =  [];
            $this->params->funcionariosCount = 0;
        }

        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $this->setPageTitle('Lista de Funcionários');
        $this->renderView('user/listarFuncionarios', 'layout');
    }
    public function funcionarioCreate()
    {
        $user = Session::get('user');
        $this->params->user = $user;
        $this->params->equipes = Equipe::all();
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->getSession('inputs');
        $this->setPageTitle('Registrar Funcionário');
        $this->renderView('user/registrarFuncionario', 'layout');
    }
    public function funcionarioStore($request)
    {
        $user = Session::get('user');
        $this->params->user = $user;
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }

        $data = [
            'nome' => $request->post->nome,
            'funcao' => $request->post->funcao,
            'equipe' => $request->post->equipe,
            'email' => $request->post->email,
            'senha' => $request->post->senha,
        ];

        $rules = [
            'nome' => 'required',
            'funcao' => 'required',
            'equipe' => 'required',
            'email' => 'required',
            'senha' => 'required',
        ];
        if (Validator::make($data, $rules)) {
            return Redirect::route('/funcionarios/create');
        }
        unset($data['equipe']);
        $data['equipe_id'] = $request->post->equipe;
        $data['nv_acesso'] = 1;
        try {
            $resultCliente = Cliente::where('email', $request->post->email)->first();
            $resultFuncionario = Funcionario::where('email', $request->post->email)->first();
            if ($resultCliente || $resultFuncionario) {
                //voltando dados para o input
                unset($data['equipe_id']);
                $data['equipe'] = $request->post->equipe;
                Redirect::route('/funcionarios/create',  [
                    "inputs" => $data,
                    "err" => ["Email já registrado no sistema!"]
                ]);
            } else {
                Funcionario::create($data);
                Redirect::route('/funcionarios/create', [
                    "success" => ["Funcionário registrado com sucesso"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/funcionarios/create',  [
                "err" => ["Erro ao tentar registrar funcionário"]
            ]);
        }
    }
    public function funcionarioPermissao($id, $request)
    {
        $user = Session::get('user');
        $this->params->user = $user;
        if ($user['nv'] != 2) {
            Redirect::route('/',  [
                "err" => ["Você não tem permissão para completar essa ação!"]
            ]);
        }
        if ($user['id'] == $id) {
            Redirect::route('/',  [
                "err" => ["Você não pode alterar sua própria permissão!"]
            ]);
        }
        try {
            $resultFuncionario = Funcionario::find($id);
            if ($resultFuncionario) {
                $resultFuncionario->nv_acesso = ($resultFuncionario->nv_acesso==1)?"2":"1";
                $resultFuncionario->save();
                Redirect::route('/funcionarios', [
                    "success" => ["Permissão do funcionário altarada com sucesso"]
                ]);
            } else {
                Redirect::route('/funcionarios',  [
                    "err" => ["Erro ao tentar alterar permissão do funcionário!"]
                ]);
            }
        } catch (Exception $e) {
            Redirect::route('/funcionarios',  [
                "err" => ["Erro ao tentar alterar permissão do funcionário!"]
            ]);
        }
    }
    public function logout()
    {
        Session::destroy('user');
        Redirect::route('/login');
    }
}
