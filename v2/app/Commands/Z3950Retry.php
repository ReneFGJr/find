<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Z3950Retry extends BaseCommand
{
    protected $group = 'FIND';
    protected $name = 'z3950:retry';
    protected $description = 'Reenvia pedidos de coleta de metadados pendentes para a API Brapci.';

    public function run(array $params)
    {
        $sent = (new \App\Libraries\Z3950Outbox())->retry();
        CLI::write("Pedidos enviados: {$sent}");
    }
}
