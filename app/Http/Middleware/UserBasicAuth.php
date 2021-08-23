<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use Closure;

class UserBasicAuth {

  /**
   * Método responsável por retornar uma instância de usuário autenticado
   *
   * @return  User|boolean  
   */
  private function getBasicAuthUser() {
    // VERIFICA A EXISTÊNCIA DOS DADOS DE ACESSO
    if (!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW'])) return false;

    // BUSCA USUÁRIO POR E-MAIL
    $obUser = EntityUser::getUserByEmail($_SERVER['PHP_AUTH_USER']);

    // VERIFICA A INSTÂNCIA
    if (!$obUser instanceof EntityUser) return false;

    // VÁLIDA A SENHA E RETORNA O USUÁRIO
    return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
  }

  /**
   * Método responsável por validar o acesso via HTTP BASIC AUTH
   *
   * @param   Request    $request
   * 
   * @return  boolean|void 
   */
  private function basicAuth(Request $request) {
    // VERIFICA O USUÁRIO RECEBIDO
    if ($obUser = $this->getBasicAuthUser()) {
      $request->user = $obUser;
      return true;
    }

    // EMITE O ERRO DE USUÁRIO OU SENHA INVÁLIDOS
    throw new \Exception("Usuário ou senha inválidos", 403);
  }

  /**
   * Método responsável por executar o middleware
   *
   * @param   Request  $request  
   * @param   Closure  $next     
   *
   * @return  Response           
   */
  public function handle(Request $request, Closure $next) {
    // REALIZA A VALIDAÇÃO DO ACESSO VIA BASIC AUTH
    $this->basicAuth($request);

    // EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
    return $next($request);
  }
}
