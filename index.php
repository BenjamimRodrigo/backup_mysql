<?php
require __DIR__ . '/vendor/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;

try {
  $dotenv = new Dotenv\Dotenv(__DIR__);
  $dotenv->load();  
} catch (Exception $e) {
  echo "Por favor, verifique o arquivo .env!\n";
  die();
}

if (php_sapi_name() != 'cli') {
  writeLog("Check the Request Come From", false);
  throw new Exception('This application must be run on the command line.');
  writeLog("Check the Request Come From: OK!", false);
}

/**
 * Backup & Compress DB to gzip
 */
function backupDB($dbname) {
  writeLog("=========================================================", false);
  writeLog("Realizando backup >> $dbname...");
  $dumpSettings = array(
    'compress' => 'gzip'
  );
  try {
    $mysqlPath = getenv('BACKUP_SAVE');
    $zipFile = $dbname . '_' . date('Ymdhi') . ".zip";
    $dump = new IMysqldump\Mysqldump('mysql:host='. getenv('MYSQL_HOST') .';dbname=' . $dbname, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'), $dumpSettings);
    $dump->start($mysqlPath . $zipFile);
    writeLog("Backup e compressão finalizados!");
  } catch (\Exception $e) {
    writeLog('mysqldump-php error: ' . $e->getMessage());
    echo 'mysqldump-php error: ' . $e->getMessage();
  }
}

function writeLog($message = "") {
  echo "[" . date('d-m-Y H:i:s') . "] " . $message . "\n";  
}

backupDB('db_name1');   // Informe aqui o nome do banco de dados para fazer Backup
//backupDB('db_name2'); // Aqui você pode informar outro, no mesmo host
//backupDB('db_name3');
// ....

writeLog("=========================================================", false);