<?php

namespace App\Models\Find\Items;

use CodeIgniter\Model;

class Historic extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'itens_historico';
    protected $primaryKey       = 'id_jh';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ih_code',
        'ih_datetime',
        'ih_user',
        'ih_tombo',
        'ih_library',
    ];

    protected ?string $resolvedTable = null;
    protected ?array $resolvedColumns = null;

    private function resolveTable(): ?string
    {
        if ($this->resolvedTable !== null) {
            return $this->resolvedTable;
        }

        $db = $this->db ?? \Config\Database::connect();

        if ($db->tableExists('find_item_historic')) {
            $this->resolvedTable = 'find_item_historic';
            return $this->resolvedTable;
        }

        if ($db->tableExists('itens_historico')) {
            $this->resolvedTable = 'itens_historico';
            return $this->resolvedTable;
        }

        $this->resolvedTable = '';
        return null;
    }

    private function resolveColumns(string $table): array
    {
        if ($this->resolvedColumns !== null) {
            return $this->resolvedColumns;
        }

        $fields = $this->db->getFieldNames($table);
        $this->resolvedColumns = is_array($fields) ? $fields : [];

        return $this->resolvedColumns;
    }

    private function chooseColumn(array $columns, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }

    private function getClientIp(): string
    {
        try {
            $request = service('request');
            if ($request !== null) {
                $ip = trim((string) $request->getIPAddress());
                if ($ip !== '') {
                    return $ip;
                }
            }
        } catch (\Throwable $e) {
            // Fallback para ambiente sem request HTTP.
        }

        $ip = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
        return $ip !== '' ? $ip : '0.0.0.0';
    }

    public function registerMovement(int $code, int $userId, int $tombo, int $libraryId, ?int $itemId = null): bool
    {
        $table = $this->resolveTable();
        if (!$table) {
            return false;
        }

        $columns = $this->resolveColumns($table);

        $payload = [];
        $now = date('Y-m-d H:i:s');
        $clientIp = $this->getClientIp();

        // Mapeamento nominal direto (prioridade alta)
        $map = [
            ['value' => $code, 'candidates' => ['ih_code', 'fih_code', 'code', 'action_code', 'historic_code']],
            ['value' => $now, 'candidates' => ['ih_datetime', 'fih_datetime', 'created_at', 'date', 'datetime']],
            ['value' => $userId, 'candidates' => ['ih_user', 'fih_user', 'user_id', 'id_user']],
            ['value' => $tombo, 'candidates' => ['ih_tombo', 'fih_tombo', 'tombo', 'i_tombo']],
            ['value' => $libraryId, 'candidates' => ['ih_library', 'fih_library', 'library_id', 'id_library', 'library']],
            ['value' => $itemId, 'candidates' => ['fih_item', 'ih_item', 'item_id', 'id_item', 'i_item']],
            ['value' => $clientIp, 'candidates' => ['h_ip', 'fih_ip', 'ih_ip', 'ip', 'client_ip']],
        ];

        foreach ($map as $entry) {
            if ($entry['value'] === null) {
                continue;
            }
            $col = $this->chooseColumn($columns, $entry['candidates']);
            if ($col !== null) {
                $payload[$col] = $entry['value'];
            }
        }

        // Mapeamento por heurística para schemas desconhecidos.
        foreach ($columns as $col) {
            if (array_key_exists($col, $payload)) {
                continue;
            }

            $colL = strtolower($col);

            if ((str_contains($colL, 'date') || str_contains($colL, 'time') || str_ends_with($colL, '_dt')) && !str_contains($colL, 'update')) {
                $payload[$col] = $now;
                continue;
            }
            if (str_contains($colL, 'user')) {
                $payload[$col] = $userId;
                continue;
            }
            if (str_contains($colL, 'tombo')) {
                $payload[$col] = $tombo;
                continue;
            }
            if (str_contains($colL, 'library')) {
                $payload[$col] = $libraryId;
                continue;
            }
            if ((str_contains($colL, '_ip') || $colL === 'ip') && $clientIp !== '') {
                $payload[$col] = $clientIp;
                continue;
            }
            if ((str_contains($colL, 'item') || str_contains($colL, 'exemplar')) && $itemId !== null) {
                $payload[$col] = $itemId;
                continue;
            }
            if (str_contains($colL, 'code') || str_contains($colL, 'status') || str_contains($colL, 'action')) {
                $payload[$col] = $code;
                continue;
            }
        }

        if (empty($payload)) {
            return false;
        }

        try {
            return (bool) $this->db->table($table)->insert($payload);
        } catch (\Throwable $e) {
            log_message('error', 'Historic::registerMovement failed on table {table}: {msg}', [
                'table' => $table,
                'msg' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
