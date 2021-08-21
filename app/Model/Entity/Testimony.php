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

    $this->id = (new Database('depoimentos'))->insert([
      'nome'     => $this->nome,
      'mensagem' => $this->mensagem,
      'data'     => $this->data,
    ]);

    // SUCESSO
    return true;
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
  public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*') {
    return (new Database('depoimentos'))->select($where, $order, $limit, $fields);
  }
}
