<?php

namespace Framework\Twig;

use PHPUnit\Framework\TestCase;
use Framework\Twig\TimeExtension;

class TimeExtensionTest extends  TestCase
{

    private TimeExtension $timeExtension;

    public function setUp(): void
    {
        $this->timeExtension = new TimeExtension();
    }

    public function testDateFormat ()
    {
        $date = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = '<span class="timeago" datetime="+' . $date->format(\DateTime::ATOM) . '">' . $date->format($format) . '</span>';
        $this->assertEquals($result, $this->timeExtension->ago($date));
    }

}