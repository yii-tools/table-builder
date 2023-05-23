<?php

declare(strict_types=1);

namespace Yii\TableBuilder;

use IteratorAggregate;
use Yii\TableBuilder\Column\AbstractColumn;

/**
 * Defines the methods for configuring a table builder.
 *
 * @extends IteratorAggregate<mixed>
 */
interface TableConfigurationInterface extends IteratorAggregate
{
    /**
     * @return array The columns for the table builder.
     *
     * @psalm-return AbstractColumn[]
     */
    public function getColumns(): array;

    /**
     * @return int The page size for the table builder.
     */
    public function getPageSize(): int;

    /**
     * @return string The pagination for the table builder.
     */
    public function getPagination(): string;
}
