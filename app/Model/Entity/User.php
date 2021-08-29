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
    return self::getUsers('email = ' . "'$email'")->fetchObject(self::class);
  }

  /**
   * Método responsável por retornar um usuário combase no seu id
   *
   * @param   integer  $id  
   *
   * @return  User    
   */
  public static function getUserById(int $id) {
    return self::getUsers('id = ' . $id)->fetchObject(self::class);
  }

  /**
   * Método responsável por cadastrar a intância atual no banco de dados
   *
   * @return  boolean
   */
  public function cadastrar() {
    // INSERE O USUÁRIO NO BANCO DE DADOS
    $this->id = (new Database('usuarios'))->insert([
      'nome'  => $this->nome,
      'email' => $this->email,
      'senha' => $this->senha
    ]);

    // SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar os dados no banco
   *
   * @return  boolean
   */
  public function atualizar() {
    // ATUALIZA O USUÁRIO NO BANCO DE DADOS
    return (new Database('usuarios'))->update('id = ' . $this->id, [
      'nome'  => $this->nome,
      'email' => $this->email,
      'senha' => $this->senha
    ]);
  }

  /**
   * Método responsável por excluir um usuário do banco
   *
   * @return  boolean
   */
  public function excluir() {
    // EXCLUI O USUÁRIO DO BANCO DE DADOS
    return (new Database('usuarios'))->delete('id = ' . $this->id);
  }

  /**
   * Método reponsável por retornar Usuários
   *
   * @param   string  $where
   * @param   string  $order
   * @param   string  $limit
   * @param   string  $fields
   *
   * @return  PDOStatement
   */
  public static function getUsers(string $where = null, string $order = null, string $limit = null, string $fields = '*') {
    return (new Database('usuarios'))->select($where, $order, $limit, $fields);
  }
}
