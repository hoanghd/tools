<?php
class EDM_Default_Table extends EDM_Abstract{
	public function forms(){
         wp_send_json(array(
		 	array(
				'id' => 100,
				'name' => 'Test 01',
				'formList' => array(
					array(
						'id' => 10,
						'name' => ' sdfasdf',
						'type' => 'String',
						'length' => 5,
						'decimals' => 0,
						'allowNull' => true,
						'def' => '',
						'comment' => ''
					),
					array(
						'id' => 11,
						'name' => ' sdfasdf 3333',
						'type' => 'String',
						'length' => 5,
						'decimals' => 0,
						'allowNull' => true,
						'def' => '',
						'comment' => ''
					)
				)
			),
			array(
				'id' => 101,
				'name' => 'Test 02',
				'formList' => array(
					array(
						'id' => 21,
						'name' => ' sdfasdf',
						'type' => 'String',
						'length' => 5,
						'decimals' => 0,
						'allowNull' => true,
						'def' => '',
						'comment' => ''
					),
					array(
						'id' => 22,
						'name' => ' sdfasdf 3333',
						'type' => 'String',
						'length' => 5,
						'decimals' => 0,
						'allowNull' => true,
						'def' => '',
						'comment' => ''
					)
				)
			)
		 ));
    }
	
    public function index(){
         $this->render('index');
    }
}
?>