<?php

use UliCMS\HTML\Alert;

class AlertTest extends \PHPUnit\Framework\TestCase {

    public function testDanger() {
        $this->assertEquals("<div class=\"alert alert-danger \">&lt;strong&gt;Hello World&lt;/strong&gt;</div>",
                Alert::danger("<strong>Hello World</strong>", "", false));
    }

    public function testInfo() {
        $this->assertEquals("<div class=\"alert alert-info \">&lt;strong&gt;Hello World&lt;/strong&gt;</div>",
                Alert::info("<strong>Hello World</strong>", "", false));
    }

    public function testWarning() {
        $this->assertEquals("<div class=\"alert alert-warning \">&lt;strong&gt;Hello World&lt;/strong&gt;</div>",
                Alert::warning("<strong>Hello World</strong>", "", false));
    }

    public function testSuccess() {
        $this->assertEquals("<div class=\"alert alert-success \">&lt;strong&gt;Hello World&lt;/strong&gt;</div>",
                Alert::success("<strong>Hello World</strong>", "", false));
    }

    public function testDangerAllowHtml() {
        $this->assertEquals("<div class=\"alert alert-danger \"><strong>Hello World</strong></div>",
                Alert::danger("<strong>Hello World</strong>", "", true));
    }

    public function testInfoAllowHtml() {
        $this->assertEquals("<div class=\"alert alert-info \"><strong>Hello World</strong></div>",
                Alert::info("<strong>Hello World</strong>", "", true));
    }

    public function testWarningAllowHtml() {
        $this->assertEquals("<div class=\"alert alert-warning \"><strong>Hello World</strong></div>",
                Alert::warning("<strong>Hello World</strong>", "", true));
    }

    public function testSuccessAllowHtml() {
        $this->assertEquals("<div class=\"alert alert-success \"><strong>Hello World</strong></div>",
                Alert::success("<strong>Hello World</strong>", "", true));
    }

}
