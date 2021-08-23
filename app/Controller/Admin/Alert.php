<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Alert {

  /**
   * Método reponsável por retornar a mensagem de erro
   *
   * @param   string  $message  
   *
   * @return  string            
   */
  public static function getError(string $message) {
    return View::render('admin\\alert\\status', [
      'tipo' => 'danger',
      'mensagem' => $message
    ]);
  }

  /**
   * Método reponsável por retornar a mensagem de sucesso
   *
   * @param   string  $message  
   *
   * @return  string            
   */
  public static function getSuccess(string $message) {
    return View::render('admin\\alert\\status', [
      'tipo' => 'success',
      'mensagem' => $message
    ]);
  }
}
