<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\Testimony as EntityTestimony;
use WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Api {


  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a api
   *
   * @param   Request    $request
   * @param   Pagination $obgPagination
   * 
   * @return  string
   */
  private static function getTestimoniesItems(Request $request, &$obgPagination) {
    // DEPOIMENTOS
    $itens = [];

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
      $itens[] = [
        'id'       => (int) $obTestimony->id,
        'nome'     => $obTestimony->nome,
        'mensagem' => $obTestimony->mensagem,
        'data'     => $obTestimony->data
      ];
    }

    // RETORNA OS DEPOIMENTOS
    return $itens;
  }

  /**
   * Mensagem responsável por retornar os os depoimentos cadastrados
   *
   * @param   Request  $request  
   *
   * @return  array             
   */
  public static function getTestimonies(Request $request) {
    return [
      'depoimentos' => self::getTestimoniesItems($request, $obgPagination),
      'paginacao'   => parent::getPagination($request, $obgPagination)
    ];
  }

  /**
   * Método responsável por retornar os detalhes de um depoimento
   *
   * @param   Request  $request  
   * @param   integer  $id       
   *
   * @return  array              
   */
  public static function getTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // BUSCA DEPOIMENTO
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VALIDA SE O DEPOIMENTO EXISTE
    if (!$obTestimony instanceof EntityTestimony) throw new \Exception("O depoimento($id) não foi encontrado", 404);

    // RETORNA OS DETALHES DO DEPOIMENTO
    return [
      'id'       => (int) $obTestimony->id,
      'nome'     => $obTestimony->nome,
      'mensagem' => $obTestimony->mensagem,
      'data'     => $obTestimony->data
    ];
  }

  /**
   * Método responsável por cadastrar um novo depoimento
   *
   * @param   Request  $request  
   */
  public static function setNewTestimony(Request $request) {
    // POST VARS
    $postVars = $request->getPostVars();

    // VALIDA OS CAMPOS OBRIGÁTORIOS
    if (!isset($postVars['nome']) || !isset($postVars['mensagem'])) throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);

    // NOVO DEPOIMENTO
    $obTestimony = new EntityTestimony;
    $obTestimony->nome     = $postVars['nome'];
    $obTestimony->mensagem = $postVars['mensagem'];
    $obTestimony->cadastrar();

    // RETORNA OS DETALHES DO DEPOIMENTO CADASTRADO
    return [
      'id'       => (int) $obTestimony->id,
      'nome'     => $obTestimony->nome,
      'mensagem' => $obTestimony->mensagem,
      'data'     => $obTestimony->data
    ];
  }

  /**
   * Método responsável por atualizar um depoimento
   *
   * @param   Request  $request  
   */
  public static function setEditTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // POST VARS
    $postVars = $request->getPostVars();

    // VALIDA OS CAMPOS OBRIGÁTORIOS
    if (!isset($postVars['nome']) || !isset($postVars['mensagem'])) throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);

    // BUSCA O DEPOIMENTO NO BANCO
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VALIDA A INSTÂNCIA
    if (!$obTestimony instanceof EntityTestimony) throw new \Exception("O depoimento($id) não foi encontrado", 404);

    // ATUALIZA O DEPOIMENTO
    $obTestimony->nome     = $postVars['nome'];
    $obTestimony->mensagem = $postVars['mensagem'];
    $obTestimony->atualizar();

    // RETORNA OS DETALHES DO DEPOIMENTO ATUALIZADO
    return [
      'id'       => (int) $obTestimony->id,
      'nome'     => $obTestimony->nome,
      'mensagem' => $obTestimony->mensagem,
      'data'     => $obTestimony->data
    ];
  }

  /**
   * Método responsável por excluir um depoimento
   *
   * @param   Request  $request  
   */
  public static function setDeleteTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // BUSCA O DEPOIMENTO NO BANCO
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VALIDA A INSTÂNCIA
    if (!$obTestimony instanceof EntityTestimony) throw new \Exception("O depoimento($id) não foi encontrado", 404);

    // EXCLUI O DEPOIMENTO
    $obTestimony->excluir();

    // RETORNA O SUCESSO DO DEPOIMENTO EXLCUIDO
    return [
      'sucesso' => true,
    ];
  }
}
