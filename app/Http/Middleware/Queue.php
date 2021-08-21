<?php

namespace App\Http\Middleware;

use App\Http\Request;

class Queue {

  /** @var array Mapeamento de middlewares */
  private static $map = [];

  /** @var array Mapeamento de middlewares que serão carregados em todas as rotas */
  private static $default = [];

  /** @var array Fila de middlewares a serem executados */
  private $middlewares = [];

  /** @var \Closure Função de execução do controlador */
  private $controller;

  /** @var array Argumentos da função do controlador */
  private $controllerArgs = [];

  /**
   * Método responsável por construir a classe de fila de middlewares
   *
   * @param   array     $middlewares     
   * @param   \Closure  $controller      
   * @param   array     $controllerArgs  
   */
  public function __construct(array $middlewares, \Closure $controller, array $controllerArgs) {
    $this->middlewares    = array_merge(self::$default, $middlewares);
    $this->controller     = $controller;
    $this->controllerArgs = $controllerArgs;
  }

  /**
   * Método responsável por definir o mapeamento de middlewares
   *
   * @param   array  $map  
   */
  public static function setMap(array $map) {
    self::$map = $map;
  }

  /**
   * Método responsável por definir o mapeamento de middlewares padrões
   *
   * @param   array  $default  
   */
  public static function setDefault(array $default) {
    self::$default = $default;
  }

  /**
   * Método reponsável por executar o próximo nivel da fila de middlewares
   *
   * @param   Request  $request  
   *
   * @return  Response           
   */
  public function next(Request $request) {;
    // VERIFICA SE A FILA ESTÁ VAZIA
    if (empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

    // MIDDLEWARE
    $middleware = array_shift($this->middlewares);

    // VERIFICA O MAPEAMENTO
    if (!isset(self::$map[$middleware])) throw new \Exception("Problemas ao processar o middleware da requisição", 500);
    
    // NEXT
    $queue = $this;
    $next = function ($request) use($queue){
      return $queue->next($request);
    };
    
    // EXECUTA O MIDDLEWARE
    return (new self::$map[$middleware])->handle($request, $next);
  }
}
