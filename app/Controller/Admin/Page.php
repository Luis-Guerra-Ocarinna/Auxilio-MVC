<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\Organization;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Page {

  /** @var array Módulos disponíveis no painel */
  private static $modules = [
    'home' => [
      'label' => 'Home',
      'link' => URL . '/admin'
    ],
    'testimonies' => [
      'label' => 'Depoimentos',
      'link' => URL . '/admin/testimonies'
    ],
    'users' => [
      'label' => 'Usuários',
      'link' => URL . '/admin/users'
    ],
  ];

  /**
   * Método responsável por retornar o conteúdo (View) da estrutura genérica de página painel
   *
   * @param   string  $title    
   * @param   string  $content  
   *
   * @return  string            
   */
  public static function getPage(string $title, string $content) {
    // ORGANIZAÇÃO
    $obOrganization = new Organization;

    return View::render('admin\\page', [
      'title'   => $title,
      'content' => $content,
      'name'    => $obOrganization->name
    ]);
  }

  /**
   * Método responsável por renderizar a view do menu de painel
   *
   * @param   string  $currentModule  
   *
   * @return  string                  
   */
  private static function getMenu(string $currentModule) {
    // LINKS DO MENU
    $links = '';

    // ITERA OS MÓDULOS
    foreach (self::$modules as $hash => $module) {
      $links .= View::render('admin\\menu\\link', [
        'label' => $module['label'],
        'link'  => $module['link'],
        'current' => $hash == $currentModule ? 'text-danger' : '',
      ]);
    }

    // RETORNA A RENDERIZAÇÃO DO MENU
    return View::render('admin\\menu\\box', [
      'links' => $links
    ]);
  }

  /**
   * Método responsável por renderizar a (View) do painel com conteúdos dinâmicos
   *
   * @param   string  $title          
   * @param   string  $content        
   * @param   string  $currentModule  
   *
   * @return  string                  
   */
  public static function getPanel(string $title, string $content, string $currentModule) {
    // RENDERIZA A VIEW DO PAINEL
    $contentPanel = View::render('admin\\panel', [
      'menu' => self::getMenu($currentModule),
      'content' => $content
    ]);

    // RETORNA A PÁGINA RENDERIZADA
    return self::getPage($title, $contentPanel);
  }

  /**
   * Método responsável por renderizar o layout de paginação
   *
   * @param   Request  $request
   * @param   Pagination  $obgPagination
   *
   * @return  string
   */
  public static function getPagination(Request $request, Pagination $obgPagination) {
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

    // RENDERIZA OS LINKS
    foreach ($pages as $page) {
      // ALTERA PÁGINA
      $queryParams['page'] = $page['page'];

      // LINK
      $link = $url . '?' . http_build_query($queryParams);

      // VIEW
      $links .= View::render('\\admin\\pagination\\link', [
        'page'   => $page['page'],
        'link'   => $link,
        'active' => $page['current'] ? 'active' : ''
      ]);
    }

    // RENDERIZA BOX DE PAGINAÇÃO
    return  View::render('\\admin\\pagination\\box', [
      'links' => $links
    ]);
  }
}
