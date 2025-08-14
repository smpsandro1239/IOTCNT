<?php
try {
  echo "1. Iniciando teste Laravel...<br>";

  // Carregar autoload
  require_once __DIR__ . '/../vendor/autoload.php';
  echo "2. Autoload carregado com sucesso<br>";

  // Carregar aplicação
  $app = require_once __DIR__ . '/../bootstrap/app.php';
  echo "3. Bootstrap carregado com sucesso<br>";

  // Testar kernel
  $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
  echo "4. Kernel criado com sucesso<br>";

  echo "5. Laravel está funcional!<br>";
  echo "IOTCNT Sistema Online - Laravel OK<br>";
} catch (Exception $e) {
  echo "ERRO: " . $e->getMessage() . "<br>";
  echo "Ficheiro: " . $e->getFile() . "<br>";
  echo "Linha: " . $e->getLine() . "<br>";
} catch (Throwable $e) {
  echo "ERRO FATAL: " . $e->getMessage() . "<br>";
  echo "Ficheiro: " . $e->getFile() . "<br>";
  echo "Linha: " . $e->getLine() . "<br>";
}
