<?php

namespace App\Libraries;

/** MARC 21 / ISO 2709. All directory lengths and offsets are byte counts. */
class Marc21Export
{
    public function record(array $item, array $metadata, array $copies): string
    {
        $fields = [['001', 'FIND-' . $item['id_i']]];
        $values = static function (string $key) use ($metadata): array {
            return array_values(array_unique(array_filter(array_map(static function ($entry) {
                return trim((string) ($entry['name'] ?? ''));
            }, $metadata[$key] ?? []), static function ($value) { return $value !== ''; })));
        };
        $add = function (string $tag, string $indicators, array $subfields) use (&$fields) {
            $data = '';
            foreach ($subfields as $code => $value) {
                $value = $this->text((string) $value);
                if ($value !== '') {
                    $data .= "\x1f" . $code . $value;
                }
            }
            if ($data !== '') {
                $fields[] = [$tag, $indicators . $data];
            }
        };
        $identifier = trim((string) ($item['i_identifier'] ?? ''));
        $isbn = preg_replace('/[\s-]/', '', $identifier);
        if (preg_match('/^(?:[0-9]{13}|[0-9]{9}[0-9Xx])$/', $isbn)) {
            $add('020', '  ', ['a' => $isbn]);
        } elseif ($identifier !== '') {
            $add('035', '  ', ['a' => $identifier]);
        }
        foreach (['CDD' => ['082', '04'], 'CDU' => ['080', '  ']] as $key => $mapping) {
            foreach ($values($key) as $value) {
                $add($mapping[0], $mapping[1], ['a' => $value]);
            }
        }
        $authors = $values('Authors');
        if (!$authors && !empty($item['i_autores'])) {
            $authors = array_values(array_filter(array_map('trim', explode(';', $item['i_autores']))));
        }
        if ($authors) {
            $add('100', strpos($authors[0], ',') !== false ? '1 ' : '0 ', ['a' => $authors[0]]);
        }
        $add('245', $authors ? '10' : '00', ['a' => $item['i_titulo'] ?: implode(' : ', $values('Title'))]);
        $add('260', '  ', [
            'a' => implode(' ; ', $values('PublisherPlace') ?: $values('Place')),
            'b' => implode(' ; ', $values('Publisher')),
            'c' => implode(' ; ', $values('dateOfPublication')) ?: ($item['i_year'] ?? ''),
        ]);
        $add('300', '  ', ['a' => implode(' ; ', $values('Page'))]);
        foreach ($values('Description') as $value) {
            $add('500', '  ', ['a' => $value]);
        }
        foreach ($values('Subject') as $value) {
            $add('650', ' 4', ['a' => $value]);
        }
        foreach (array_slice($authors, 1) as $author) {
            $add('700', strpos($author, ',') !== false ? '1 ' : '0 ', ['a' => $author]);
        }
        foreach ($copies as $copy) {
            $add('852', '  ', [
                'a' => $copy['i_library'],
                'h' => trim(implode(' ', array_map(static function ($key) use ($copy) {
                    return $copy[$key] ?? '';
                }, ['i_ln1', 'i_ln2', 'i_ln3', 'i_ln4']))),
                'p' => $copy['i_tombo'] ?? '',
                't' => $copy['i_exemplar'] ?? '',
            ]);
        }
        return $this->encode($fields);
    }

    private function text(string $value): string
    {
        if (!preg_match('//u', $value)) {
            throw new \RuntimeException('O acervo contém texto inválido em UTF-8.');
        }
        return trim(preg_replace('/[\x00-\x1f\x7f]/', ' ', $value));
    }

    public function encode(array $fields): string
    {
        $directory = '';
        $data = '';
        foreach ($fields as [$tag, $value]) {
            $length = strlen($value) + 1;
            if ($length > 9999) {
                throw new \RuntimeException('Um campo excede o limite de 9999 bytes do MARC21.');
            }
            $directory .= $tag . sprintf('%04d%05d', $length, strlen($data));
            $data .= $value . "\x1e";
        }
        $base = 24 + strlen($directory) + 1;
        $length = $base + strlen($data) + 1;
        if ($length > 99999) {
            throw new \RuntimeException('Um registro excede o limite de 99999 bytes do MARC21.');
        }
        // Minimal bibliographic description, UTF-8 (leader/09 = a).
        $leader = sprintf('%05d', $length) . 'nam a22' . sprintf('%05d', $base) . '7u 4500';
        return $leader . $directory . "\x1e" . $data . "\x1d";
    }
}
