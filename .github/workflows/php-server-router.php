<?php 
declare(strict_types=1);

// phpcs:ignoreFile

/**
 * Router for the PHP built-in webserver, started with `pub/` as document root
 *
 * Without a router, the built-in webserver returns a 404 for every URL with a file extension (like a product URL
 * `foo.html`) that does not exist as a file. Magento its own `phpserver/router.php` serves static files itself with
 * a limited list of MIME types (lacking WebP, for instance), so existing files are left to the webserver instead.
 */

$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$path = (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path !== '/' && is_file($documentRoot . $path)) {
    return false;
}

if (str_starts_with($path, '/static/')) {
    $_GET['resource'] = (string)preg_replace('#^/static/(version\d+/)?#', '', $path);
    require $documentRoot . '/static.php';
    return true;
}

if (str_starts_with($path, '/media/')) {
    require $documentRoot . '/get.php';
    return true;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $documentRoot . '/index.php';
require $documentRoot . '/index.php';
