<?php
namespace CodeIgniter { class Model {} }
namespace App\Models\Find\Rdf {
    class RDF_name {
        public static $values = [];
        public function createLiteral($value, $lang) { self::$values[] = [$value, $lang]; return count(self::$values) + 100; }
    }
    class RDF_class {}
    class RDF_data {
        public static $rows = [];
        public static $fail = false;
        public function where(...$args) { return $this; }
        public function first() { return null; }
        public function insert($data) {
            if (self::$fail) return false;
            self::$rows[] = $data;
            return count(self::$rows);
        }
    }
}
namespace {
    function helper($name) {}
    require __DIR__ . '/../app/Models/Find/Rdf/RDF.php';
    $rdf = new \App\Models\Find\Rdf\RDF();
    foreach (['Vocabulário controlado para indexação de obras ficcionais', '27081', '0'] as $text) {
        $id = $rdf->createTextLiteral(17, 1, $text, 'pt_BR');
        $row = \App\Models\Find\Rdf\RDF_data::$rows[$id - 1];
        $value = \App\Models\Find\Rdf\RDF_name::$values[$id - 1];
        if ($value !== [$text, 'pt_BR'] || $row['d_r2'] !== 0 || $row['d_literal'] !== $id + 100 || $row['d_r1'] !== 17 || $row['d_p'] !== 1) throw new \RuntimeException('Gravação incorreta de literal');
    }
    \App\Models\Find\Rdf\RDF_data::$fail = true;
    if ($rdf->createTextLiteral(17, 1, 'Falha', 'pt_BR') !== null) throw new \RuntimeException('Falha de vínculo ignorada');
    echo "OK: TEXT usa rdf_name e d_literal, com d_r2=0, inclusive números; falhas de vínculo são reportadas.\n";
}
