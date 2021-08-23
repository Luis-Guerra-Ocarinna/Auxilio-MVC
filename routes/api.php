<?php

// INCLUI ROTAS PADRÕES DA API (V1)
require_once(__DIR__ . '\\api\\v1\\default.php');

// INCLUI ROTAS DE AUTENTICAÇÃO DA API
require_once(__DIR__ . '\\api\\v1\\auth.php');

// INCLUI ROTAS DE DEPOIMENTOS
require_once(__DIR__ . '\\api\\v1\\testimonies.php');

// INCLUI ROTAS DE USUÁRIOS
require_once(__DIR__ . '\\api\\v1\\users.php');
