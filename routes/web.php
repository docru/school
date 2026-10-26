<?php

declare(strict_types=1);

// Маршруты подключаются по хосту запроса. В HOST_SCHOOL / HOST_HOOKS можно перечислить
// несколько хостов через запятую (например, localhost:8095 и IP компьютера для телефона в локальной сети).
$host = request()->getHttpHost();
$hosts = static fn (string $key): array => array_filter(array_map('trim', explode(',', (string) env($key, ''))));

if (in_array($host, $hosts('HOST_SCHOOL'), true)) {
    require 'web_school.php';
} elseif (in_array($host, $hosts('HOST_HOOKS'), true)) {
    require 'web_hooks.php';
}
