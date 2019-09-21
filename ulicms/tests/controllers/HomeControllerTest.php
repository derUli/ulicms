<?php
class HomeControllerTest extends \PHPUnit\Framework\TestCase {
	public function testGetModel(){
		$controller = ControllerRegistry::get(HomeController::class);
		
		$model = $controller->getModel();
		
		$this->assertInstanceOf(HomeViewModel::class, $model);
		
		// TODO: Do more asserts, check data
		
	}
}
?>