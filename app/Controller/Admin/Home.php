<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;

class Home extends Page {

  /**
   * Método responsável por renderizar a view de home do painel admin
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  public static function getHome(Request $request = null) {
    // CONTEÚDO DA HOME
    $content  = View::render('admin\\modules\\home\\index', []);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Home >> {{name}}', $content, 'home');
  }
}
