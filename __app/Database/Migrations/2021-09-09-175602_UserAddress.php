<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserAddress extends Migration
{
	public function up()
	{
      $this->forge->addField([
            'id_ud' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'ud_user' => [
                'type' => 'INT',
            ], 						
            'ud_cep' => [
                'type' => 'VARCHAR',
                'constraint' => '80'
            ],                       
            'ud_logradouro' => [
                'type' => 'VARCHAR',
                'constraint' => '100'
			],			
            'ud_number' => [
                'type' => 'VARCHAR',
                'constraint' => '15'
            ],    
            'ud_complemento' => [
                'type' => 'VARCHAR',
                'constraint' => '20'
			],		
            'ud_bairro' => [
                'type' => 'VARCHAR',
                'constraint' => '50'
			],				
            'ud_localidade' => [
                'type' => 'VARCHAR',
                'constraint' => '150'
            ],
            'ud_uf' => [
                'type' => 'VARCHAR',
                'constraint' => '2'
            ],
            'ud_country' => [
                'type' => 'VARCHAR',
                'constraint' => '3'
            ],
            'ud_ibge' => [
                'type' => 'INT',
            ],
            'ud_gia' => [
                'type' => 'VARCHAR',
                'constraint' => '15'
            ], 
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_ud', true);
        $this->forge->createTable('users_address');
	}

	public function down()
	{
		//
	}
}
