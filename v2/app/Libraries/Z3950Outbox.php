<?php
namespace App\Libraries;

class Z3950Outbox
{
    private function directory(): string
    {
        $directory = WRITEPATH . 'z3950-outbox/';
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new \RuntimeException('Não foi possível criar a caixa de pedidos Z39.50.');
        }
        return $directory;
    }

    public function enqueue(string $identifier, int $itemId): void
    {
        $isbn = Z3950Isbn::normalize($identifier);
        if ($isbn === '') return;
        $path = $this->directory() . $itemId . '.json';
        $payload = json_encode(['isbn' => $isbn, 'item_id' => $itemId], JSON_THROW_ON_ERROR);
        if (file_put_contents($path, $payload, LOCK_EX) === false) {
            throw new \RuntimeException('Não foi possível salvar o pedido Z39.50.');
        }
        $this->send($path);
    }

    private function send(string $path): bool
    {
        $handle = fopen($path, 'r+');
        if (!$handle) return false;
        if (!flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);
            return false;
        }
        try {
            $payload = json_decode(stream_get_contents($handle), true, 512, JSON_THROW_ON_ERROR);
            $url = (string) env('metadata.queueUrl', 'https://cip.brapci.inf.br/api/find/z3950/queue');
            $response = \Config\Services::curlrequest()->post($url, [
                'json' => $payload, 'timeout' => 3, 'connect_timeout' => 2, 'http_errors' => false,
            ]);
            $ack = json_decode($response->getBody(), true);
            $success = $response->getStatusCode() === 202 && isset($ack['id'], $ack['isbn']) && $ack['isbn'] === $payload['isbn'];
            if (!$success) throw new \RuntimeException('API não confirmou o recebimento do pedido.');
        } catch (\Throwable $e) {
            log_message('error', 'Reenvio Z39.50 pendente: {error}', ['error' => $e->getMessage()]);
            $success = false;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
        if ($success) unlink($path);
        return $success;
    }

    public function retry(): int
    {
        $sent = 0;
        foreach (array_slice(glob($this->directory() . '*.json') ?: [], 0, 20) as $path) {
            if ($this->send($path)) $sent++;
        }
        return $sent;
    }
}
