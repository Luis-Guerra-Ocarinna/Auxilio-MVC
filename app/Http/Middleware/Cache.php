<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Utils\Cache\File as CacheFile;
use Closure;

class Cache {

  /**
   * Método responsável por verificar se a request atual pode ser acheada
   *
   * @param   Request $request
   * 
   * @return  boolean  
   */
  private function isCacheable($request) {
    // VALIDA O TEMPO DE CACHE
    if (getenv('CACHE_TIME') <= 0) return false;

    // VALIDA O MÉTODO DA REQUISIÇÃO
    if ($request->getHttpMethod() != 'GET') return false;

    // VALIDA SE O CONTROLE DE CACHE PELO USUÁRIO ESTÁ ATIVO 
    if (getenv('CACHE_USER_CONTROL') == 'true') {
      // VALIDA O HEADER DE CACHE
      $headers = $request->getHeaders();
      if (isset($headers['Cache-Control']) and $headers['Cache-Control'] == 'no-cache') return false;
    }

    // CACHEÁVEL
    return true;
  }

  /**
   * Método responsável por retornar a hash do cache de
   *
   * @param   Request  $request  
   *
   * @return  string             
   */
  private function getHash($request) {
    // URI DA ROTAS
    $uri = $request->getRouter()->getUri();

    // QUERY PARAMS
    $queryParams = $request->getQueryParams();

    // URI PARA O HASH
    $hUri = $uri . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');

    // REMOVE AS BARRAS E RETORNA A HASH
    return rtrim('route-' . preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($hUri, '/')), "-");
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
    // VERIFICA SE A REQUEST ATUAL É CACHEÁVEL
    if (!$this->isCacheable($request)) return $next($request);

    // HASH DO CACHE
    $hash = $this->getHash($request);

    // RETONAR OS DADOS DO CACHE
    return CacheFile::getCache($hash, getenv('CACHE_TIME'), function () use ($request, $next) {
      return $next($request);
    });
  }
}
