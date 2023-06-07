<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yii\Support\Assert;
use Yii\TableBuilder\Column\Column;

final class ColumnTest extends TestCase
{
    private array $row = ['id' => 1, 'name' => 'John Doe', 'blocked_at' => null];
    private array $rowEmptyCell = ['id' => '1', 'name' => '', 'blocked_at' => null];

    public function testAttributesWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td class="text-center">John Doe</td>
            HTML,
            Column::create()->attributes(['class' => fn () => 'text-center'])->name('name')->renderDataCell($this->row, 1),
        );
    }

    public function testClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td class="test-class">John Doe</td>
            HTML,
            Column::create()->class('test-class')->name('name')->renderDataCell($this->row, 1),
        );
    }

    public function testDataLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td data-label="Block">John Doe</td>
            HTML,
            Column::create()->dataLabel('Block')->name('name')->renderDataCell($this->row, 1),
        );
    }

    public function testDefinitions(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td class="test-class">John Doe</td>
            HTML,
            Column::create(['class()' => ['test-class'], 'name()' => ['name']])->renderDataCell($this->row, 1),
        );
    }

    public function testEmptyCell(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td>test empty cell</td>
            HTML,
            Column::create()->emptyCell('test empty cell')->name('name')->renderDataCell($this->rowEmptyCell, 1),
        );
    }

    public function testFooter(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td>test footer</td>
            HTML,
            Column::create()->footer('test footer')->name('name')->renderFooterCell(),
        );
    }

    public function testFooterAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td class="test-class">test footer</td>
            HTML,
            Column::create()
                ->footer('test footer')
                ->footerAttributes(['class' => 'test-class'])
                ->name('name')
                ->renderFooterCell(),
        );
    }

    public function testFooterWithEmptyData(): void
    {
        $this->assertEmpty(Column::create()->emptyCell('')->renderFooterCell());
    }

    public function testImmutable(): void
    {
        $column = Column::create();

        $this->assertNotSame($column, $column->class(''));
        $this->assertNotSame($column, $column->dataLabel(''));
        $this->assertNotSame($column, $column->emptyCell(''));
        $this->assertNotSame($column, $column->footer(''));
        $this->assertNotSame($column, $column->footerAttributes([]));
        $this->assertNotSame($column, $column->label(''));
        $this->assertNotSame($column, $column->labelAttributes([]));
        $this->assertNotSame($column, $column->labelClass(''));
        $this->assertNotSame($column, $column->name(''));
        $this->assertNotSame($column, $column->value(''));
        $this->assertNotSame($column, $column->visible(false));
    }

    public function testLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <th>Test label</th>
            HTML,
            Column::create()->label('test label')->renderHeaderCell(),
        );
    }

    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <th id="test-id">Test label</th>
            HTML,
            Column::create()->label('test label')->labelAttributes(['id' => 'test-id'])->renderHeaderCell(),
        );
    }

    public function testLabelClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <th class="test-class">Test label</th>
            HTML,
            Column::create()->label('test label')->labelClass('test-class')->renderHeaderCell(),
        );
    }

    public function testLabelWithEmpty(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <th>Id</th>
            HTML,
            Column::create()->label('')->name('id')->renderHeaderCell(),
        );
    }

    public function testRenderDataCellWithEmptyData(): void
    {
        $this->assertEmpty(Column::create()->renderDataCell([], 1));
    }

    public function testValue(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td>Sam doe</td>
            HTML,
            Column::create()->value('Sam doe')->name('name')->renderDataCell($this->row, 1),
        );
    }

    public function testValueWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td>UnBlock</td>
            HTML,
            Column::create()
                ->name('blocket_at')
                ->value(static fn (array $data) => $data['blocked_at'] === null ? 'UnBlock' : 'Blocked')
                ->renderDataCell($this->row, 1),
        );
    }
}
