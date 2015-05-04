<?php
use NS\Core\Controller;

class Page extends Controller {
	function index() {
		echo 'Hello World!';
	}

	function view() {
		$this->View->File = 'view.php';
		$this->View->assign('framework_name', 'NewStep Framework');
	}
}
?>