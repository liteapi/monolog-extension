<?php

namespace LiteApi\MonologExtension;

use LiteApi\Component\Extension\Extension;
use LiteApi\Container\Container;
use LiteApi\Exception\ProgrammerException;
use LiteApi\MonologExtension\Definition\MonologLogger;

class MonologExtension extends Extension
{

    public function validateConfig(): void
    {
        foreach ($this->config as $name => $item) {
            $this->assertIsString($name);
            $this->assertHasOnlyPermittedKeys($item, ['class', 'handlers', 'processors']);
            $this->assertHasKeys($item, ['handlers']);
            $this->assertIsArray($item['handlers']);
        }
    }


    public function registerServices(Container $container): void
    {
        /** @var array<string, MonologLogger> $definitions */
        $definitions = [];
        foreach ($this->config as $loggerName => $loggerConfig) {
            if (isset($loggerConfig['class']) && str_starts_with($loggerConfig['class'], '@')) {
                $loggerName = substr($loggerConfig['class'], 1);
                $definition = $this->getDefinitionFrom($definitions, $loggerName);
                $aliasHandlers = $definition->handlers;
                $aliasProcessors = $definition->processors;
            }
            $handlers = $loggerConfig['handlers'];
            $processors = $loggerConfig['processors'] ?? [];
            if (isset($aliasHandlers)) {
                $handlers += $aliasHandlers;
            }
            if (isset($aliasProcessors)) {
                $processors += $aliasProcessors;
            }
            $definitions[$loggerName] = new MonologLogger($loggerName, $handlers, $processors);
        }
        $container->load($definitions);
    }

    /**
     * @param array<string,MonologLogger> $definitions
     * @param string $loggerName
     * @return MonologLogger
     * @throws ProgrammerException
     */
    private function getDefinitionFrom(array $definitions, string $loggerName): MonologLogger
    {
        if (isset($definitions[$loggerName])) {
            return $definitions[$loggerName];
        }
        throw new ProgrammerException('Cannot find logger definition of ' . $loggerName);
    }

}