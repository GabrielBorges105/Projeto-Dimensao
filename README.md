# PROJETO DESAFIO - GRUPO DIMENSÃO

<a id="sobre"></a>
## Sobre

Projeto desenvolvido durante o processo seletivo do Grupo Dimensão, cujo objetivo era analisar as seguintes habilidades:

* Análise e compreensão dos requisitos (problema)
* Lógica e conhecimento sobre a liguagem PHP
* Lógica e conhecimento sobre a liguagem Javascript, HTML + CSS
* Organização do projeto e do código
* Compreensão de modelos de dados e modelagem de novas estruturas
* Performance e velocidade de entrega
* Qualidade da entrega

-------------------------------------------------

Tabela de conteúdos
=================
   * [Modelagem do Banco de Dados](https://github.com/GabrielBorges105/Projeto-Dimensao/blob/master/Modelagem%20Banco%20de%20Dados.png)
   * [Sobre](#sobre)
   * [Requisitos](#requisitos)
   * [Instalação](#instalacao)
   * [Como usar](#como-usar)
        - [Cliente](#como-usar-cliente)
        - [Funcionário Comum](#como-usar-comum)
        - [Funcionário Administrador](#como-usar-administrador)
   * [Tecnologias](#tecnologias)
   * [Autor](#autor)
   * [Licença](#licenca)

-------------------------------------------------

<a id="requisitos"></a>
## Requisitos

* Primeiramente verifique se você possui os seguintes requisitos:
    * PHP 7.2.5+
    * Composer
    * Mysql
* Após cumprir os requisitos acima, você deverá realizar as seguintes etapas:
    * Crie um banco de dados com o nome "db_dimensao", caso queira personalizar informe-o também no arquivo database.php
        ```bash
        -- app
            |-- database.php
        ```

-------------------------------------------------

<a id="instalacao"></a>
## 🛠 Instalação 

Antes de começar, você vai precisar ter instalado em sua máquina as seguintes ferramentas:
[Php](https://www.php.net/downloads), [Composer](https://getcomposer.org/). 

* Faça Download do Projeto ou clone usando comando
    ```bash
    git clone <https://github.com/GabrielBorges105/Projeto-Dimensao>
    ```
* Importe o banco de dados para o Mysql, você poderá encontrar o arquivo .sql no seguinte caminho:
    ```bash
    -- storage
        |-- db_dimensao.sql
    ```
    

* Pelo terminal vá até a raiz do projeto e execute:
    ```bash
    composer install
    ```

-------------------------------------------------

<a id="como-usar"></a>
## 🎲 Como Usar

Para utilizar você vai precisar ter instalado um servidor php, neste caso vamos utilizar o servidor embutido do php.
* Na pasta do projeto execute o seguinte comando em seu terminal:
    ```bash
    php -S localhost:8080
    ```

Feito isso?!

<strong>Agora vamos compreender os diferentes níveis de acesso do sistema, são eles:</strong>

<a id="como-usar-cliente"></a>
### Cliente

- Acessando a base do projeto atravéz da rota "/" você poderá optar por criar uma nova conta ou utilizar uma já criada anteriormente, caso possua.
- Depois de utilizar suas credenciais de acesso como Cliente, você poderá acessar as seguintes funcionalidades:
    - [x] Abrir Chamado
    - [x] Visualizar seus Chamados
    - [x] Editar e Remover Chamados <strong>antes de serem respondidos</strong>.

<a id="como-usar-comum"></a>
### Funcionário Comum

- Diferentemente da conta do cliente, o funcionário somente poderá ser registrado no sistema pelo administrador.
- Depois de receber suas credenciais de acesso feitas pelo administrador, você poderá utilizar a rota "/" para ter acesso ao sistema.
- Após fazer login no sistema serão apresentadas as seguintes funcionalidades:
    - [x] Visualizar Todos Chamados
    - [x] Minha Equipe e chamados sendo atendidos por ela.

<a id="como-usar-administrador"></a>
### Funcionário Administrador

- Por padrão já disponibilizei uma conta com o nível Administrador, para acessá-la utilize as seguintes informações:
    - Email: admin@admin.com
    - Senha: admin123
- Após fazer login no sistema serão apresentadas as seguintes funcionalidades:
    - [x] Visualizar Todos Chamados
    - [x] Minha Equipe e chamados sendo atendidos por ela.
    - [x] Todas Equipes.
    - [x] Cadastrar, Editar e Remover equipes, somente será permitida a remoção de uma equipe caso ela ainda não tenha atendido a nenhum chamado.
    - [x] Lista de Funcionários.
    - [x] Adicionar e Remover Funcionários em equipes.
    - [x] Atribuir permissões a outros Funcionários.



-------------------------------------------------

<a id="tecnologias"></a>
## 🛠 Tecnologias

As seguintes ferramentas foram utilizadas na construção do projeto

- [CC Framework 1.0](https://github.com/GabrielBorges105/CC-Framework)
- [Boostrap v4.5.2](https://getbootstrap.com/)
- [Composer v1.10.10](https://getcomposer.org/)
- [jQuery v3.5.1](https://jquery.com/)

-------------------------------------------------

<a id="autor"></a>
## Autor

<a href="http://www.jeangabrieldeveloper.com">
 <img style="border-radius: 50%;" src="http://www.jeangabrieldeveloper.com/img/perfil2.png" width="100px;" alt=""/>
 <br />
 <sub><b>Jean Gabriel</b></sub></a> <a href="http://www.jeangabrieldeveloper.com" title="Portfólio"></a>


Feito com ❤️ por Jean Gabriel 👋🏽 Entre em contato!


[![Linkedin Badge](DSA)](https://www.linkedin.com/in/jean-b-6b9180139/)  [![Hotmail Badge](https://img.shields.io/badge/-cantanhede2000@hotmail.com-c14438?style=flat-square&logo=Hotmail&logoColor=white&link=mailto:cantanhede2000@hotmail.com)](mailto:cantanhede2000@hotmail.com)

-------------------------------------------------

<a id="licenca"></a>
## Licença

[MIT](https://choosealicense.com/licenses/mit/)
