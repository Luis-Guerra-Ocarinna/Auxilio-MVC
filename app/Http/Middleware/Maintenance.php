<?php

namespace App\Http\Middleware;

class Maintenance {

  /**
   * Método responsável por executar o middleware
   *
   * @param   \App\Http\Request  $request  
   * @param   \Closure           $next     
   *
   * @return  App\Http\Response           
   */
  public function handle(\App\Http\Request $request, \Closure $next) {
    // VERIFICA O ESTADO DE MANUTENÇÃO DA PÁGINA
    if (getenv('MAINTENANCE') == 'true') throw new \Exception("Página em manutenção. Tente mais tarde.", 503);
    
    // EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
    return $next($request);
  }
}
