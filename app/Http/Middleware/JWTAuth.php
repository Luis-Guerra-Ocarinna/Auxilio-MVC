<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use Closure;
use Firebase\JWT\JWT;

class JWTAuth {

  /**
   * Método responsável por retornar uma instância de usuário autenticado
   *
   * @param   Request     $request
   * 
   * @return  User|boolean  
   */
  private function getJWTAuthUser(Request $request) {
    // HEADERS
    $headers = $request->getHeader();

    // TOKEN PURO EM JWT
    $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

    try {
      // DECODE
      $decode = (array) JWT::decode($jwt, getenv('JWT_KEY'), ['HS256']);
    } catch (\Exception $e) {
      throw new \Exception("Token inválido", 403);
    }

    // E-MAIL
    $email = $decode['email'];

    // BUSCA USUÁRIO POR E-MAIL
    $obUser = EntityUser::getUserByEmail($email);

    // RETORNA O USUÁRIO
    return ($obUser instanceof EntityUser) ? $obUser : false;
  }

  /**
   * Método responsável por validar o acesso via JWT
   *
   * @param   Request    $request
   * 
   * @return  boolean|void 
   */
  private function auth(Request $request) {
    // VERIFICA O USUÁRIO RECEBIDO
    if ($obUser = $this->getJWTAuthUser($request)) {
      $request->user = $obUser;
      return true;
    }

    // EMITE O ERRO TOKEN INVÁLIDO
    throw new \Exception("Acesso negado", 403);
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
    // REALIZA A VALIDAÇÃO DO ACESSO VIA JWT
    $this->auth($request);

    // EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
    return $next($request);
  }
}
