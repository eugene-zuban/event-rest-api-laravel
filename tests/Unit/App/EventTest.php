<?php

namespace tests\App;

use App\Event;
use Tests\TestCase;

class EventTest extends TestCase
{
    public function testThatEventModelExists()
    {
        $this->assertTrue(class_exists(Event::class));
    }
}
