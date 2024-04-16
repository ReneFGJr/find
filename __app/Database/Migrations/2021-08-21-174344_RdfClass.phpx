<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RdfClass extends Migration
{
	public function up()
	{
      $this->forge->addField([
            'id_c' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'c_class' => [
                'type' => 'VARCHAR',
                'constraint' => '80'
            ],            
            'c_prefix' => [
                'type' => 'INT',
            ],            
            'c_type' => [
                'type' => 'VARCHAR',
                'constraint' => '1'
			],			
            'c_url' => [
                'type' => 'VARCHAR',
                'constraint' => '150'
            ],            
            'c_equivalent' => [
				'type' => 'INT',            
			],  
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_c', true);
        $this->forge->createTable('rdf_class');
	}

	public function down()
	{
		//
	}
}
