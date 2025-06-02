<?php

namespace Avtomato\ConsoleCommands\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class CommandHelper
{
    private static array $defaultCommands = [
        'optimize:clear',
        'tenants:seed',
        'sync-ican-transactions', // todo: remove after first run
    ];

    public static function getCommands(): array
    {
        $commands = [];
        $commandClasses = Artisan::all();

        foreach ($commandClasses as $commandClass) {
            if (! in_array($commandClass->getName(), self::$defaultCommands, true)) {
                continue;
            }

            $commands[] = [
                'name' => $commandClass->getName(),
                'description' => $commandClass->getDescription(),
            ];
        }

        return $commands;
    }

    public static function getCommand(string $commandName): array
    {
        $commandClasses = Artisan::all();

        $commandClass = Arr::first($commandClasses, function ($commandClass) use ($commandName) {
            return $commandClass->getName() === $commandName;
        });

        if (! $commandClass) {
            throw new CommandNotFoundException(sprintf('The command "%s" does not exist.', $commandName));
        }

        $commandData = [
            'name' => $commandClass->getName(),
            'class' => get_class($commandClass),
            'description' => $commandClass->getDescription(),
            'options' => [],
            'arguments' => [],
        ];

        foreach ($commandClass->getDefinition()->getArguments() as $name => $description) {
            $value = $description->getDefault() ?? null;
            $hidden = false;

            if ($name === 'executor-user-id') {
                $value = auth()->id();
                $hidden = true;
            }

            $commandData['arguments'][] = [
                'name' => $name,
                'description' => $description->getDescription(),
                'isRequired' => $description->isRequired(),
                'isArray' => $description->isArray(),
                'value' => $value,
                'hidden' => $hidden,
            ];
        }

        foreach ($commandClass->getDefinition()->getOptions() as $name => $description) {
            $commandData['options'][] = [
                'name' => $name,
                'description' => $description->getDescription(),
                'value' => $description->getDefault() ?? null,
                'shortcut' => $description->getShortcut(),
                'isNegatable' => $description->isNegatable(),
                'isArray' => $description->isArray(),
                'isRequired' => $description->isValueRequired(),
                'isValueOptional' => $description->isValueOptional(),
                'acceptValue' => $description->acceptValue(),
                'hidden' => false,
            ];
        }

        return $commandData;
    }

    public static function processCommand(string $commandName, array $commandData): void
    {
        if ($commandName === 'optimize:clear') {
            Artisan::call('optimize:clear');

            return;
        }

        $parameters = [];

        foreach ($commandData['arguments'] as $argument) {
            if (! $argument['value']) {
                continue;
            }

            $parameters[$argument['name']] = $argument['value'];
        }

        foreach ($commandData['options'] as $option) {
            if (! $option['value']) {
                continue;
            }

            $parameters['--' . $option['name']] = $option['value'];
        }

        if (isset($parameters['--all']) && isset($parameters['--companyIds'])) {
            unset($parameters['--companyIds']);
        }

        Artisan::queue($commandName, $parameters);
    }
}
