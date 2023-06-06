<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Table;

use PHPUnit\Framework\TestCase;
use Yii\TableBuilder\Tests\Support\TestTrait;
use Yii\TableBuilder\Table;
use Yii\TableBuilder\TableSorter;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testTableImmutability(): void
    {
        $table = Table::widget($this->getTableConfiguration(true));

        $this->assertNotSame($table, $table->canBeShowFooter(true));
        $this->assertNotSame($table, $table->emptyText(''));
        $this->assertNotSame($table, $table->headerAttributes([]));
        $this->assertNotSame($table, $table->rowAttributes([]));
        $this->assertNotSame($table, $table->toolbar(''));
    }

    public function testTableSorterImmutability(): void
    {
        $tableSorter = TableSorter::widget();

        $this->assertNotSame($tableSorter, $tableSorter->column(''));
        $this->assertNotSame($tableSorter, $tableSorter->currentPage(0));
        $this->assertNotSame($tableSorter, $tableSorter->linkAttributes([]));
        $this->assertNotSame($tableSorter, $tableSorter->linkClass(''));
        $this->assertNotSame($tableSorter, $tableSorter->pageName(''));
        $this->assertNotSame($tableSorter, $tableSorter->pageSize(0));
        $this->assertNotSame($tableSorter, $tableSorter->pageSizeName(''));
        $this->assertNotSame($tableSorter, $tableSorter->separator(''));
        $this->assertNotSame($tableSorter, $tableSorter->sortClassAsc(''));
        $this->assertNotSame($tableSorter, $tableSorter->sortClassDesc(''));
        $this->assertNotSame($tableSorter, $tableSorter->sortParamName(''));
        $this->assertNotSame($tableSorter, $tableSorter->sortParams([]));
        $this->assertNotSame($tableSorter, $tableSorter->urlQueryParameters([]));
        $this->assertNotSame($tableSorter, $tableSorter->urlPath(''));
    }
}
