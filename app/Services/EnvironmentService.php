<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class EnvironmentService
{
    /**
     * Set environment variable temporarily for command execution
     */
    public function setTemporaryEnvironment(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    /**
     * Execute a command with temporary environment variables
     */
    public function executeWithEnvironment(array $envVars, callable $callback): mixed
    {
        $originalEnv = [];
        
        // Backup original values
        foreach ($envVars as $key => $value) {
            $originalEnv[$key] = $_ENV[$key] ?? null;
            $originalEnv[$key] = $_SERVER[$key] ?? null;
            $originalEnv[$key] = getenv($key) ?: null;
        }

        try {
            // Set temporary environment
            foreach ($envVars as $key => $value) {
                $this->setTemporaryEnvironment($key, $value);
            }

            return $callback();
        } finally {
            // Restore original environment
            foreach ($originalEnv as $key => $value) {
                if ($value === null) {
                    putenv($key);
                    unset($_ENV[$key], $_SERVER[$key]);
                } else {
                    $this->setTemporaryEnvironment($key, $value);
                }
            }
        }
    }

    /**
     * Check if environment is production
     */
    public function isProduction(): bool
    {
        return app()->environment('production');
    }

    /**
     * Check if environment is local/development
     */
    public function isLocal(): bool
    {
        return app()->environment(['local', 'testing']);
    }

    /**
     * Get current environment
     */
    public function getEnvironment(): string
    {
        return app()->environment();
    }
}