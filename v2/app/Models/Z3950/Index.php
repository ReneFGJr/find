<?php

namespace App\Models\Z3950;

use CodeIgniter\Model;
use RuntimeException; // ← importa a built-in

class Index extends Model
{
    protected $table            = 'msg';
    protected $primaryKey       = 'id_msg';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function searchISBN($isbn)
    {
        // https://cip.brapci.inf.br/api/find/getISBN?query=9786589167501
        $url = 'https://cip.brapci.inf.br/api/find/getISBN?query=9786589167501';

        try {
            $res = $this->api_request('GET', $url, ['query' => $isbn]);
            if ($res['status'] === 200 && $res['json']) {
                return $res['json'];
            } else {
                return [];
            }
        } catch (RuntimeException $e) {
            return [];
        }
    }

    function api_request(
        string $method,
        string $url,
        array $data = [],
        array $headers = [],
        int $timeout = 15
    ): array {
        $method = strtoupper($method);
        $ch = curl_init();

        // Se for GET e $data for array, coloca na querystring
        if ($method === 'GET' && is_array($data) && $data) {
            $qs  = http_build_query($data);
            $url .= (strpos($url, '?') !== false ? '&' : '?') . $qs;
            $data = null;
        }

        $responseHeaders = [];
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_USERAGENT      => 'PHP-API-Client/1.0',
            CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $header = trim($header);
                if ($header === '' || strpos($header, ':') === false) return $len;
                [$name, $value] = explode(':', $header, 2);
                $name = strtolower(trim($name));
                $value = trim($value);
                // Suporta múltiplos headers com o mesmo nome
                if (isset($responseHeaders[$name])) {
                    if (is_array($responseHeaders[$name])) $responseHeaders[$name][] = $value;
                    else $responseHeaders[$name] = [$responseHeaders[$name], $value];
                } else {
                    $responseHeaders[$name] = $value;
                }
                return $len;
            },
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        // Monta corpo (para métodos com payload)
        if ($method !== 'GET' && $data !== null) {
            if (is_array($data)) {
                // Se não houver Content-Type explícito ≈ envia JSON
                $hasCT = false;
                foreach ($headers as $h) {
                    if (stripos($h, 'Content-Type:') === 0) {
                        $hasCT = true;
                        break;
                    }
                }
                if (!$hasCT) {
                    $headers[] = 'Content-Type: application/json; charset=utf-8';
                    $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    // Content-Type informado: decide entre JSON ou form-url-encoded
                    $isJson = false;
                    foreach ($headers as $h) {
                        if (stripos($h, 'Content-Type:') === 0 && stripos($h, 'application/json') !== false) {
                            $isJson = true;
                            break;
                        }
                    }
                    $payload = $isJson ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                        : http_build_query($data);
                }
            } else {
                $payload = (string) $data;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $body   = curl_exec($ch);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($errno) {
            throw new RuntimeException("Erro cURL ($errno): $error");
        }

        // Tenta decodificar JSON se Content-Type indicar
        $json = null;
        $ct = (string)($responseHeaders['content-type'] ?? '');
        if ($body !== false && stripos($ct, 'application/json') !== false) {
            $tmp = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) $json = $tmp;
        }

        return [
            'status'  => (int)$status,
            'headers' => $responseHeaders,
            'body'    => $body,
            'json'    => $json,
            'url'     => $url,
        ];
    }
}
