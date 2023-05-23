<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Support;

use Yii\DataProvider\ArrayIteratorDataProvider;
use Yii\TableBuilder\TableConfiguration;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Exception\InvalidConfigException;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;
use Yiisoft\Db\Sqlite\Dsn;

trait TestTrait
{
    private array $data = [
        ['id' => 1, 'name' => 'John Doe', 'blocked_at' => null],
        ['id' => 2, 'name' => 'Jane Doe', 'blocked_at' => '2021-01-01 00:00:00'],
        ['id' => 3, 'name' => 'John Smith', 'blocked_at' => null],
        ['id' => 4, 'name' => '', 'blocked_at' => ''],
    ];
    private string $dsn = '';

    private function getArrayIteratorDataProvider(bool $emptyData = false): ArrayIteratorDataProvider
    {
        if ($emptyData) {
            return new ArrayIteratorDataProvider([]);
        }

        return new ArrayIteratorDataProvider($this->data);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    private function getConnection(): PdoConnectionInterface
    {
        return new Connection(new Driver($this->getDsn()), new SchemaCache(new ArrayCache()));
    }

    private function getDriverName(): string
    {
        return 'sqlite';
    }

    private function getDsn(): string
    {
        if ($this->dsn === '') {
            $this->dsn = (new Dsn('sqlite', 'memory'))->asString();
        }

        return $this->dsn;
    }

    private function getTableConfiguration(bool $emptyData = false): TableConfiguration
    {
        if ($emptyData) {
            return new TableConfiguration($this->getArrayIteratorDataProvider(true), 0, 0);
        }

        return new TableConfiguration($this->getArrayIteratorDataProvider($emptyData), 0, 5);
    }

    private function setDsn(string $dsn): void
    {
        $this->dsn = $dsn;
    }
}
