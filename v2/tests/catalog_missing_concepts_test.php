<?php
namespace Config {
    class Database {
        public static $db;
        public static function connect() { return self::$db; }
    }
}
namespace App\Controllers {
    class BaseController {
        public $request;
        public $response;
    }
}
namespace App\Libraries {
    class CatalogPermissions {
        public static $allowed = true;
        public function canCatalog($library) { return self::$allowed; }
    }
}
namespace App\Models\Find\Items {
    class Index {
        public static $item;
        public static $failUpdate = false;
        public function where($field, $value) { return $this; }
        public function first() { return self::$item; }
        public function update($id, $values) {
            if (self::$failUpdate) return false;
            self::$item = array_replace(self::$item, $values);
            return true;
        }
    }
}
namespace App\Models\Find\Rdf {
    class RDF {
        public static $created = [];
        public function createConcept($class, $name) {
            self::$created[] = [$class, $name];
            return ['id_cc' => 100 + count(self::$created)];
        }
    }
    class RDF_form {
        public function getForm($type, $id, $library) {
            if ($id <= 0) throw new \RuntimeException('Form received a missing concept');
            return $id;
        }
    }
}
namespace {
    function helper($names) {}
    function session() { return new class { public function get($key) { return $key === 'id_us' ? 1 : null; } }; }
    function view($name, $data) { return $data; }
    function log_message($level, $message, $context) {}
    require __DIR__ . '/../app/Controllers/Catalog.php';

    class ConceptTestTransaction {
        public $committed = false;
        public $rolledBack = false;
        public function transBegin() {}
        public function transStatus() { return true; }
        public function transCommit() { $this->committed = true; return true; }
        public function transRollback() { $this->rolledBack = true; }
    }
    function check($condition, $message) {
        if (!$condition) throw new \RuntimeException($message);
    }
    $controller = new \App\Controllers\Catalog();
    $controller->request = new class { public function getGet($key) { return 27155; } };
    $controller->response = new class {
        public $status;
        public function setStatusCode($status) { $this->status = $status; return $this; }
        public function setBody($body) { return $this; }
    };
    $fields = ['i_work', 'i_expression', 'i_manifestation'];
    for ($mask = 0; $mask < 8; $mask++) {
        $original = ['id_i' => 27155, 'i_library' => 'A'];
        foreach ($fields as $bit => $field) $original[$field] = ($mask & (1 << $bit)) ? 0 : 10 + $bit;
        \App\Models\Find\Items\Index::$item = $original;
        \App\Models\Find\Rdf\RDF::$created = [];
        \Config\Database::$db = new ConceptTestTransaction();
        $result = $controller->form_item();
        $missing = 0;
        foreach ($fields as $field) {
            if ($original[$field] === 0) {
                $missing++;
                check($result['book'][$field] > 0, 'Missing concept was not saved');
            } else {
                check($result['book'][$field] === $original[$field], 'Existing concept changed');
            }
        }
        check(count(\App\Models\Find\Rdf\RDF::$created) === $missing, 'Wrong creation count');
        $controller->form_item();
        check(count(\App\Models\Find\Rdf\RDF::$created) === $missing, 'Reload created duplicates');
    }
    \App\Models\Find\Items\Index::$item['i_work'] = 0;
    \App\Models\Find\Items\Index::$failUpdate = true;
    \Config\Database::$db = new ConceptTestTransaction();
    $controller->form_item();
    check(\Config\Database::$db->rolledBack && $controller->response->status === 500, 'Save failure was not rolled back');
    \App\Models\Find\Rdf\RDF::$created = [];
    \App\Libraries\CatalogPermissions::$allowed = false;
    $controller->form_item();
    check($controller->response->status === 403 && !\App\Models\Find\Rdf\RDF::$created, 'Unauthorized request created concepts');
    echo "OK: missing concepts, existing IDs, reload, save failure and permissions.\n";
}
