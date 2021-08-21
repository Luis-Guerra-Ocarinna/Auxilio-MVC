<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;

class Router {

  /** @var string URL completa do projeto (raiz) */
  private $url = '';

  /** @var string Prefixo de todas as rotas */
  private $prefix = '';

  /** @var array Indice de rotas */
  private $routes = [];

  /** @var Request Intância de Request */
  private $request;

  /**
   * Métod responsável por inicar a classe
   *
   * @param  string $url
   */
  public function __construct($url) {
    $this->request = new Request($this);
    $this->url     = $url;
    $this->setPrefix();
  }

  /**
   * Método responsável por definir o prefixo das rotas
   */
  private function setPrefix() {
    // INFORMAÇÕES DA URL ATUAL
    $parseUrl = parse_url($this->url);

    // DEFINE O PREFIXO
    $this->prefix = $parseUrl['path'] ?? '';
  }

  /**
   * Método responsável por adiconar uma rota na classe
   *
   * @param  string $method
   * @param  string $route
   * @param  array  $params
   */
  private function addRoute($method, $route, $params = []) {
    // VALIDAÇÂO DOS PARÂMETROS
    foreach ($params as $key => $value) {
      if ($value instanceof Closure) {
        $params['controller'] = $value;
        unset($params[$key]);
        continue;
      }
    }

    // VARIÁVEIS DA ROTA
    $params['variables'] = [];

    // PADRÃO DE VALIDAÇÃO DAS VARIAVÉIS DAS ROTAS
    $patternVariable = '/{(.*?)}/';
    if (preg_match_all($patternVariable, $route, $matches)) {
      $route =  preg_replace($patternVariable, '(.*?)', $route);
      $params['variables'] = $matches[1];
    }

    // PADRÂO DE VÀLIDAÇÂO DA URL
    $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

    // ADICIONA A ROTA DENTRO DA CLASSE
    $this->routes[$patternRoute][$method] = $params;
  }

  /**
   * Método responsável por definir uma rota de GET
   *
   * @param  string $route
   * @param  array  $params
   */
  public function get($route, $params = []) {
    return $this->addRoute('GET', $route, $params);
  }

  /**
   * Método responsável por definir uma rota de POST
   *
   * @param  string $route
   * @param  array  $params
   */
  public function post($route, $params = []) {
    return $this->addRoute('POST', $route, $params);
  }

  /**
   * Método responsável por definir uma rota de PUT
   *
   * @param  string $route
   * @param  array  $params
   */
  public function put($route, $params = []) {
    return $this->addRoute('PUT', $route, $params);
  }

  /**
   * Método responsável por definir uma rota de DELETE
   *
   * @param  string $route
   * @param  array  $params
   */
  public function delete($route, $params = []) {
    return $this->addRoute('DELETE', $route, $params);
  }

  /**
   * Método responsável por retornar a URI desconsiderando o prefixo
   *
   * @return string
   */
  private function getUri() {
    $uri = $this->request->getUri();

    // FATIA A URI COM PREFIXO
    $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

    // REOTRNA A URI SEM PREFIXO
    return end($xUri);
  }

  /**
   * Método responsável por os dados da rota atual
   *
   * @return array
   */
  private function getRoute() {
    // URI
    $uri = $this->getUri();

    // METHOD
    $httpMethod = $this->request->getHttpMethod();

    // VALIDA AS ROTAS
    foreach ($this->routes as $patternRoute => $methods) {
      // VERIFICA SE A URI BATE O PADRÃO
      if (preg_match($patternRoute, $uri, $matches)) {
        // VERIFICA O MÉTOD
        if (isset($methods[$httpMethod])) { # isset pra evitar WARNING
          // REMOVE A PRIMEIRA POSIÇÃO
          unset($matches[0]);

          // VARIÁVEIS PROCESSADAS 
          $keys = $methods[$httpMethod]['variables'];
          $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
          $methods[$httpMethod]['variables']['request'] = $this->request;

          // RETORNO DOS PARÂMETROS DA ROTA
          return $methods[$httpMethod];
        }

        // MÉTODO NÃO PERMITIDO/DEFINIDO
        throw new Exception("Método não permitido", 405);
      }
    }

    // URL NÃO ENCONTRADA
    throw new Exception("URL não encontrada", 404);
  }

  /**
   * Método responsável por executar a rota atual
   *
   * @return Response
   */
  public function run() {
    try {
      // OBTÉM A ROTA ATUAL
      $route = $this->getRoute();

      // VERIFICA O CONTROLADOR
      if (!isset($route['controller'])) throw new Exception("URL não pôde ser processada", 500);

      // ARGUMENTOS DA FUNÇÃO
      $args = [];

      // REFLECTION
      $reflection = new ReflectionFunction($route['controller']);
      foreach ($reflection->getParameters() as $parameter) {
        $name = $parameter->getName();
        $args[$name] = $route['variables'][$name] ?? '';
      }

      // RETORNAR A EXECUÇÃO DA FUNÇÃO
      return call_user_func_array($route['controller'], $args);
    } catch (Exception $e) {
      return new Response($e->getCode(), $e->getMessage());
    }
  }

  /**
   * Método responsável por retornar a URL atual
   *
   * @return  string
   */
  public function getCurrentUrl() {
    return $this->url . $this->getUri();
  }
}
