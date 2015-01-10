<?php 
class Migration_Cria_tabela_de_funcionarios extends CI_migration {
	
	public function up() {
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT','auto_increment' => true),
			'nome' => array('type' => 'varchar(255)')
		));
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('funcionarios', true);
	}

	public function down() {
		$this->dbforge->drop_table('funcionarios');
	}
}