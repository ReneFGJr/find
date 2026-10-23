<?php
namespace CodeIgniter { class Model {} }
namespace Config {
    class Database {
        public static $pdo;
        public static function connect() { return new self(); }
        public function query($sql, $params) {
            $statement = self::$pdo->prepare($sql);
            $statement->execute($params);
            return new class($statement) {
                private $statement;
                public function __construct($statement) { $this->statement = $statement; }
                public function getResultArray() { return $this->statement->fetchAll(\PDO::FETCH_ASSOC); }
            };
        }
    }
}
namespace App\Controllers { class BaseController { public $request; public $response; } }
namespace App\Models\Language {
    class LanguageModel { public function getAllLanguages() { return [['lg_code' => 'pt_BR']]; } }
}
namespace App\Models\Find\Rdf {
    class RDF {
        public static $saved;
        public static $result = 123;
        public function createTextLiteral(...$params) { self::$saved = $params; return self::$result; }
    }
}
namespace {
    function helper($name) {}
    function get_cookie($name) { return 'WRONG_LIBRARY'; }
    function view($name, $params) { return $params; }
    require __DIR__ . '/../app/Models/Find/Rdf/RDF_form.php';
    require __DIR__ . '/../app/Controllers/Catalog/RDF.php';
    $pdo = new \PDO('sqlite::memory:');
    \Config\Database::$pdo = $pdo;
    $pdo->exec('CREATE TABLE rdf_form_class_2 (id_form INTEGER, form_frbr TEXT, form_group TEXT, form_group_subgroup TEXT, form_order INTEGER, form_property INTEGER, form_range TEXT, form_library TEXT)');
    $pdo->exec('CREATE TABLE rdf_class (id_c INTEGER, c_class TEXT, c_order INTEGER)');
    $pdo->exec('CREATE TABLE rdf_data (id_d INTEGER, d_r1 INTEGER, d_r2 INTEGER, d_p INTEGER, d_literal INTEGER)');
    $pdo->exec('CREATE TABLE rdf_concept (id_cc INTEGER, cc_use INTEGER, cc_pref_term INTEGER)');
    $pdo->exec('CREATE TABLE rdf_name (id_n INTEGER, n_name TEXT, n_lang TEXT)');
    $ranges = ['["132"]', '[ "132" ]', '["TEXT"]', 'TEXT', 'LITERAL', '132'];
    foreach ($ranges as $index => $range) {
        $id = $index + 1;
        $pdo->prepare("INSERT INTO rdf_form_class_2 VALUES (?, 'W', 'TITLE', '', ?, ?, ?, 'A')")->execute([$id, $id, $id, $range]);
        $pdo->exec("INSERT INTO rdf_class VALUES ($id, 'hasTitle$id', $id)");
        $pdo->exec("INSERT INTO rdf_data VALUES ($id, 17, 0, $id, $id)");
        $pdo->prepare("INSERT INTO rdf_name VALUES (?, ?, 'pt_BR')")->execute([$id, 'Vocabulário controlado para indexação de obras ficcionais']);
    }
    $pdo->exec("INSERT INTO rdf_form_class_2 VALUES (7, 'W', 'AUTHORS', '', 7, 7, '[\"Person\"]', 'A'), (8, 'W', '', '', 8, 8, 'TEXT', 'B')");
    $pdo->exec("INSERT INTO rdf_class VALUES (7, 'hasAuthor', 7), (8, 'otherLibrary', 8)");
    $pdo->exec("INSERT INTO rdf_name VALUES (7, 'Autor de teste', 'pt')");
    $pdo->exec('INSERT INTO rdf_concept VALUES (70, 70, 7)');
    $pdo->exec('INSERT INTO rdf_data VALUES (7, 17, 70, 7, 0)');
    $rows = (new \App\Models\Find\Rdf\RDF_form())->getForm('W', 17, 'A');
    if (count($rows) !== 7) throw new \RuntimeException('Filtro de biblioteca incorreto');
    foreach ($rows as $index => $row) {
        if ($index < 6 && ($row['n_type'] !== 'TEXT' || $row['n_name'] !== 'Vocabulário controlado para indexação de obras ficcionais')) throw new \RuntimeException('Texto ausente: ' . $ranges[$index]);
        if ($index === 6 && ($row['n_type'] !== 'CONCEPT' || $row['n_name'] !== 'Autor de teste')) throw new \RuntimeException('Conceito ausente');
    }
    $pdo->exec('UPDATE rdf_data SET d_r2 = 70, d_literal = 0 WHERE id_d = 1');
    $pdo->exec('DELETE FROM rdf_data WHERE id_d = 2');
    $rows = (new \App\Models\Find\Rdf\RDF_form())->getForm('W', 17, 'A');
    foreach (array_slice($rows, 0, 2) as $row) {
        if ($row['n_type'] !== 'TEXT' || $row['n_name'] !== null || (int) $row['id_cc'] !== 0) throw new \RuntimeException('TEXT confundido com conceito');
    }
    $controller = new \App\Controllers\Catalog\RDF();
    $controller->request = new class {
        public $post = ['idC' => '17', 'prop' => '1', 'lang' => 'pt_BR', 'textValue' => '0'];
        public function getMethod() { return 'post'; }
        public function getGet() { return ['idC' => '999']; }
        public function getPost() { return $this->post; }
    };
    $controller->response = new class { public function setBody($body) { return $body; } };
    $result = $controller->rdf_text_add();
    if (\App\Models\Find\Rdf\RDF::$saved !== ['17', '1', '0', 'pt_BR'] || !str_contains($result, 'window.parent.location.reload')) throw new \RuntimeException('Falha de envio POST');
    \App\Models\Find\Rdf\RDF::$result = null;
    $result = $controller->rdf_text_add();
    if (empty($result['error']) || $result['textValue'] !== '0') throw new \RuntimeException('Falha silenciosa');
    echo "OK: leitura de seis formatos TEXT, conceitos, biblioteca do item, prioridade POST, valor zero e falha de gravação.\n";
}
