<?php

use App\HTML\Alert;

class AlertTest extends \PHPUnit\Framework\TestCase {
    public function testAlert(): void {
        $this->assertEquals(
            '<div class="alert foo bar">&lt;strong&gt;Hello World&lt;/strong&gt;</div>',
            Alert::alert('<strong>Hello World</strong>', 'foo bar', false)
        );
    }

    public function testDanger(): void {
        $this->assertEquals(
            '<div class="alert alert-danger ">&lt;strong&gt;Hello World&lt;/strong&gt;</div>',
            Alert::danger('<strong>Hello World</strong>', '', false)
        );
    }

    public function testInfo(): void {
        $this->assertEquals(
            '<div class="alert alert-info ">&lt;strong&gt;Hello World&lt;/strong&gt;</div>',
            Alert::info('<strong>Hello World</strong>', '', false)
        );
    }

    public function testWarning(): void {
        $this->assertEquals(
            '<div class="alert alert-warning ">&lt;strong&gt;Hello World&lt;/strong&gt;</div>',
            Alert::warning('<strong>Hello World</strong>', '', false)
        );
    }

    public function testSuccess(): void {
        $this->assertEquals(
            '<div class="alert alert-success ">&lt;strong&gt;Hello World&lt;/strong&gt;</div>',
            Alert::success('<strong>Hello World</strong>', '', false)
        );
    }

    public function testAlertAllowHtml(): void {
        $this->assertEquals(
            '<div class="alert foo bar"><strong>Hello World</strong></div>',
            Alert::alert('<strong>Hello World</strong>', 'foo bar', true)
        );
    }

    public function testDangerAllowHtml(): void {
        $this->assertEquals(
            '<div class="alert alert-danger "><strong>Hello World</strong></div>',
            Alert::danger('<strong>Hello World</strong>', '', true)
        );
    }

    public function testInfoAllowHtml(): void {
        $this->assertEquals(
            '<div class="alert alert-info "><strong>Hello World</strong></div>',
            Alert::info('<strong>Hello World</strong>', '', true)
        );
    }

    public function testWarningAllowHtml(): void {
        $this->assertEquals(
            '<div class="alert alert-warning "><strong>Hello World</strong></div>',
            Alert::warning('<strong>Hello World</strong>', '', true)
        );
    }

    public function testSuccessAllowHtml(): void {
        $this->assertEquals(
            '<div class="alert alert-success "><strong>Hello World</strong></div>',
            Alert::success('<strong>Hello World</strong>', '', true)
        );
    }
}
