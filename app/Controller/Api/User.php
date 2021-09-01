<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use WilliamCosta\DatabaseManager\Pagination;

class User extends Api {


  /**
   * Método responsável por obter a renderização dos itens de usuários para a api
   *
   * @param   Request    $request
   * @param   Pagination $obgPagination
   * 
   * @return  string
   */
  private static function getUsersItems(Request $request, &$obgPagination) {
    // USUÁRIOS
    $itens = [];

    // QUANTIDADE TOTAL DE REGISTRO
    $quantitadetotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

    // PÁGINA ATUAL
    $queryParams = $request->getQueryParams();
    $paginaAtual = $queryParams['page'] ?? 1;

    // LIMITE POR PÁGINA
    $limit = $queryParams['per_page'] ?? 3;
    $limit = is_numeric($limit) ? $limit : 3;

    // VALIDANDO SE DEVE MOSTRAR TODOS
    $limit = $limit > 0 ? $limit : $quantitadetotal;

   
    // INSTÂNCIA DE PAGINAÇÃO
    $obgPagination = new Pagination($quantitadetotal, $paginaAtual, $limit);

    // RESULTADOS DA PÁGINA
    $results = EntityUser::getUsers(null, 'id ASC', $obgPagination->getLimit());

    // RENDERIZA O(S) ITEM(S)
    while ($obUser = $results->fetchObject(EntityUser::class)) {
      $itens[] = [
        'id'    => (int) $obUser->id,
        'nome'  => $obUser->nome,
        'email' => $obUser->email,
      ];
    }

    // RETORNA OS USUÁRIOS
    return $itens;
  }

  /**
   * Mensagem responsável por retornar os os usuários cadastrados
   *
   * @param   Request  $request  
   *
   * @return  array             
   */
  public static function getUsers(Request $request) {
    return [
      'usuários' => self::getUsersItems($request, $obgPagination),
      'paginacao'   => parent::getPagination($request, $obgPagination)
    ];
  }

  /**
   * Método responsável por retornar os detalhes de um usuário
   *
   * @param   Request  $request  
   * @param   integer  $id       
   *
   * @return  array              
   */
  public static function getUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // BUSCA USUÁRIO
    $obUser = EntityUser::getUserById($id);

    // VALIDA SE O USUÁRIO EXISTE
    if (!$obUser instanceof EntityUser) throw new \Exception("O usuário($id) não foi encontrado", 404);

    // RETORNA OS DETALHES DO USUÁRIO
    return [
      'id'    => (int) $obUser->id,
      'nome'  => $obUser->nome,
      'email' => $obUser->email,
    ];
  }

  /**
   * Método responsável por retornar o usuário atualmente conectado de
   *
   * @param   Request  $request  
   *
   * @return  array             
   */
  public static function getCurrentUser(Request $request) {
    // USUÁRIO ATUAL
    $obUser = $request->user;

    // RETORNA OS DETALHES DO USUÁRIO
    return [
      'id'    => (int) $obUser->id,
      'nome'  => $obUser->nome,
      'email' => $obUser->email,
    ];
  }

  /**
   * Método responsável por cadastrar um novo usuário
   *
   * @param   Request  $request  
   */
  public static function setNewUser(Request $request) {
    // POST VARS
    $postVars = $request->getPostVars();

    // VALIDA OS CAMPOS OBRIGÁTORIOS
    if (!isset($postVars['nome']) || !isset($postVars['email']) || !isset($postVars['senha'])) throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);

    // VALIDA O E-MAIL DO USUÁRIO (DUPLICAÇÃO)
    $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
    if ($obUserEmail instanceof EntityUser) throw new \Exception("O e-mail '$postVars[email]' já está em uso", 400);

    // NOVO USUÁRIO
    $obUser = new EntityUser;
    $obUser->nome  = $postVars['nome'];
    $obUser->email = $postVars['email'];
    $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
    $obUser->cadastrar();

    // RETORNA OS DETALHES DO USUÁRIO CADASTRADO
    return [
      'id'    => (int) $obUser->id,
      'nome'  => $obUser->nome,
      'email' => $obUser->email,
    ];
  }

  /**
   * Método responsável por atualizar um usuário
   *
   * @param   Request  $request  
   */
  public static function setEditUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // POST VARS
    $postVars = $request->getPostVars();

    // VALIDA OS CAMPOS OBRIGÁTORIOS
    if (!isset($postVars['nome']) || !isset($postVars['email']) || !isset($postVars['senha'])) throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);

    // BUSCA O USUÁRIO NO BANCO
    $obUser = EntityUser::getUserById($id);

    // VALIDA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) throw new \Exception("O usuário($id) não foi encontrado", 404);

    // VALIDA O E-MAIL DO USUÁRIO (DUPLICAÇÃO)
    $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
    if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) throw new \Exception("O e-mail '$postVars[email]' já está em uso", 400);;

    // ATUALIZA O USUÁRIO
    $obUser->nome  = $postVars['nome'];
    $obUser->email = $postVars['email'];
    $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
    $obUser->atualizar();

    // RETORNA OS DETALHES DO USUÁRIO ATUALIZADO
    return [
      'id'    => (int) $obUser->id,
      'nome'  => $obUser->nome,
      'email' => $obUser->email
    ];
  }

  /**
   * Método responsável por excluir um usuário
   *
   * @param   Request  $request  
   */
  public static function setDeleteUser(Request $request, $id) {
    // VALIDA O ID DO USUÁRIO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // BUSCA O USUÁRIO NO BANCO
    $obUser = EntityUser::getUserById($id);

    // VALIDA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) throw new \Exception("O usuário($id) não foi encontrado", 404);

    // IMPEDE A EXCLUSÃO DO PRÓPRIO CADASTRO
    if ($obUser->id == $request->user->id) throw new \Exception("Não é possivel excluir o cadastro atualmente conectado", 400);

    // EXCLUI O USUÁRIO
    $obUser->excluir();

    // RETORNA O SUCESSO DO USUÁRIO EXLCUIDO
    return [
      'sucesso' => true,
    ];
  }
}
