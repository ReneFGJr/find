<?php
require __DIR__ . '/../app/Libraries/Marc21Export.php';

function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
$writer = new App\Libraries\Marc21Export();
$item = ['id_i' => 123, 'i_identifier' => '978-85-123-4567-8', 'i_titulo' => 'Ação e educação 日本語', 'i_autores' => 'Silva, João; Souza, José', 'i_year' => '2025'];
$meta = ['Publisher' => [['name' => 'Edição São Paulo']], 'Subject' => [['name' => 'Educação']], 'CDD' => [['name' => '370']]];
$copies = [
    ['i_library' => '1001', 'i_tombo' => '001', 'i_exemplar' => '1'],
    ['i_library' => '1001', 'i_tombo' => '002', 'i_exemplar' => '2'],
];
$first = $writer->record($item, $meta, $copies);
$item['id_i'] = 124;
$item['i_identifier'] = 'LOCAL:124';
$item['i_autores'] = '';
$second = $writer->record($item, [], []);
$file = $first . $second;
$position = 0;
$records = [];
while ($position < strlen($file)) {
    $length = (int) substr($file, $position, 5);
    check($length >= 25, 'Invalid record length');
    $record = substr($file, $position, $length);
    check(strlen($record) === $length && substr($record, -1) === "\x1d", 'Record boundary');
    check($record[9] === 'a' && substr($record, 10, 2) === '22', 'UTF-8 leader');
    check(substr($record, 20, 4) === '4500', 'Entry map');
    $base = (int) substr($record, 12, 5);
    check($record[$base - 1] === "\x1e" && ($base - 25) % 12 === 0, 'Directory boundary');
    $fields = [];
    $expectedOffset = 0;
    for ($i = 24; $i < $base - 1; $i += 12) {
        $tag = substr($record, $i, 3);
        $size = (int) substr($record, $i + 3, 4);
        $offset = (int) substr($record, $i + 7, 5);
        check($offset === $expectedOffset, 'Byte offset');
        $data = substr($record, $base + $offset, $size);
        check(strlen($data) === $size && substr($data, -1) === "\x1e", 'Field boundary');
        check(preg_match('//u', $data) === 1, 'UTF-8 field');
        $fields[$tag][] = substr($data, 0, -1);
        $expectedOffset += $size;
    }
    check($base + $expectedOffset + 1 === $length, 'Total length');
    $records[] = $fields;
    $position += $length;
}
check(count($records) === 2, 'Consecutive records');
check(strpos($records[0]['245'][0], 'Ação e educação 日本語') !== false, 'Unicode preserved');
check(count($records[0]['852']) === 2, 'Copies retained');
check(isset($records[0]['020']) && isset($records[1]['035']), 'Identifier mapping');
check(substr($records[1]['245'][0], 0, 2) === '00', 'Title without main entry');
foreach ([ [['245', str_repeat('a', 9999)]], array_fill(0, 11, ['500', str_repeat('a', 9500)]) ] as $fields) {
    try { $writer->encode($fields); throw new LogicException('Overflow accepted'); }
    catch (RuntimeException $e) {}
}
$item['i_titulo'] = "\xff";
try { $writer->record($item, [], []); throw new LogicException('Invalid UTF-8 accepted'); }
catch (RuntimeException $e) {}
echo "OK: consecutive records, UTF-8, byte offsets, copies, identifiers and size limits.\n";
