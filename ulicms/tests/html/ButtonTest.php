<?php

use UliCMS\HTML\Button;
use UliCMS\Constants\ButtonType;

class ButtonTest extends \PHPUnit\Framework\TestCase {

    public function testButtonWithoutAnything() {
        $this->assertEquals('<button type="submit" class="btn">Do Something</button>',
                Button::button("Do Something"));
    }

    public function testButtonWithType() {
        $this->assertEquals('<button type="reset" class="btn">Do Something</button>',
                Button::button("Do Something", ButtonType::BUTTON_RESET));
    }

    public function testButtonWithoutHtml() {
        $this->assertEquals('<button type="submit" class="btn">&lt;i class=&quot;fa fa-save&quot;&gt;&lt;/i&gt; Do Something</button>',
                Button::button("<i class=\"fa fa-save\"></i> Do Something"));
    }

    public function testButtonWithHtml() {
        $this->assertEquals('<button type="submit" class="btn"><i class="fa fa-save"></i> Do Something</button>',
                Button::button("<i class=\"fa fa-save\"></i> Do Something", ButtonType::BUTTON_SUBMIT, [], true));
    }

    public function testButtonDefault() {
        $this->assertEquals('<button class="btn btn-default" type="submit">Do Something</button>',
                Button::default("Do Something"));
    }

    public function testButtonPrimary() {
        $this->assertEquals('<button class="btn btn-primary" type="submit">Do Something</button>',
                Button::primary("Do Something"));
    }

    public function testButtonSuccess() {
        $this->assertEquals('<button class="btn btn-success" type="submit">Do Something</button>',
                Button::success("Do Something"));
    }

    public function testButtonInfo() {
        $this->assertEquals('<button class="btn btn-info" type="submit">Do Something</button>',
                Button::info("Do Something"));
    }

    public function testButtonWarning() {
        $this->assertEquals('<button class="btn btn-warning" type="submit">Do Something</button>',
                Button::warning("Do Something"));
    }

    public function testButtonDanger() {
        $this->assertEquals('<button class="btn btn-danger" type="submit">Do Something</button>',
                Button::danger("Do Something"));
    }

    public function testButtonLink() {
        $this->assertEquals('<button class="btn btn-link" type="submit">Do Something</button>',
                Button::link("Do Something"));
    }

}
