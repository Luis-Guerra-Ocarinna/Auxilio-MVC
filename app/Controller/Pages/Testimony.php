<?php

namespace App\Controller\Pages;

use \App\Model\Entity\Testimony as EntityTestimony;
use \App\Utils\View;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page {

  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a página
   *
   * @param   Request $request
   * @param   Pagination $obgPagination
   * 
   * @return  string
   */
  private static function getTestimoniesItems($request, &$obgPagination) {
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
      $itens .=  View::render('pages\\testimony\\item', [
        'nome'     => $obTestimony->nome,
        'mensagem' => $obTestimony->mensagem,
        'data'     => date('d/m/Y H:i:s', strtotime($obTestimony->data))
      ]);
    }

    // RETORNA OS DEPOIMENTOS
    return $itens;
  }

  /**
   * Método responsável por retornar o conteúdo (view) de depoimentos
   *
   * @param   Request $request
   * 
   * @return  string  
   */
  public static function getTestimonies($request) {

    // VIEW DA DEPOIMENTOS
    $content =  View::render('pages\\testimonies', [
      'itens' => self::getTestimoniesItems($request, $obgPagination),
      'pagination' => parent::getPagination($request, $obgPagination)
    ]);

    // RETORNA A VIEW DA PÁGINA 
    return parent::getPage('DEPOIMENTOS > {{name}}', $content);
  }

  /**
   * Método responsável por cadastrar um depoimento
   *
   * @param   Request  $request
   *
   * @return  string
   */
  public static function insertTestimmony($request) {
    // DADOS DO POST
    $postVars = $request->getPostVars();

    // NOVA INSTÂNCIA DA ENTIDADE DEPOIMENTO 
    $obTestimony = new EntityTestimony;
    $obTestimony->nome = $postVars['nome'];
    $obTestimony->mensagem = $postVars['mensagem'];
    $obTestimony->cadastrar();

    // RETORNA A PÁGINA DE LISTAGEM DE DEPOIMENTOS
    return self::getTestimonies($request);
  }
};
