<?php
namespace App\Models\Find\Items;

class MarcExport
{
    public function records(string $library): \Generator
    {
        $items = new Index();
        $rdf = new \App\Models\Find\Rdf\RDF();
        $metadataModel = new \App\Models\Find\Metadata\Index();
        $encoder = new \App\Libraries\Marc21Export();
        $seen = [];
        $lastId = 0;
        do {
            // Large collections can take longer than PHP's request time limit.
            // Renew it per batch without changing the limit for other requests.
            if (is_callable('set_time_limit')) {
                set_time_limit(120);
            }
            $batch = $items->where('i_library', $library)->where('id_i >', $lastId)
                ->orderBy('id_i')->findAll(200);
            foreach ($batch as $item) {
                $lastId = $item['id_i'];
                if ((int) $item['i_manifestation'] > 0) {
                    $field = 'i_manifestation';
                } elseif (trim((string) $item['i_identifier']) !== '') {
                    $field = 'i_identifier';
                } else {
                    $field = 'id_i';
                }
                $key = $field . ':' . $item[$field];
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $copyQuery = $items->where('i_library', $library)->where($field, $item[$field]);
                if ($field === 'i_identifier') {
                    $copyQuery->groupStart()->where('i_manifestation <=', 0)->orWhere('i_manifestation', null)->groupEnd();
                }
                $copies = $copyQuery->orderBy('id_i')->findAll();
                $pending = [$item['i_manifestation'], $item['i_expression'], $item['i_work']];
                $visited = [];
                $data = [];
                while ($pending) {
                    $id = (int) array_shift($pending);
                    if ($id <= 0 || isset($visited[$id])) {
                        continue;
                    }
                    $visited[$id] = true;
                    // getData is read-only; le() also updates authority records.
                    $properties = $rdf->getData($id);
                    $data = array_merge($data, $properties);
                    foreach ($properties as $property) {
                        if (in_array($property['Property'], ['isAppellationOfManifestation', 'isAppellationOfExpression'], true)) {
                            $pending[] = $property['ID'];
                        }
                    }
                }
                try {
                    $record = $encoder->record($item, $metadataModel->metadata(['data' => $data]), $copies);
                } catch (\Throwable $error) {
                    throw new \RuntimeException(
                        'Item ' . $item['id_i'] . ': ' . $error->getMessage(),
                        0,
                        $error
                    );
                }
                yield $record;
            }
        } while (count($batch) === 200);
    }
}
