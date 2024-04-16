<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RdfPrefix extends Migration
{
	public function up()
	{
      $this->forge->addField([
            'id_prefix' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'prefix_ref' => [
                'type' => 'VARCHAR',
                'constraint' => '30'
			], 
            'prefix_url' => [
				'type' => 'TEXT',
			], 			         
            'prefix_ativo' => [
                'type' => 'INT',
                'default' => '1'
            ],            
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_prefix', true);
        $this->forge->createTable('rdf_prefix');
	}

	public function down()
	{
		//
	}
}
