<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class User {

  /** @var integer ID do usuário */
  public $id;

  /** @var string Nome do usuário */
  public $nome;

  /** @var string Email do usuário */
  public $email;

  /** @var string Senha do usuário */
  public $senha;

  /**
   * Método reponsável por retornar um usuário com base em seu e-mail
   *
   * @param   string  $email  
   *
   * @return  User          
   */
  public static function getUserByEmail(string $email) {
    return (new Database('usuarios'))->select('email = "' . $email . '"')->fetchObject(self::class);
  }
}
