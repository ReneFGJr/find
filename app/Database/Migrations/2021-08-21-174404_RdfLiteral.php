<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RdfMame extends Migration
{
	public function up()
	{
      $this->forge->addField([
            'id_n' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'n_name' => [
                'type' => 'TEXT',
			], 
            'n_lock' => [
				'type' => 'INT',
				'default' => 0
			], 			         
            'n_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '5'
            ],            
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_n', true);
        $this->forge->createTable('rdf_literal');
	}

	public function down()
	{
		//
	}
}
