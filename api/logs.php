<?php
session_start();

require_once 'tools.php';

ini_set('display_errors', 0);

header('Content-Type: application/json');

tools::checkPermission('p_log');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':
        get_logs();
        break;
    default:
        http_response_code(405);
        break;
}

function get_logs() : void
{
    $maxLines = 200;
    if (isset($_GET['lines']) && is_numeric($_GET['lines'])) {
        $maxLines = (int)$_GET['lines'];
        $maxLines = max(10, min(1000, $maxLines));
    }

    $candidates = list_candidate_log_paths();
    $readableLogs = get_readable_logs($candidates);

    if (count($readableLogs) === 0) {
        echo json_encode(
            [
                'logs' => build_no_log_message($candidates),
                'sources' => []
            ],
            JSON_INVALID_UTF8_SUBSTITUTE
        );
        return;
    }

    $sections = [];
    foreach ($readableLogs as $entry) {
        $content = read_last_lines($entry['path'], $maxLines);
        if ($content === '') {
            continue;
        }

        $timestamp = @filemtime($entry['path']);
        $updatedAt = $timestamp !== false ? date('Y-m-d H:i:s', $timestamp) : 'unknown';
        $sections[] = '===== ' . $entry['label'] . " =====\n"
            . 'File: ' . $entry['path'] . "\n"
            . 'Last update: ' . $updatedAt . "\n\n"
            . $content;
    }

    if (count($sections) === 0) {
        echo json_encode(
            [
                'logs' => 'Log files were found but could not be read.',
                'sources' => []
            ],
            JSON_INVALID_UTF8_SUBSTITUTE
        );
        return;
    }

    echo json_encode(
        [
            'logs' => implode("\n\n", $sections),
            'sources' => array_values(array_map(static function ($entry) {
                return $entry['path'];
            }, $readableLogs))
        ],
        JSON_INVALID_UTF8_SUBSTITUTE
    );
}

function append_candidate(array &$candidates, string $label, string $path): void
{
    $cleanPath = trim($path, " \t\n\r\0\x0B\"'");
    if ($cleanPath === '' || strtolower($cleanPath) === 'syslog') {
        return;
    }

    $realPath = @realpath($cleanPath);
    if ($realPath !== false) {
        $cleanPath = $realPath;
    }

    $key = strtolower(str_replace('\\', '/', $cleanPath));
    if (isset($candidates[$key])) {
        return;
    }

    $candidates[$key] = [
        'label' => $label,
        'path' => $cleanPath
    ];
}

function append_xampp_candidates(array &$candidates, string $xamppRoot): void
{
    $root = rtrim($xamppRoot, '/\\');
    if ($root === '') {
        return;
    }

    append_candidate($candidates, 'Apache error log', $root . DIRECTORY_SEPARATOR . 'apache' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log');
    append_candidate($candidates, 'Apache access log', $root . DIRECTORY_SEPARATOR . 'apache' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'access.log');
    append_candidate($candidates, 'PHP error log', $root . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'php_error_log');
    append_candidate($candidates, 'MySQL error log', $root . DIRECTORY_SEPARATOR . 'mysql' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mysql_error.log');
    append_candidate($candidates, 'MySQL daemon log', $root . DIRECTORY_SEPARATOR . 'mysql' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mysqld.log');
}

function list_candidate_log_paths(): array
{
    $candidates = [];

    $phpErrorLog = (string)ini_get('error_log');
    append_candidate($candidates, 'PHP ini error_log', $phpErrorLog);

    $projectRoot = @realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
    if ($projectRoot !== false) {
        append_candidate($candidates, 'Project php_error_log', $projectRoot . DIRECTORY_SEPARATOR . 'php_error_log');
    }

    $xamppRoots = [];

    $fromProject = $projectRoot !== false
        ? @realpath($projectRoot . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..')
        : false;
    if ($fromProject !== false) {
        $xamppRoots[] = $fromProject;
    }

    $fromPhpBinary = @realpath(dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . '..');
    if ($fromPhpBinary !== false) {
        $xamppRoots[] = $fromPhpBinary;
    }

    $envXampp = getenv('XAMPP_ROOT');
    if (is_string($envXampp) && $envXampp !== '') {
        $xamppRoots[] = $envXampp;
    }

    foreach (array_unique($xamppRoots) as $root) {
        append_xampp_candidates($candidates, $root);
    }

    append_candidate($candidates, 'Apache error log (Debian/Ubuntu)', '/var/log/apache2/error.log');
    append_candidate($candidates, 'Apache access log (Debian/Ubuntu)', '/var/log/apache2/access.log');
    append_candidate($candidates, 'Apache error log (RHEL/CentOS)', '/var/log/httpd/error_log');
    append_candidate($candidates, 'Apache access log (RHEL/CentOS)', '/var/log/httpd/access_log');
    append_candidate($candidates, 'Nginx error log', '/var/log/nginx/error.log');
    append_candidate($candidates, 'PHP-FPM error log', '/var/log/php-fpm/error.log');
    append_candidate($candidates, 'PHP-FPM 8.3 log', '/var/log/php8.3-fpm.log');
    append_candidate($candidates, 'PHP-FPM 8.2 log', '/var/log/php8.2-fpm.log');
    append_candidate($candidates, 'PHP-FPM 8.1 log', '/var/log/php8.1-fpm.log');
    append_candidate($candidates, 'MySQL error log', '/var/log/mysql/error.log');
    append_candidate($candidates, 'MySQL log', '/var/log/mysql/mysql.log');
    append_candidate($candidates, 'MySQL daemon log', '/var/log/mysqld.log');
    append_candidate($candidates, 'XAMPP Linux Apache log', '/opt/lampp/logs/error_log');
    append_candidate($candidates, 'XAMPP Linux PHP log', '/opt/lampp/logs/php_error_log');

    foreach (glob('/opt/lampp/var/mysql/*.err') ?: [] as $path) {
        append_candidate($candidates, 'XAMPP Linux MySQL err', $path);
    }

    foreach (glob('/var/log/mysql/*.err') ?: [] as $path) {
        append_candidate($candidates, 'MySQL err', $path);
    }

    return array_values($candidates);
}

function get_readable_logs(array $candidates): array
{
    $readable = [];

    foreach ($candidates as $entry) {
        $path = $entry['path'];
        if (!is_file($path) || !is_readable($path)) {
            continue;
        }

        $readable[] = $entry;
    }

    usort($readable, static function (array $left, array $right): int {
        $leftTime = @filemtime($left['path']);
        $rightTime = @filemtime($right['path']);
        $leftTime = $leftTime !== false ? $leftTime : 0;
        $rightTime = $rightTime !== false ? $rightTime : 0;
        return $rightTime <=> $leftTime;
    });

    return array_slice($readable, 0, 4);
}

function read_last_lines(string $path, int $maxLines): string
{
    $maxBytes = 512 * 1024;

    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        return '';
    }

    $size = @filesize($path);
    if ($size === false) {
        fclose($handle);
        return '';
    }

    $offset = max(0, $size - $maxBytes);
    if (@fseek($handle, $offset, SEEK_SET) !== 0) {
        fclose($handle);
        return '';
    }

    $length = $size - $offset;
    $buffer = $length > 0 ? @fread($handle, $length) : '';
    fclose($handle);

    if (!is_string($buffer) || $buffer === '') {
        return '';
    }

    $buffer = str_replace("\0", '', $buffer);
    $lines = preg_split("/\r\n|\r|\n/", $buffer);
    if (!is_array($lines) || count($lines) === 0) {
        return '';
    }

    if ($offset > 0) {
        array_shift($lines);
    }

    $lines = array_filter($lines, static function ($line) {
        return $line !== '';
    });
    $lines = array_slice(array_values($lines), -$maxLines);

    return implode("\n", $lines);
}

function build_no_log_message(array $candidates): string
{
    $lines = [
        'No readable log file was found.',
        'Checked paths:'
    ];

    foreach (array_slice($candidates, 0, 20) as $entry) {
        $status = 'missing';
        if (is_file($entry['path'])) {
            $status = is_readable($entry['path']) ? 'readable' : 'permission denied';
        }

        $lines[] = '- [' . $entry['label'] . '] ' . $entry['path'] . ' (' . $status . ')';
    }

    return implode("\n", $lines);
}
