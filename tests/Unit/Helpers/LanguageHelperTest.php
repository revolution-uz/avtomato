<?php

namespace Tests\Unit\Helpers;

use App\Helpers\LanguageHelper;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LanguageHelperTest extends TestCase
{
    #[Test]
    public function getLanguageByCode(): void
    {
        $this->assertEquals('O\'zbekcha', LanguageHelper::getLanguageName('uz'));
        $this->assertEquals('Русский', LanguageHelper::getLanguageName('ru'));
        $this->assertEquals('English', LanguageHelper::getLanguageName('en'));
        $this->assertEquals('Ўзбекча', LanguageHelper::getLanguageName('oz'));
        $this->assertEquals('Русский', LanguageHelper::getLanguageName('cn'));
    }
}
