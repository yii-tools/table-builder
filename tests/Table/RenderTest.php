<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Table;

use PHPForge\Support\Assert;
use PHPUnit\Framework\TestCase;
use Yii\TableBuilder\Table;
use Yii\TableBuilder\Tests\Support\TestTrait;

final class RenderTest extends TestCase
{
    use TestTrait;

    public function testEmptyText(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody><tr>
            <td colspan="0">empty table test</td>
            </tr></tbody>
            <thead>
            <tr>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration(true))->emptyText('empty table test')->render(),
        );
    }

    public function testFooter(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-id="id">1</td>
            <td data-name="name">John Doe</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">2</td>
            <td data-name="name">Jane Doe</td>
            <td data-blocked_at="blocked_at">2021-01-01 00:00:00</td>
            </tr>
            <tr>
            <td data-id="id">3</td>
            <td data-name="name">John Smith</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">4</td>
            <td data-name="name">empty cell</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Blocked_at</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
            <td>empty cell</td>
            <td>empty cell</td>
            <td>empty cell</td>
            </tr>
            </tfoot>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration())->canBeShowFooter(true)->emptyText('empty table test')->render(),
        );
    }

    public function testFooterWithEmptyData(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody><tr>
            <td colspan="0">empty table</td>
            </tr></tbody>
            <thead>
            <tr>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration(true))->canBeShowFooter(true)->render(),
        );
    }

    public function testHeaderAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-id="id">1</td>
            <td data-name="name">John Doe</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">2</td>
            <td data-name="name">Jane Doe</td>
            <td data-blocked_at="blocked_at">2021-01-01 00:00:00</td>
            </tr>
            <tr>
            <td data-id="id">3</td>
            <td data-name="name">John Smith</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">4</td>
            <td data-name="name">empty cell</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            </tr>
            </tbody>
            <thead class="test-class">
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration())->headerAttributes(['class' => 'test-class'])->render(),
        );
    }

    public function testRender(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr>
            <td data-id="id">1</td>
            <td data-name="name">John Doe</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">2</td>
            <td data-name="name">Jane Doe</td>
            <td data-blocked_at="blocked_at">2021-01-01 00:00:00</td>
            </tr>
            <tr>
            <td data-id="id">3</td>
            <td data-name="name">John Smith</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">4</td>
            <td data-name="name">empty cell</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration())->render()
        );
    }

    public function testRowAttribute(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            <tbody>
            <tr class="test-class">
            <td data-id="id">1</td>
            <td data-name="name">John Doe</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr class="test-class">
            <td data-id="id">2</td>
            <td data-name="name">Jane Doe</td>
            <td data-blocked_at="blocked_at">2021-01-01 00:00:00</td>
            </tr>
            <tr class="test-class">
            <td data-id="id">3</td>
            <td data-name="name">John Smith</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr class="test-class">
            <td data-id="id">4</td>
            <td data-name="name">empty cell</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr class="test-class">
            <td></td>
            <td></td>
            <td></td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration())->rowAttributes(['class' => 'test-class'])->render(),
        );
    }

    public function testToolBar(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table>
            toolbar
            <tbody>
            <tr>
            <td data-id="id">1</td>
            <td data-name="name">John Doe</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">2</td>
            <td data-name="name">Jane Doe</td>
            <td data-blocked_at="blocked_at">2021-01-01 00:00:00</td>
            </tr>
            <tr>
            <td data-id="id">3</td>
            <td data-name="name">John Smith</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td data-id="id">4</td>
            <td data-name="name">empty cell</td>
            <td data-blocked_at="blocked_at">empty cell</td>
            </tr>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            </tr>
            </tbody>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Blocked_at</th>
            </tr>
            </thead>
            </table>
            HTML,
            Table::widget($this->getTableConfiguration())->toolBar('toolbar')->render(),
        );
    }
}
