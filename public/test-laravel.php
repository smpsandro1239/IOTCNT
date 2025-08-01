<?php
try {
  echo "1. Iniciando teste Laravel...<br>";

  require_once __DIR__ . '/../vendor/autoload.php';
  echo "2. Autoloader carregado com sucesso<br>";

  $app = require_once __DIR__ . '/../bootstrap/app.php';
  echo "3. Bootstrap carregado com sucesso<br>";

  echo "4. Laravel funcionando!<br>";
  echo "5. Versão PHP: " . phpversion() . "<br>";
} catch (Exception $e) {
  echo "ERRO: " . $e->getMessage() . "<br>";
  echo "Arquivo: " . $e->getFile() . "<br>";
  echo "Linha: " . $e->getLine() . "<br>";
}
