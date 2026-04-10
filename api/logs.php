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

    $result = resolve_log_content($maxLines);
    if ($result === null) {
        echo json_encode([
            'logs' => 'Aucun fichier de log lisible trouve (XAMPP/Linux).',
            'source' => null
        ], JSON_INVALID_UTF8_SUBSTITUTE);
        return;
    }

    echo json_encode([
        'logs' => $result['logs'],
        'source' => $result['source']
    ], JSON_INVALID_UTF8_SUBSTITUTE);
}

function resolve_log_content(int $maxLines): ?array
{
    $firstEmptyFile = null;

    foreach (get_candidate_paths() as $path) {
        if (!is_file($path)) {
            continue;
        }

        $logs = read_last_lines_simple($path, $maxLines);
        if ($logs === '') {
            if ($firstEmptyFile === null) {
                $firstEmptyFile = $path;
            }
            continue;
        }

        return [
            'logs' => $logs,
            'source' => $path
        ];
    }

    if ($firstEmptyFile !== null) {
        return [
            'logs' => '',
            'source' => $firstEmptyFile
        ];
    }

    return null;
}

function get_candidate_paths(): array
{
    $projectRoot = @realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
    if ($projectRoot === false) {
        $projectRoot = dirname(__DIR__);
    }

    $paths = [
        $projectRoot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'php_error.log',
        (string)ini_get('error_log'),
        $projectRoot . DIRECTORY_SEPARATOR . 'php_error_log',
        'C:/xampp/apache/logs/error.log',
        'C:/xampp/apache/logs/access.log',
        'C:/xampp/php/logs/php_error_log'
    ];

    $finalPaths = [];
    $seen = [];

    foreach ($paths as $path) {
        $path = trim((string)$path, " \t\n\r\0\x0B\"'");
        if ($path === '' || strtolower($path) === 'syslog') {
            continue;
        }

        if (!is_absolute_path($path)) {
            $path = $projectRoot . DIRECTORY_SEPARATOR . $path;
        }

        $realPath = @realpath($path);
        if ($realPath !== false) {
            $path = $realPath;
        }

        $key = strtolower(str_replace('\\', '/', $path));
        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $finalPaths[] = $path;
    }

    return $finalPaths;
}

function is_absolute_path(string $path): bool
{
    return str_starts_with($path, '/')
        || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1
        || str_starts_with($path, '\\\\');
}

function read_last_lines_simple(string $path, int $maxLines): string
{
    $lines = @file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return '';
    }

    $lastLines = array_slice($lines, -$maxLines);
    return implode("\n", $lastLines);
}
