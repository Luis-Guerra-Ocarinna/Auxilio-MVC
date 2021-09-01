<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\Testimony as EntityTestimony;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page {

  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a página
   *
   * @param   Request $request
   * @param   Pagination $obgPagination
   * 
   * @return  string
   */
  private static function getTestimoniesItems(Request $request, &$obgPagination) {
    // DEPOIMENTOS
    $itens = '';

    // QUANTIDADE TOTAL DE REGISTRO
    $quantitadetotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

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
    $results = EntityTestimony::getTestimonies(null, 'id DESC', $obgPagination->getLimit());

    // RENDERIZA O(S) ITEM(S)
    while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
      // VIEW DA DEPOIMENTOS
      $itens .=  View::render('admin\\modules\\testimonies\\item', [
        'id'       => $obTestimony->id,
        'nome'     => $obTestimony->nome,
        'mensagem' => $obTestimony->mensagem,
        'data'     => date('d/m/Y H:i:s', strtotime($obTestimony->data))
      ]);
    }

    // RETORNA OS DEPOIMENTOS
    return $itens;
  }

  /**
   * Método responsável por renderizar a view de listagem de depoimentos
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function getTestimonies(Request $request = null) {
    // CONTEÚDO DA HOME
    $content  = View::render('admin\\modules\\testimonies\\index', [
      'items'      => self::getTestimoniesItems($request, $obgPagination),
      'pagination' => parent::getPagination($request, $obgPagination),
      'status'     => self::getStatus($request)
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Depoimentos >> {{name}}', $content, 'testimonies');
  }

  /**
   * Método responsável por retornar o fomulário de cadastro de um novo depoimento
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function getNewTestimony(Request $request) {
    // CONTEÚDO DO FORMULÁRIO
    $content  = View::render('admin\\modules\\testimonies\\form', [
      'title'    => 'Cadastrar Depoimento',
      'nome'     => '',
      'mensagem' => '',
      'status'   => ''
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Depoimentos >> Cadastrar >> {{name}}', $content, 'testimonies');
  }

  /**
   * Método responsável por cadastrar um novo depoimento no banco
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function setNewTestimony(Request $request) {
    // POST VARS
    $postVars = $request->getPostVars();

    // NOVA INSTÂNCIA DA ENTIDADE DEPOIMENTO 
    $obTestimony = new EntityTestimony;
    $obTestimony->nome     = $postVars['nome'] ?? '';
    $obTestimony->mensagem = $postVars['mensagem'] ?? '';
    $obTestimony->cadastrar();

    // REDIRECIONA O USUÁRIO
    $request->getRouter()->redirect('/admin/testimonies/' . $obTestimony->id . '/edit?status=created');
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
        return Alert::getSuccess('Depoimento criado com sucesso!');
      case 'updated':
        return Alert::getSuccess('Depoimento atualizado com sucesso!');
      case 'deleted':
        return Alert::getSuccess('Depoimento excluido com sucesso!');
    }
  }

  /**
   * Método responsável por retornar o fomulário de edição de depoimento
   *
   * @param   Request  $request  
   * @param   integer  $id
   *
   * @return  string             
   */
  public static function getEditTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obTestimony instanceof EntityTestimony) $request->getRouter()->redirect('/admin/testimonies');

    // CONTEÚDO DO FORMULÁRIO
    $content  = View::render('admin\\modules\\testimonies\\form', [
      'title'    => 'Editar Depoimento',
      'nome'     => $obTestimony->nome,
      'mensagem' => $obTestimony->mensagem,
      'status'   => self::getStatus($request)
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Depoimentos >> Editar >> {{name}}', $content, 'testimonies');
  }

  /**
   * Método responsável por gravar a atualização de um depoimento no banco
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function setEditTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obTestimony instanceof EntityTestimony) $request->getRouter()->redirect('/admin/testimonies');

    // POST VARS
    $postVars = $request->getPostVars();

    // ATUALIZA INSTÂNCIA DA ENTIDADE DEPOIMENTO 
    $obTestimony->nome     = $postVars['nome'] ?? $obTestimony->nome;
    $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
    $obTestimony->atualizar();

    // REDIRECIONA O USUÁRIO
    $request->getRouter()->redirect('/admin/testimonies/' . $obTestimony->id . '/edit?status=updated');
  }

  /**
   * Método responsável por retornar o fomulário de exclusão de um depoimento
   *
   * @param   Request  $request  
   * @param   integer  $id
   *
   * @return  string             
   */
  public static function getDeleteTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obTestimony instanceof EntityTestimony) $request->getRouter()->redirect('/admin/testimonies');

    // CONTEÚDO DO FORMULÁRIO
    $content  = View::render('admin\\modules\\testimonies\\delete', [
      'title'    => 'Excluir Depoimento',
      'nome'     => $obTestimony->nome,
      'mensagem' => $obTestimony->mensagem
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Depoimentos >> Excluir >> {{name}}', $content, 'testimonies');
  }

  /**
   * Método responsável por excluir um depoimento no banco
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function setDeleteTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VÁLIDA A INSTÂNCIA
    if (!$obTestimony instanceof EntityTestimony) $request->getRouter()->redirect('/admin/testimonies');

    // EXCLUI O DEPOIMENTO
    $obTestimony->excluir();

    // REDIRECIONA O USUÁRIO
    $request->getRouter()->redirect('/admin/testimonies?status=deleted');
  }
}
