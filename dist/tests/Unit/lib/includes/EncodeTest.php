<?php

use Spatie\Snapshots\MatchesSnapshots;

class EncodeTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    public function testJsonReadableEncode(): void {
        $data = [
            'foo' => 'bar',
            'hello' => 'world',
            'animals' => ['cat', 'dog', 'pig'],
            'number' => 123,
            'boolean' => true,
            'null' => null
        ];

        $this->assertMatchesTextSnapshot(
            json_readable_encode($data)
        );
    }
}
