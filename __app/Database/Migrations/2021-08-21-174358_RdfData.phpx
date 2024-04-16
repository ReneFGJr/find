<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RdfData extends Migration
{
	public function up()
	{
      $this->forge->addField([
            'id_d' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'd_r1' => [
                'type' => 'INT',
            ],   
            'd_p' => [
				'type' => 'INT',            
			], 			         
            'd_r2' => [
                'type' => 'INT',
            ],            
            'd_literal' => [
				'type' => 'INT',			
			],
            'd_library' => [
                'type' => 'INT',
			],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_d', true);
        $this->forge->createTable('rdf_data');
	}

	public function down()
	{
		//
	}
}
