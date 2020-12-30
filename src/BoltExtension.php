<?php

declare(strict_types=1);

namespace Monooso\Bolt;

use Codeception\Events;
use Codeception\Extension;
use Craft;
use craft\db\Connection;
use craft\test\TestSetup;
use RuntimeException;

final class BoltExtension extends Extension
{
    public static $events = [Events::SUITE_INIT => 'initializeSuite'];

    /**
     * Clean and restore the database in preparation for running the test suite
     *
     * @throws RuntimeException
     * @return void
     */
    public function initializeSuite(): void
    {
        $this->validateConfig($this->config);

        $connection = $this->getDatabaseConnection();

        $this->cleanDatabase($connection);
        $this->restoreDatabase($connection, $this->config['dump']);
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
        if (!$config['dump']) {
            throw new RuntimeException('Missing or invalid `dump` config');
        }
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
     * @param string $dump
     *
     * @return void
     */
    private function restoreDatabase(Connection $connection, string $dump): void
    {
        $connection->restore($dump);
    }
}
