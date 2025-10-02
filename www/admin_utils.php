<?php
function get_allowed_commands(): array {
    return [
        'ls' => ['cmd' => 'ls -la /var/www/html', 'label' => 'ls (Список файлов веб-каталога)'],
        'ps' => ['cmd' => 'ps aux', 'label' => 'ps (Процессы сервера)'],
        'whoami' => ['cmd' => 'whoami', 'label' => 'whoami (Текущий пользователь)'],
        'id' => ['cmd' => 'id', 'label' => 'id (Информация о пользователе)'],
        'uptime' => ['cmd' => 'uptime', 'label' => 'uptime (Время работы сервера)'],
        'df' => ['cmd' => 'df -h', 'label' => 'df (Свободное место на дисках)'],
        'free' => ['cmd' => 'free -m', 'label' => 'free (Память: использование и свободная)'],
        'uname' => ['cmd' => 'uname -a', 'label' => 'uname (Информация о системе)'],
        'netstat' => ['cmd' => 'netstat -tuln', 'label' => 'netstat (Активные сетевые соединения)'],
        'top' => ['cmd' => 'top -b -n1 | head -20', 'label' => 'top (Топ 20 процессов по нагрузке)'],
    ];
}

function run_allowed_command(string $key): string {
    $allowed = get_allowed_commands();
    if (!array_key_exists($key, $allowed)) return 'Команда не разрешена';
    $cmd = $allowed[$key]['cmd'];
    $out = shell_exec($cmd . ' 2>&1');
    return $out === null ? 'Нет вывода' : $out;
}
