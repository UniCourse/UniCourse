<?php 

class CoursenameRelationModel extends RelationModel
{
	protected $tableName = 'student';
	
	protected $_link = array(
		'course' => array(
				'mapping_type' => MANY_TO_MANY,
				'foreign_key' => 'sno',
				'relation_foreign_key' => 'cno',
				'relation_table' => 'stu_course'
		)
	);
}
?>