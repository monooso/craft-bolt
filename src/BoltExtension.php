<?php

declare(strict_types=1);

namespace Monooso\Bolt;

use Codeception\Configuration;
use Codeception\Events;
use Codeception\Exception\ModuleConfigException;
use Codeception\Extension;
use Craft;
use craft\db\Connection;
use craft\test\TestSetup;
use RuntimeException;

final class BoltExtension extends Extension
{
    public static $events = [Events::SUITE_INIT => 'initializeSuite'];

    private array $bundledDumps = ['bolt:3.5' => '35.sql'];

    /**
     * Clean and restore the database in preparation for running the test suite
     *
     * @throws RuntimeException
     * @return void
     */
    public function initializeSuite(): void
    {
        $config = $this->normalizeConfig($this->config);

        $this->validateConfig($config);

        $connection = $this->getDatabaseConnection();

        $this->cleanDatabase($connection);
        $this->restoreDatabase($connection, $config['dump']);
    }

    /**
     * Normalise the extension config array
     *
     * Does not perform any validation.
     *
     * @param array $config
     *
     * @return array
     */
    private function normalizeConfig(array $config): array
    {
        $config['dump'] = $this->ensureArray($config['dump']);

        return $config;
    }

    /**
     * Ensure that the given value is an array
     *
     * @param mixed $value
     *
     * @return array
     */
    private function ensureArray($value): array
    {
        return is_array($value) ? $value : [$value];
    }

    /**
     * Validate the configuration array
     *
     * @param array $config
     *
     * @throws RuntimeException
     * @return void
     */
    private function validateConfig(array $config): void
    {
        foreach ($config['dump'] as $dump) {
            $this->validateDump($dump);
        }
    }

    /**
     * Validate that the specified dump exists
     */
    private function validateDump(string $dump): void
    {
        if ($this->isBundledDump($dump)) {
            return;
        }

        if (!file_exists(Configuration::projectDir() . $dump)) {
            $message = "\nFile with dump doesn't exist."
                . "\nPlease check path for SQL file: ${dump}";

            throw new ModuleConfigException(__CLASS__, $message);
        }
    }

    /**
     * Determine if the given string refers to a bundled database dump
     *
     * @param string $dump
     *
     * @return bool
     */
    private function isBundledDump(string $dump): bool
    {
        return in_array($dump, array_keys($this->bundledDumps));
    }

    /**
     * Get a connection to the configured database
     *
     * @return Connection
     */
    private function getDatabaseConnection(): Connection
    {
        TestSetup::warmCraft();

        return Craft::$app->getDb();
    }

    /**
     * Clean the database, if required
     *
     * @param Connection $connection
     *
     * @return void
     */
    private function cleanDatabase(Connection $connection): void
    {
        $this->disableForeignKeys($connection);
        $this->dropTables($connection);
        $this->enableForeignKeys($connection);
    }

    /**
     * Disable database foreign key checks
     *
     * @param Connection $connection
     *
     * @return void
     */
    private function disableForeignKeys(Connection $connection): void
    {
        $connection->createCommand('SET foreign_key_checks = 0')->execute();
    }

    /**
     * Drop all of the database tables
     *
     * Assumes that foreign key checks are disabled.
     *
     * @param Connection $connection
     *
     * @return void
     */
    private function dropTables(Connection $connection): void
    {
        $tables = $connection->schema->getTableNames();

        foreach ($tables as $table) {
            $connection->createCommand()->dropTable($table)->execute();
        }
    }

    /**
     * Enable database foreign key checks
     *
     * @param Connection $connection
     *
     * @return void
     */
    private function enableForeignKeys(Connection $connection): void
    {
        $connection->createCommand('SET foreign_key_checks = 1')->execute();
    }

    /**
     * Restore the database from the specified SQL dump
     *
     * @param Connection $connection
     * @param string[] $dumps
     *
     * @return void
     */
    private function restoreDatabase(Connection $connection, array $dumps): void
    {
        foreach ($dumps as $dump) {
            if ($this->isBundledDump($dump)) {
                $dump = $this->getBundledDumpFilePath($dump);
            }

            $connection->restore($dump);
        }
    }

    /**
     * Retrieve the full path to the specified bundled dump file
     *
     * Does not check whether the given key is valid.
     *
     * @param string $key
     *
     * @return string
     */
    private function getBundledDumpFilePath(string $key): string
    {
        $filename = $this->bundledDumps[$key];

        return realpath(dirname(__DIR__) . "/dumps/${filename}");
    }
}
