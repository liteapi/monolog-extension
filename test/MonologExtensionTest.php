<?php

namespace LiteApi\MonologExtension\Test;

use LiteApi\Exception\ProgrammerException;
use LiteApi\MonologExtension\MonologExtension;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use PHPUnit\Framework\TestCase;

class MonologExtensionTest extends TestCase
{

    public function testValidateConfig(): void
    {
        $config = [
            'kernel.logger' => [
                'handlers' => [
                    [
                        'class' => StreamHandler::class,
                        'args' => ['/var/log/app.log', Level::Debug]
                    ]
                ]
            ]
        ];

        $extension = new MonologExtension();
        $extension->loadConfig($config);
        $extension->validateConfig();
        $this->expectNotToPerformAssertions();
    }

    public function testValidateConfigWrong(): void
    {
        $config = [
            0 => [
                'handlers' => [
                    [
                        'class' => StreamHandler::class,
                        'args' => ['/var/log/app.log', Level::Debug]
                    ]
                ]
            ]
        ];
        $this->expectException(ProgrammerException::class);
        $extension = new MonologExtension();
        $extension->loadConfig($config);
        $extension->validateConfig();
    }
}
