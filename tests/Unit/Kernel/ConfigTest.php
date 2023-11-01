<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Kernel;

use App\CommissionTask\Kernel\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testAllMethods(): void
    {
        $config = new Config();
        $config->load();
        $this->assertNotEmpty($config->getAllConfigValues());
        $this->assertNotEmpty($config->getEnvVarByName('CURRENCY_API_URL'));
        $this->assertNotEmpty($config->getConfigParamByName('parameters.fee.withdraw.private.percent'));
        $this->assertNull($config->getConfigParamByName('parameters.fee.withdraw.business.amount'));
        $this->assertNull($config->getConfigParamByName(''));
    }
}
