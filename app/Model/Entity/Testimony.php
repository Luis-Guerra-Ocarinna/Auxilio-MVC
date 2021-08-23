<?php

namespace App\Model\Entity;

use DateTime;
use DateTimeZone;
use WilliamCosta\DatabaseManager\Database;

class Testimony {

  /** @var integer $id ID do demopoimento */
  public $id;

  /** @var string Nome do usuáro que fez o demopoimento */
  public $nome;

  /** @var string Mensagem do demopoimento */
  public $mensagem;

  /** @var string Data do demopoimento */
  public $data;

  /**
   * Método responsável por cadastrar a intância atual no banco de dados
   *
   * @return  boolean
   */
  public function cadastrar() {
    // DEFINE A DATA
    // $this->data = date('Y-m-d H:i:s');
    $this->data = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');

    // INSERE O DEPOIMENTO NO BANCO DE DADOS
    $this->id = (new Database('depoimentos'))->insert([
      'nome'     => $this->nome,
      'mensagem' => $this->mensagem,
      'data'     => $this->data,
    ]);

    // SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar os dados do banco com a intância atual no banco de dados
   *
   * @return  boolean
   */
  public function atualizar() {
    // ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
    return (new Database('depoimentos'))->update('id = ' . $this->id, [
      'nome'     => $this->nome,
      'mensagem' => $this->mensagem
    ]);
  }

  /**
   * Método responsável por excluir um depoimento do banco
   *
   * @return  boolean
   */
  public function excluir() {
    // EXCLUI O DEPOIMENTO DO BANCO DE DADOS
    return (new Database('depoimentos'))->delete('id = ' . $this->id);
  }

  /**
   * Método responsável por retornar um depoimento combase no seu id
   *
   * @param   integer  $id  
   *
   * @return  Testimony    
   */
  public static function getTestimonyById(int $id) {
    return self::getTestimonies('id = ' . $id)->fetchObject(self::class);
  }

  /**
   * Método reponsável por retornar Depoimentos
   *
   * @param   string  $where
   * @param   string  $order
   * @param   string  $limit
   * @param   string  $fields
   *
   * @return  PDOStatement
   */
  public static function getTestimonies(string $where = null, string $order = null, string $limit = null, string $fields = '*') {
    return (new Database('depoimentos'))->select($where, $order, $limit, $fields);
  }
}
