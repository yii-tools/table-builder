<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Table;

use PHPUnit\Framework\TestCase;
use Yii\DataProvider\Sort;
use Yii\Support\Assert;
use Yii\TableBuilder\Column\ButtonColumn;
use Yii\TableBuilder\Column\Column;
use Yii\TableBuilder\Table;
use Yii\TableBuilder\TableConfiguration;
use Yii\TableBuilder\Tests\Support\TestTrait;

final class TableConfigurationTest extends TestCase
{
    use TestTrait;

    public function testAddColumn(): void
    {
        $tableConfiguration = new TableConfiguration($this->getArrayIteratorDataProvider(), 0, 0);
        $tableConfiguration = $tableConfiguration->addColumn(
            'blocked_at',
            ButtonColumn::create()
                ->content(static fn ($data) => $data['blocked_at'] === null ? 'Blocked' : 'Unblocked')
                ->name('Block/Unblock')
                ->href(static fn ($data) => '/user/block/' . $data['id'])
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John Doe</td>
            <td><a href="/user/block/1" role="button">Blocked</a></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Jane Doe</td>
            <td><a href="/user/block/2" role="button">Unblocked</a></td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">John Smith</td>
            <td><a href="/user/block/3" role="button">Blocked</a></td>
            </tr>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">empty cell</td>
            <td><a href="/user/block/4" role="button">Unblocked</a></td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th>id</th>
            <th>name</th>
            <th>Block/Unblock</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget([$tableConfiguration])->render(),
        );
    }

    public function testAddColumnValue(): void
    {
        $tableConfiguration = new TableConfiguration($this->getArrayIteratorDataProvider(), 0, 0);
        $tableConfiguration = $tableConfiguration->addColumnValue(
            'blocked_at',
            static fn ($data) => $data['blocked_at'] === null ? 'Blocked' : 'Unblocked',
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John Doe</td>
            <td data-label="blocked_at">Blocked</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Jane Doe</td>
            <td data-label="blocked_at">Unblocked</td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">John Smith</td>
            <td data-label="blocked_at">Blocked</td>
            </tr>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">empty cell</td>
            <td data-label="blocked_at">Unblocked</td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th>id</th>
            <th>name</th>
            <th>blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget([$tableConfiguration])->render(),
        );
    }

    public function testColumnsLabelClass(): void
    {
        $tableConfiguration = new TableConfiguration($this->getArrayIteratorDataProvider(), 0, 0);
        $tableConfiguration = $tableConfiguration->columnsLabelClass('test-class');

        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John Doe</td>
            <td data-label="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Jane Doe</td>
            <td data-label="blocked_at">2021-01-01 00:00:00</td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">John Smith</td>
            <td data-label="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">empty cell</td>
            <td data-label="blocked_at">empty cell</td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th class="test-class">id</th>
            <th class="test-class">name</th>
            <th class="test-class">blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget([$tableConfiguration])->render(),
        );
    }

    public function testImutable(): void
    {
        $tableConfiguration = new TableConfiguration($this->getArrayIteratorDataProvider(), 0, 0);

        $this->assertNotSame($tableConfiguration, $tableConfiguration->addColumn('', Column::create()));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->addColumnLabel('', ''));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->addColumnValue('', ''));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->attributes([]));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->class(''));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->columnsLabelClass(''));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->exceptColumns(''));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->pagination(''));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->queryParams([]));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->sortParams([]));
        $this->assertNotSame($tableConfiguration, $tableConfiguration->urlPath(''));
    }

    public function testSortAsc(): void
    {
        $sort = (new Sort())->columns(['id', 'username', 'email'])->multisort()->params(['sort' => 'id']);

        $tableConfiguration = new TableConfiguration(
            $this->getArrayIteratorDataProvider()->sortOrders($sort->getOrders()),
            0,
            0,
        );
        $tableConfiguration = $tableConfiguration
            ->addColumnValue(
                'blocked_at',
                static fn ($data) => $data['blocked_at'] === null ? 'Blocked' : 'Unblocked',
            )
            ->queryParams(['sort' => 'id'])
            ->sortParams($sort->getSortParams());

        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John Doe</td>
            <td data-label="blocked_at">Blocked</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Jane Doe</td>
            <td data-label="blocked_at">Unblocked</td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">John Smith</td>
            <td data-label="blocked_at">Blocked</td>
            </tr>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">empty cell</td>
            <td data-label="blocked_at">Unblocked</td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th><a class="text-blue-500 hover:underline asc" href="?sort=-id&amp;page=0&amp;page-size=0" data-sort="-id">Id</a></th>
            <th>name</th>
            <th>blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget([$tableConfiguration])->render(),
        );
    }

    public function testSortDesc(): void
    {
        $sort = (new Sort())->columns(['id', 'username', 'email'])->multisort()->params(['sort' => '-id']);

        $tableConfiguration = new TableConfiguration(
            $this->getArrayIteratorDataProvider()->sortOrders($sort->getOrders()),
            0,
            0,
        );
        $tableConfiguration = $tableConfiguration
            ->addColumnValue(
                'blocked_at',
                static fn ($data) => $data['blocked_at'] === null ? 'Blocked' : 'Unblocked',
            )
            ->queryParams(['sort' => '-id'])
            ->sortParams($sort->getSortParams());

        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">empty cell</td>
            <td data-label="blocked_at">Unblocked</td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">John Smith</td>
            <td data-label="blocked_at">Blocked</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Jane Doe</td>
            <td data-label="blocked_at">Unblocked</td>
            </tr>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John Doe</td>
            <td data-label="blocked_at">Blocked</td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th><a class="text-blue-500 hover:underline desc" href="?sort=id&amp;page=0&amp;page-size=0" data-sort="id">Id</a></th>
            <th>name</th>
            <th>blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget([$tableConfiguration])->render(),
        );
    }
}
