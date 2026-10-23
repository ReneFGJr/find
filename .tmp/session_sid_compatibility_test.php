<?php
$root = $argv[1];
spl_autoload_register(static function ($class) use ($root) {
    if (str_starts_with($class, 'Psr\\Log\\')) {
        $psr = is_dir($root . '/system/ThirdParty/PSR/Log') ? $root . '/system/ThirdParty/PSR/Log/' : $root . '/vendor/psr/log/src/';
        require $psr . substr($class, 8) . '.php';
    }
    if (str_starts_with($class, 'CodeIgniter\\')) {
        $file = $root . '/system/' . str_replace('\\', '/', substr($class, 12)) . '.php';
        if (is_file($file)) require $file;
    }
});
set_error_handler(static function ($severity, $message) { throw new ErrorException($message, 0, $severity); });
$original = ini_get('session.sid_length');
foreach ([
    [CodeIgniter\Session\Session::class, 'configureSidLength', 'sidRegexp'],
    [CodeIgniter\Session\Handlers\FileHandler::class, 'configureSessionIDRegex', 'sessionIDRegex'],
] as [$class, $method, $property]) {
    $object = (new ReflectionClass($class))->newInstanceWithoutConstructor();
    (new ReflectionMethod($class, $method))->invoke($object);
    $regex = (new ReflectionProperty($class, $property))->getValue($object);
    if (!preg_match('/^' . $regex . '$/', session_create_id())) throw new RuntimeException('Regex incompatível: ' . $class);
}
if (PHP_VERSION_ID >= 80400 && ini_get('session.sid_length') !== $original) throw new RuntimeException('sid_length alterado');
echo 'OK PHP ' . PHP_VERSION . ': configuração sem avisos e IDs compatíveis (' . $root . ').' . PHP_EOL;
