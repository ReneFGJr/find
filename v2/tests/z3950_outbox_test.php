<?php
namespace Config {
    class Services {
        public static $mode = 'fail';
        public static function curlrequest() { return new self(); }
        public function post($url, $options) {
            if (self::$mode === 'fail') throw new \RuntimeException('API offline');
            return new class($options['json']) {
                private $payload;
                public function __construct($payload) { $this->payload = $payload; }
                public function getStatusCode() { return 202; }
                public function getBody() { return json_encode(['id' => 1, 'isbn' => $this->payload['isbn']]); }
            };
        }
    }
}
namespace {
    define('WRITEPATH', sys_get_temp_dir() . '/find-z3950-test-' . bin2hex(random_bytes(6)) . '/');
    function env($key, $default) { return $default; }
    function log_message($level, $message, $context = []) {}
    require __DIR__ . '/../app/Libraries/Z3950Isbn.php';
    require __DIR__ . '/../app/Libraries/Z3950Outbox.php';
    $check = static function ($condition, $message) { if (!$condition) throw new \RuntimeException($message); };
    try {
        $outbox = new \App\Libraries\Z3950Outbox();
        $outbox->enqueue('1001234567890', 1);
        $check(!is_dir(WRITEPATH), 'Identificador artificial criou pedido');
        $outbox->enqueue('0-306-40615-2', 2);
        $file = WRITEPATH . 'z3950-outbox/2.json';
        $check(is_file($file), 'Falha da API perdeu pedido');
        $check(json_decode(file_get_contents($file), true)['isbn'] === '9780306406157', 'Conversão ISBN');
        \Config\Services::$mode = 'success';
        $check($outbox->retry() === 1, 'Reenvio falhou');
        $check(!file_exists($file), 'Pedido confirmado permaneceu pendente');
        $outbox->enqueue('9780306406157', 3);
        $check(!file_exists(WRITEPATH . 'z3950-outbox/3.json'), 'Envio imediato falhou');
        echo "OK: identificador sem ISBN, envio imediato, persistência e reenvio após falha.\n";
    } finally {
        foreach (glob(WRITEPATH . 'z3950-outbox/*.json') ?: [] as $file) unlink($file);
        if (is_dir(WRITEPATH . 'z3950-outbox')) rmdir(WRITEPATH . 'z3950-outbox');
        if (is_dir(WRITEPATH)) rmdir(WRITEPATH);
    }
}
