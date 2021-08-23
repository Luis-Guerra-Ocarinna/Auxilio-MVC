<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class User extends Page {

  /**
   * Método responsável por obter a renderização dos itens de usuários para a página
   *
   * @param   Request $request
   * @param   Pagination $obgPagination
   * 
   * @return  string
   */
  private static function getUsersItems(Request $request, &$obgPagination) {
    // USUÁRIOS
    $itens = '';

    // QUANTIDADE TOTAL DE REGISTRO
    $quantitadetotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

    // PÁGINA ATUAL
    $queryParams = $request->getQueryParams();
    $paginaAtual = $queryParams['page'] ?? 1;

    // INSTÂNCIA DE PAGINAÇÃO
    $obgPagination = new Pagination($quantitadetotal, $paginaAtual, 3);

    // RESULTADOS DA PÁGINA
    $results = EntityUser::getUsers(null, 'id DESC', $obgPagination->getLimit());

    // RENDERIZA O(S) ITEM(S)
    while ($obUser = $results->fetchObject(EntityUser::class)) {
      // VIEW DA USUÁRIOS
      $itens .=  View::render('admin\\modules\\users\\item', [
        'id'    => $obUser->id,
        'nome'  => $obUser->nome,
        'email' => $obUser->email
      ]);
    }

    // RETORNA OS DEPOIMENTOS
    return $itens;
  }

  /**
   * Método responsável por renderizar a view de listagem de usuários
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function getUsers(Request $request = null) {
    // CONTEÚDO DA HOME
    $content  = View::render('admin\\modules\\users\\index', [
      'items'      => self::getUsersItems($request, $obgPagination),
      'pagination' => parent::getPagination($request, $obgPagination),
      'status'     => self::getStatus($request)
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Usuários >> {{name}}', $content, 'users');
  }

  /**
   * Método responsável por retornar a mensagem de status
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  private static function getStatus(Request $request) {
    // QUERY PARAMS
    $queryParams = $request->getQueryParams();

    // STATUS
    if (!isset($queryParams['status'])) return '';

    // MENSAGEM DE STATUS
    switch ($queryParams['status']) {
      case 'created':
        return Alert::getSuccess('Usuário criado com sucesso!');
      case 'updated':
        return Alert::getSuccess('Usuário atualizado com sucesso!');
      case 'deleted':
        return Alert::getSuccess('Usuário excluido com sucesso!');
      case 'duplicated':
        return Alert::getError('O e-mail digitado já está sendo utilizado por outro usuário.');
    }
  }

  /**
   * Método responsável por retornar o fomulário de cadastro de um novo usuários
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function getNewUser(Request $request) {
    // CONTEÚDO DO FORMULÁRIO
    $content  = View::render('admin\\modules\\users\\form', [
      'title'    => 'Cadastrar Usuário',
      'nome'     => '',
      'email'    => '',
      'status'   => self::getStatus($request)
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Usuários >> Cadastrar >> {{name}}', $content, 'users');
  }

  /**
   * Método responsável por cadastrar um novo usuários no banco
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function setNewUser(Request $request) {
    // POST VARS
    $postVars = $request->getPostVars();
    $nome = $postVars['nome'] ?? '';
    $email = $postVars['email'] ?? '';
    $senha = $postVars['senha'] ?? '';

    // VALIDA O E-MAIL DO USUÁRIO
    $obUser = EntityUser::getUserByEmail($email);
    if ($obUser instanceof EntityUser) {
      // REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/users/new?status=duplicated');
    }

    // NOVA INSTÂNCIA DA ENTIDADE DEPOIMENTO 
    $obUser        = new EntityUser;
    $obUser->nome  = $nome;
    $obUser->email = $email;
    $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
    $obUser->cadastrar();

    // REDIRECIONA O USUÁRIO
    $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=created');
  }

  /**
   * Método responsável por retornar o fomulário de edição de usuário
   *
   * @param   Request  $request  
   * @param   integer  $id
   *
   * @return  string             
   */
  public static function getEditUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O USUÁRIO DO BANCO DE DADOS
    $obUser = EntityUser::getUserById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) $request->getRouter()->redirect('/admin/users');

    // CONTEÚDO DO FORMULÁRIO
    $content  = View::render('admin\\modules\\users\\form', [
      'title'  => 'Editar Usuário',
      'nome'   => $obUser->nome,
      'email'  => $obUser->email,
      'status' => self::getStatus($request)
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Usuários >> Editar >> {{name}}', $content, 'users');
  }

  /**
   * Método responsável por gravar a atualização de um usuário no banco
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function setEditUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O USUÁRIO DO BANCO DE DADOS
    $obUser = EntityUser::getUserById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) $request->getRouter()->redirect('/admin/users');

    // POST VARS
    $postVars = $request->getPostVars();
    $nome = $postVars['nome'] ?? '';
    $email = $postVars['email'] ?? '';
    $senha = $postVars['senha'] ?? '';

    // VALIDA O E-MAIL DO USUÁRIO
    $obUserEmail = EntityUser::getUserByEmail($email);
    if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
      // REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/users/' . $id . '/edit?status=duplicated');
    }

    // ATUALIZA A INSTÂNCIA
    $obUser->nome  = $nome;
    $obUser->email = $email;
    $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
    $obUser->atualizar();

    // REDIRECIONA O USUÁRIO
    $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=updated');
  }

  /**
   * Método responsável por retornar o fomulário de exclusão de um usuário
   *
   * @param   Request  $request  
   * @param   integer  $id
   *
   * @return  string             
   */
  public static function getDeleteUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O USUÁRIO DO BANCO DE DADOS
    $obUser = EntityUser::getUserById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) $request->getRouter()->redirect('/admin/users');

    // CONTEÚDO DO FORMULÁRIO
    $content  = View::render('admin\\modules\\users\\delete', [
      'title' => 'Excluir Usuário',
      'nome'  => $obUser->nome,
      'email' => $obUser->email
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Usuários >> Excluir >> {{name}}', $content, 'users');
  }

  /**
   * Método responsável por excluir um usuário no banco
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function setDeleteUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O USUÁRIO DO BANCO DE DADOS
    $obUser = EntityUser::getUserById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) $request->getRouter()->redirect('/admin/users');

    // EXCLUI O USUÁRIO
    $obUser->excluir();

    // REDIRECIONA O USUÁRIO
    $request->getRouter()->redirect('/admin/users?status=deleted');
  }
}
