<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\User;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Utils\View;

class Login extends Page {

  /**
   * Método responsável por retornar a renderização da página
   *
   * @param   Request  $request  
   * @param   string   $errorMessage
   * 
   * @return  string             
   */
  public static function getLogin(Request $request, ?string $errorMessage = null) {
    // STATUS
    $status = !is_null($errorMessage) ? View::render('admin\\login\\status', [
      'mensagem' => $errorMessage
    ]) : '';

    // CONTÚDO DA PÁGINA DE LOGIN
    $content = View::render('admin\\login', [
      'status' => $status
    ]);

    // RETORNA A PÁGINA COMPLETA
    return parent::getPage('Login > HELP', $content);
  }

  /**
   * Método responsável por definir o login do usuário
   *
   * @param   Request  $request              
   */
  public static function setLogin(Request $request) {
    // POST VARS
    $postVars = $request->getPostVars();
    $email    = $postVars['email'] ?? '';
    $senha    = $postVars['senha'] ?? '';

    // BUSCA USUÁRIO PELO E-MAIL
    $obUser = User::getUserByEmail($email);
    if (!$obUser instanceof User) return self::getLogin($request, 'E-mail ou senha inválidos');

    // VERIFICA A SENHA DO USUÁRIO
    if (!password_verify($senha, $obUser->senha)) return self::getLogin($request, 'E-mail ou senha inválidos');

    # Poderia ser:
    // if ((!$obUser instanceof User) || (!password_verify($senha, $obUser->senha))) return self::getLogin($request, 'E-mail ou senha inválidos');

    // CRIAR A SESSÃO DE LOGIN
    SessionAdminLogin::login($obUser);

    // REDIRECIONA O USUÁRIO PARA A HOME DO ADMIN
    $request->getRouter()->redirect('/admin');
  }

  /**
   * Método reponsável por deslogar o usuário
   *
   * @param   Request  $request  
   */
  public static function setLogout(Request $request) {
    // DESTRÓI A SESSÃO DE LOGIN
    SessionAdminLogin::logout();

    // REDIRECIONA O USUÁRIO PARA A TELA DE LOGIN
    $request->getRouter()->redirect('/admin/login');
  }
}
