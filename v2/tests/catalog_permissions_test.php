<?php
namespace Config {
    class Database {
        public static $db;
        public static function connect() { return self::$db; }
    }
}
namespace {
    // Run the production permission query against an isolated SQL database.
    class PermissionTestDatabase {
        private $pdo;
        private $from;
        private $columns;
        private $join;
        private $conditions = [];
        private $values = [];
        public function __construct($pdo) { $this->pdo = $pdo; }
        public function table($from) { $this->from = $from; $this->conditions = $this->values = []; return $this; }
        public function select($columns) { $this->columns = $columns; return $this; }
        public function join($table, $on, $type) { $this->join = " $type JOIN $table ON $on"; return $this; }
        public function where($condition, ...$values) {
            $this->conditions[] = $condition . ($values ? ' = ?' : '');
            if ($values) $this->values[] = $values[0];
            return $this;
        }
        public function get() {
            $statement = $this->pdo->prepare("SELECT {$this->columns} FROM {$this->from}{$this->join} WHERE " . implode(' AND ', $this->conditions));
            $statement->execute($this->values);
            return new class($statement) {
                private $statement;
                public function __construct($statement) { $this->statement = $statement; }
                public function getResultArray() { return $this->statement->fetchAll(\PDO::FETCH_ASSOC); }
            };
        }
    }
    $userId = 0;
    function session() { return new class { public function get($key) { return $GLOBALS['userId']; } }; }
    require __DIR__ . '/../app/Libraries/CatalogPermissions.php';
    $pdo = new \PDO('sqlite::memory:');
    $pdo->exec('CREATE TABLE users_group (id_gr INTEGER, gr_hash TEXT, gr_name TEXT)');
    $pdo->exec('CREATE TABLE users_group_members (grm_user INTEGER, grm_group INTEGER, grm_library TEXT, grm_status INTEGER)');
    $pdo->exec("INSERT INTO users_group VALUES (1, '#ADM', 'Administrador'), (2, '#CAT', 'Catalogador'), (3, '#USR', 'Leitor')");
    $pdo->exec("INSERT INTO users_group_members VALUES (1, 1, 'A', 1), (2, 2, 'A', 1), (3, 1, 'B', 1), (4, 3, 'A', 1), (5, 2, 'A', 0), (6, 2, 'A', NULL)");
    \Config\Database::$db = new PermissionTestDatabase($pdo);
    $permissions = new \App\Libraries\CatalogPermissions();
    foreach ([[0, 'A', false], [1, 'A', true], [2, 'A', true], [3, 'A', false], [4, 'A', false], [5, 'A', false], [6, 'A', true], [1, 'B', false], [1, '', false]] as [$userId, $library, $expected]) {
        if ($permissions->canCatalog($library) !== $expected) throw new \RuntimeException("Permissão incorreta: usuário $userId, biblioteca $library");
    }
    echo "OK: administrador, catalogador, leitor, anônimo, vínculo inativo e acesso entre bibliotecas.\n";
}
