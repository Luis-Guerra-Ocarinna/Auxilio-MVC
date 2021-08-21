<?php

namespace App\Http;

class Request {

  /** @var Router Intância do Router */
  private $router;

  /** @var string Método HTTP da requisição */
  private $httpMethod;

  /** @var string URI da página */
  private $uri;

  /** @var array Parâmetros da URL ($_GET) */
  private $queryParams = [];

  /** @var array Variáveis recebidas no POST da página ($_POST) */
  private $postVars = [];

  /** @var array Cabeçalho da requisição */
  private $headers = [];

  /**
   * Contrutor da classe
   *
   * @param   Router  $router
   */
  public function __construct($router) {
    $this->router      = $router;
    $this->queryParams = $_GET ?? [];
    $this->postVars    = $_POST ?? [];
    $this->headers     = getallheaders();
    $this->httpMethod  = $_SERVER['REQUEST_METHOD'] ?? '';
    $this->setUri();
  }

  /**
   * Método responsável por definir a URI
   */
  private function setUri() {
    // URI COMPLETA (COM QUERY)
    $this->uri = $_SERVER['REQUEST_URI'] ?? '';

    // REMOVE QUERYS DA URI
    $xUri =  explode('?', $this->uri);

    $this->uri = $xUri[0];
  }

  /**
   * Método responsável por retornar a intância de Router
   *
   * @return Router
   **/
  public function getRouter() {
    return $this->router;
  }

  /**
   * Método responsável por retornar o método HTTP da requisição
   * @return string
   */
  public function getHttpMethod() {
    return $this->httpMethod;
  }

  /**
   * Método responsável por retornar a URI da requisição
   * @return string
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * Método responsável por retornar os parâmetros da URL da requisição
   * @return array
   */
  public function getQueryParams() {
    return $this->queryParams;
  }

  /**
   * Método responsável por retornar as varáveis POST da requisição
   * @return array
   */
  public function getPostVars() {
    return $this->postVars;
  }

  /**
   * Método responsável por retornar os headers da requisição
   * @return array
   */
  public function getHeader() {
    return $this->headers;
  }
}
