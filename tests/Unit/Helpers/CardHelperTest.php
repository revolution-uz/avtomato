<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CardHelper;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CardHelperTest extends TestCase
{
    #[Test]
    public function isUzcardTest(): void
    {
        $this->assertTrue(CardHelper::isUzcard('8600123456789012'));
        $this->assertTrue(CardHelper::isUzcard('5614123456789012'));
        $this->assertTrue(CardHelper::isUzcard('6262123456789012'));
        $this->assertFalse(CardHelper::isUzcard('1234567890123456'));
        $this->assertTrue(CardHelper::isUzcard(''));
        $this->assertFalse(CardHelper::isUzcard(null));
    }

    #[Test]
    public function getNameByCardNumberTest(): void
    {
        $this->assertEquals('UZCARD', CardHelper::getNameByCardNumber('8600123456789012'));
        $this->assertEquals('UZCARD', CardHelper::getNameByCardNumber('5614123456789012'));
        $this->assertEquals('UZCARD', CardHelper::getNameByCardNumber('6262123456789012'));
        $this->assertEquals('HUMO', CardHelper::getNameByCardNumber('1234567890123456'));
        $this->assertEquals('UZCARD', CardHelper::getNameByCardNumber(''));
        $this->assertEquals('HUMO', CardHelper::getNameByCardNumber(null));
    }
}
