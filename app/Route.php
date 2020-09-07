<?php
// rotas de login e registro
$route[] = ['/login', 'user@index'];
$route[] = ['/login/auth', 'user@auth'];
$route[] = ['/logout', 'user@logout']; 
$route[] = ['/register', 'user@register'];
$route[] = ['/register/store', 'user@store'];
$route[] = ['/perfil', 'user@profile', 'auth'];
$route[] = ['/perfil/update', 'user@update', 'auth'];
$route[] = ['/perfil/resetPass', 'user@resetPass', 'auth'];
$route[] = ['/perfil/passUpdate', 'user@passUpdate', 'auth'];

// rota home
$route[] = ['/', 'home@index', 'auth'];

// rotas chamados
$route[] = ['/chamados', 'chamado@index', 'auth'];
$route[] = ['/chamados/create', 'chamado@create', 'auth'];
$route[] = ['/chamados/store', 'chamado@store', 'auth'];
$route[] = ['/chamados/{id}/show', 'chamado@show', 'auth'];
$route[] = ['/chamados/{id}/edit', 'chamado@edit', 'auth'];
$route[] = ['/chamados/{id}/update', 'chamado@update', 'auth'];
$route[] = ['/chamados/{id}/delete', 'chamado@delete', 'auth'];
$route[] = ['/chamados/all', 'chamado@all', 'auth'];
$route[] = ['/chamados/{id}/checked', 'chamado@checked', 'auth'];


// rotas equipes
$route[] = ['/equipes', 'equipe@index', 'auth'];
$route[] = ['/equipes/create', 'equipe@create', 'auth'];
$route[] = ['/equipes/show', 'equipe@show', 'auth'];
$route[] = ['/equipes/store', 'equipe@store', 'auth'];
$route[] = ['/equipes/{id}/edit', 'equipe@edit', 'auth'];
$route[] = ['/equipes/{id}/update', 'equipe@update', 'auth'];
$route[] = ['/equipes/{id}/delete', 'equipe@delete', 'auth'];
$route[] = ['/equipes/{id}/addFuncionario', 'equipe@addFuncionario', 'auth'];
$route[] = ['/equipes/{id}/removeFuncionario', 'equipe@removeFuncionarioEquipe', 'auth'];


// rotas funcionario
$route[] = ['/funcionarios', 'user@funcionarios', 'auth'];
$route[] = ['/funcionarios/create', 'user@funcionarioCreate', 'auth'];
$route[] = ['/funcionarios/store', 'user@funcionarioStore', 'auth'];
$route[] = ['/funcionarios/{id}/permissao', 'user@funcionarioPermissao', 'auth'];




// rotas agendamentos
$route[] = ['/agendamentos', 'agendamento@index', 'auth'];
$route[] = ['/agendamentos/{tipo}/create', 'agendamento@create', 'auth'];
$route[] = ['/agendamentos/store', 'agendamento@store', 'auth'];
$route[] = ['/agendamentos/{id}/update', 'agendamento@update', 'auth'];
$route[] = ['/agendamentos/{id}/delete', 'agendamento@delete', 'auth'];


return $route;
