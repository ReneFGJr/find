<?php
namespace CodeIgniter { class Model {} }
namespace {
    require __DIR__ . '/../app/Models/Z3950/Index.php';
    class Z3950ErrorTestClient extends \App\Models\Z3950\Index {
        public $result;
        public function api_request(string $method, string $url, array $data = [], array $headers = [], int $timeout = 15): array {
            if ($this->result instanceof \Throwable) throw $this->result;
            return $this->result;
        }
    }
    $client = new Z3950ErrorTestClient();
    $cases = [
        [new \RuntimeException('Erro cURL (28): timeout'), 'timeout'],
        [['status' => 503, 'json' => ['message' => 'Servidor indisponível'], 'body' => ''], 'HTTP 503: Servidor indisponível'],
        [['status' => 200, 'json' => null, 'body' => '<html>erro</html>'], 'JSON inválida'],
        [['status' => 200, 'json' => ['success' => false, 'message' => 'Extensão YAZ ausente'], 'body' => ''], 'Extensão YAZ ausente'],
    ];
    foreach ($cases as [$client->result, $expected]) {
        try { $client->searchISBN('9780306406157', true); throw new \LogicException('Erro ignorado'); }
        catch (\RuntimeException $e) { if (!str_contains($e->getMessage(), $expected)) throw $e; }
        if ($client->searchISBN('9780306406157') !== []) throw new \LogicException('Compatibilidade alterada');
    }
    $payload = ['data' => [['literal' => 'Título']]];
    $client->result = ['status' => 200, 'json' => $payload, 'body' => ''];
    if ($client->searchISBN('9780306406157', true) !== $payload) throw new \LogicException('Sucesso alterado');
    function view($name, $params = []) { return ''; }
    function esc($value, $context = '') { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
    function base_url($path) { return 'http://find/' . $path; }
    $isbn = '9780306406157';
    $message = '<script>alert(1)</script> Erro da API';
    ob_start();
    require __DIR__ . '/../app/Views/catalog/z3950_error.php';
    $html = ob_get_clean();
    if (str_contains($html, '<script>') || !str_contains($html, '&lt;script&gt;') || !str_contains($html, 'Tentar novamente')) throw new \LogicException('Exibição insegura ou incompleta');
    echo "OK: timeout, HTTP, JSON inválido, erro da API, sucesso e exibição segura da mensagem.\n";
}
