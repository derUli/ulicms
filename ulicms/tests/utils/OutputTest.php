<?php

class OutputTest extends \PHPUnit\Framework\TestCase {

    public function testFcFlush() {
        fcflush();
        $this->assertEquals(0, ob_get_length());
    }

}
