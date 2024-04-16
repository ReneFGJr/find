<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ItemHistoricLoan extends Migration
{
	public function up()
	{
      $this->forge->addField([
            'id_hl' => [
                'type' => 'INT',
                'auto_increment' => true
            ], 
            'hl_date' => [
                'type' => 'DATE'
			], 	
            'hl_item' => [
                'type' => 'INT',
                'default' => '0'
			],
            'hl_status' => [
                'type' => 'INT',
			],
            'hl_ip' => [
                'type' => 'VARCHAR',
                'constraint' => '40'
			],
            'hl_user' => [
                'type' => 'INT'
			],		
            'hl_library' => [
                'type' => 'INT',
			],            													          
            'hl_created datetime default current_timestamp',
            'hl_update datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id_hl', true);
        $this->forge->createTable('find_item_historic_loan');
	}

	public function down()
	{
		//
	}
}
