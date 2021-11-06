<?php

namespace Test;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Math.php';

class MathTest extends TestCase
{
    public function testDouble(): void
    {
        $this->assertSame(4, double(2));
    }
}
