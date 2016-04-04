<?php
use NS\Core\Model;

class ExampleModel extends Model {
	function __construct() {
		/* Change TABLE_NAME with the name of table on database that you want to use
		 * Change PRIMARY_KEY with column name on database table you want to use that defined as primary key with auto increment
		 */
		parent::__construct('TABLE_NAME', 'PRIMARY_KEY');
	}
}
?>