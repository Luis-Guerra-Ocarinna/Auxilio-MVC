<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use Firebase\JWT\JWT;

class Auth extends Api {

  /**
   * Método responsável por gera um token JWT en
   *
   * @param   Request  $request  
   *
   * @return  array             
   */
  public static function generateToken(Request $request) {
    // POST VARS
    $postVars = $request->getPostVars();

    // VALIDA OS CAMPOS OBRIGÁTORIOS
    if (!isset($postVars['email']) || !isset($postVars['senha'])) throw new \Exception("Os campos 'email' e 'senha' são obrigatórios", 400);

    // BUSCA USUÁRIO PELO E-MAIL
    $obUser = EntityUser::getUserByEmail($postVars['email']);

    // VALIDA O E-MAIL
    if (!$obUser instanceof EntityUser) throw new \Exception("E-mail inválido", 400);

    // VALIDA A SENHA
    if (!password_verify($postVars['senha'], $obUser->senha)) throw new \Exception("Senha inválida", 400);

    // PAYLOAD
    $payload = [
      'email' => $postVars['email']
    ];

    // RETORNA O TOKEN GERADO
    return [
      'token' => JWT::encode($payload, getenv('JWT_KEY'))
    ];
  }
}
