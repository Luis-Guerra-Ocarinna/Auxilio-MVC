<?php

namespace App\Controller\Pages;

use App\Model\Entity\Organization;
use \App\Utils\View;

class Page {

  /**
   * Método responsável por renderizar o topo da página
   *
   * @return  string
   */
  private static function getHeader() {
    return View::render('pages/header');
  }

  /**
   * Método responsável por renderizar o rodapé da página
   *
   * @return  string
   */
  private static function getFooter() {
    return View::render('pages/footer');
  }

  /**
   * Método responsável por retornar um link da paginação
   *
   * @param   array   $queryParams  
   * @param   array   $page         
   * @param   string  $url          
   * @param   string  $label          
   *
   * @return  string                
   */
  private static function getPaginationLink($queryParams, $page, $url, $label = null) {
    // ALTERA PÁGINA
    $queryParams['page'] = $page['page'];

    // LINK
    $link = $url . '?' . http_build_query($queryParams);

    // VIEW
    return View::render('\\pages\\pagination\\link', [
      'page'   => $label ?? $page['page'],
      'link'   => $link,
      'active' => $page['current'] ? 'active' : ''
    ]);
  }

  /**
   * Método responsável por renderizar o layout de paginação
   *
   * @param   Request  $request
   * @param   Pagination  $obgPagination
   *
   * @return  string
   */
  public static function getPagination($request, $obgPagination) {
    // PÁGINAS
    $pages = $obgPagination->getPages();

    // VERIFICA A QUANTIDADE DE PÁGINAS
    if (count($pages) <= 1) return '';

    // LINKS
    $links = '';

    // URL ATUAL (SEM QUERYS)
    $url =  $request->getRouter()->getCurrentUrl();

    // QUERY
    $queryParams = $request->getQueryParams();

    // PÁGINA ATUAL
    $currentPage = $queryParams['page'] ?? 1;

    // LIMITE DE PÁGINAS
    $limit = getenv('PAGINATION_LIMIT');

    // MEIO DA PAGINAÇÃO
    $middle = ceil($limit / 2);

    // INICIO DA PAGINAÇÃO
    $start = $middle > $currentPage ? 0 : $currentPage - $middle;

    // AJUSTA O FINAL DA PAGINAÇÃO
    $end = $limit + $start;

    // AJUSRA O INÍCIO DA PAGINAÇÃO
    if ($end > count($pages)) {
      $diff = $end - count($pages);
      $start -= $diff;
    }

    // LINK INICIAL
    if ($start > 0) {
      $links .= self::getPaginationLink($queryParams, reset($pages), $url, '<<');
    }

    // RENDERIZA OS LINKS
    foreach ($pages as $page) {
      // VERIFICA O START DA PAGINAÇÃO
      if ($page['page'] <= $start) continue;

      // VERIFICA O END DA PAGINAÇÃO
      if ($page['page'] > $end) {
        $links .= self::getPaginationLink($queryParams, end($pages), $url, '>>');
        break;
      }

      $links .= self::getPaginationLink($queryParams, $page, $url);
    }

    // RENDERIZA BOX DE PAGINAÇÃO
    return  View::render('\\pages\\pagination\\box', [
      'links' => $links
    ]);
  }

  /**
   * Método responsável por retornar o conteúdo (view) da nossa página genárica
   *
   * @param   string  $title
   * @param   string  $content
   *
   * @return  string
   */
  public static function getPage($title, $content) {
    // ORGANIZAÇÃO
    $obOrganization = new Organization;

    return View::render('pages\\page', [
      'title'   => $title,
      'header'  => self::getHeader(),
      'content' => $content,
      'footer'  => self::getFooter(),
      'name'    => $obOrganization->name
    ]);
  }
};
