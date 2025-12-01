<?php

class ArgParser
{
    private array $args = [];
    private string $command = '';
    private array $options = [];
    private array $params = [];

    public function __construct(array $argv = null)
    {
        if ($argv === null) {
            $argv = $_SERVER['argv'];
        }
        
        array_shift($argv); // Remove script name
        $this->args = $argv;
        $this->parse();
    }

    private function parse(): void
    {
        if (empty($this->args)) {
            return;
        }

        $this->command = $this->args[0] ?? '';
        array_shift($this->args);

        foreach ($this->args as $arg) {
            if (str_starts_with($arg, '--')) {
                $this->parseLongOption($arg);
            } elseif (str_starts_with($arg, '-')) {
                $this->parseShortOption($arg);
            } else {
                $this->params[] = $arg;
            }
        }
    }

    private function parseLongOption(string $arg): void
    {
        $parts = explode('=', $arg, 2);
        $key = substr($parts[0], 2);
        $value = $parts[1] ?? true;
        $this->options[$key] = $value;
    }

    private function parseShortOption(string $arg): void
    {
        $key = substr($arg, 1);
        $this->options[$key] = true;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(int $index, $default = null)
    {
        return $this->params[$index] ?? $default;
    }

    public function hasOption(string $key): bool
    {
        return isset($this->options[$key]);
    }

    public function hasCommand(): bool
    {
        return !empty($this->command);
    }
}