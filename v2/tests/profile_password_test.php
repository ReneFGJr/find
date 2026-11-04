<?php
namespace CodeIgniter {
    class Controller {
        public $request;
    }
}
namespace App\Models\Social {
    class Social {
        public static $stored;
        public static $saved;
        public static $saveResult = true;
        public function find($id) {
            if ($id !== 7) throw new \RuntimeException('Unexpected account');
            return ['us_password' => self::$stored];
        }
        public function update($id, $data) {
            if ($id !== 7) throw new \RuntimeException('Unexpected account update');
            self::$saved = $data;
            return self::$saveResult;
        }
    }
}
namespace {
    class ProfileTestSession {
        public $id = 7;
        public $regenerated = false;
        public $data = [];
        public function get($key) { return $this->id; }
        public function regenerate($destroy) { $this->regenerated = $destroy; }
        public function set($key, $value) { $this->data[$key] = $value; }
    }
    class ProfileTestRedirect {
        public $target;
        public function to($target) { $this->target = $target; return $this; }
        public function with($key, $value) { return $this; }
    }
    function session() { return $GLOBALS['testSession']; }
    function redirect() { return new ProfileTestRedirect(); }
    function helper($names) {}
    function view($name, $data = []) { return ['view' => $name, 'data' => $data]; }
    function check($value, $message) { if (!$value) throw new RuntimeException($message); }
    require __DIR__ . '/../app/Controllers/Perfil.php';
    $testSession = new ProfileTestSession();
    $controller = new \App\Controllers\Perfil();
    $controller->request = new class {
        public $method = 'post';
        public $post = [];
        public function getMethod() { return $this->method; }
        public function getPost($key) { return $this->post[$key] ?? null; }
    };
    $testSession->id = null;
    check($controller->password()->target === '/login', 'Anonymous access must redirect');
    $testSession->id = 7;
    $controller->request->method = 'get';
    check($controller->password()['view'] === 'Users/change_password', 'Form must load');
    $controller->request->method = 'post';
    foreach ([md5('old-password'), password_hash('old-password', PASSWORD_DEFAULT)] as $stored) {
        \App\Models\Social\Social::$stored = $stored;
        foreach ([
            ['wrong', 'new-password', 'new-password'],
            ['old-password', 'short', 'short'],
            ['old-password', 'new-password', 'different'],
            ['old-password', ' new-password', ' new-password'],
            ['old-password', str_repeat('a', 73), str_repeat('a', 73)],
            ['old-password', "new\0password", "new\0password"],
            [[], 'new-password', 'new-password'],
            ['', '', ''],
        ] as [$current, $password, $confirmation]) {
            \App\Models\Social\Social::$saved = null;
            $controller->request->post = ['current_password' => $current, 'new_password' => $password, 'confirm_password' => $confirmation];
            $result = $controller->password();
            check(!empty($result['data']['error']), 'Invalid request must show error');
            check(\App\Models\Social\Social::$saved === null, 'Invalid request must not update account');
        }
        $controller->request->post = ['current_password' => 'old-password', 'new_password' => 'new-password', 'confirm_password' => 'new-password'];
        check($controller->password()->target === '/perfil', 'Successful change must redirect to profile');
        $saved = \App\Models\Social\Social::$saved;
        check(password_verify('new-password', $saved['us_password']), 'New hash must authenticate');
        check(!password_verify('old-password', $saved['us_password']), 'Old password must stop working');
        check($testSession->regenerated, 'Session must rotate');
        check($testSession->data['apikey'] === $saved['us_apikey'], 'Session key must match updated key');
    }
    \App\Models\Social\Social::$saveResult = false;
    check(!empty($controller->password()['data']['error']), 'Database failure must show error');
    echo "OK: authentication required, form, invalid inputs, legacy and modern hashes, session rotation, save failure.\n";
}
