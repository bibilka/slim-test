<?php

/**
 * Скрипт генерирует случайный секретный ключ и устанавливает его в параметр SECRET_KEY в конфигурационный файл .env.
 */

if (!file_exists('.env.example')) {
    die('Couldnt find env files :C' . PHP_EOL);
}

if (!file_exists('.env')) {
    exec('cp .env.example .env');
}

$f = fopen(".env", "r+");

$content = file_get_contents(".env");
$specificLine = "SECRET_KEY=";

$exists = false;

while (($line = fgets($f)) !== false) {
    if (strpos($line, $specificLine) !== false) {
        $exists = true;
        break;
    }
}

$key = base64_encode(random_bytes(32));

if ($exists) {
    $replace = 'SECRET_KEY=' . $key;
    $content = preg_replace('/SECRET_KEY=\S*/', $replace, $content);
    file_put_contents('.env', $content);
} else {
    file_put_contents('.env', "\nSECRET_KEY=$key\n", FILE_APPEND | LOCK_EX);
}

fclose($f);

echo 'SECRET KEY generated!' . PHP_EOL;