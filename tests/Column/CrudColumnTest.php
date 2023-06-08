<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yii\Support\Assert;
use Yii\TableBuilder\Column\ButtonColumn;
use Yii\TableBuilder\Column\CrudColumn;
use Yii\TableBuilder\Column\Enum\DataAttribute;

final class CrudColumnTest extends TestCase
{
    private array $row = ['id' => 1, 'name' => 'John Doe', 'blocked_at' => null];
    private array $rowCustomId = ['uuid' => '017f22e2-79b0-7cc3-98c4-dc0c0c07398f', 'name' => 'John Doe', 'blocked_at' => null];

    public function testActions(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/delete/1" role="button"><span>🗑</span></a><a href="/view/1" role="button"><span>🔎</span></a></td>
            HTML,
            CrudColumn::create()
                ->actions(['delete' => 'delete', 'view' => 'view'])
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testActionsWithWrongName(): void
    {
        $this->assertEmpty(
            CrudColumn::create()->actions(['wrong'])->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testActionsAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/delete/1" role="button" data-foo="bar"><span>🗑</span></a><a id="test-id-update" href="/update/1" role="button"><span>✎</span></a></td>
            HTML,
            CrudColumn::create()
                ->actionsAttributes(
                    [
                        'delete' => ['data-foo' => 'bar'],
                        'update' => ['id' => 'test-id-update'],
                    ],
                )
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testAddActionClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a class="test-class" href="/delete/1" role="button"><span>🗑</span></a><a href="/update/1" role="button"><span>✎</span></a></td>
            HTML,
            CrudColumn::create()->addActionClass('delete', 'test-class')->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testAddButtonColumn(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/confirmation/1" role="button">✅</a></td>
            HTML,
            CrudColumn::create()
                ->actions(['confirmation'])
                ->addButtonColumn('confirmation', ButtonColumn::create()->content('✅'))
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testAddDataAttribute(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/delete/1" role="button" data-action="deleteHandler"><span>🗑</span></a><a href="/update/1" role="button"><span>✎</span></a></td>
            HTML,
            CrudColumn::create()
                ->addDataAttribute('delete', DataAttribute::ACTION, 'deleteHandler')
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testImmutable(): void
    {
        $crudColumn = CrudColumn::create();

        $this->assertNotSame($crudColumn, $crudColumn->actions([]));
        $this->assertNotSame($crudColumn, $crudColumn->actionsAttributes([]));
        $this->assertNotSame($crudColumn, $crudColumn->addActionClass('', ''));
        $this->assertNotSame($crudColumn, $crudColumn->addButtonColumn('', ButtonColumn::create()));
        $this->assertNotSame($crudColumn, $crudColumn->addDataAttribute('', DataAttribute::ACTION, ''));
        $this->assertNotSame($crudColumn, $crudColumn->buttons([]));
        $this->assertNotSame($crudColumn, $crudColumn->primaryKey(''));
        $this->assertNotSame($crudColumn, $crudColumn->urlPath(''));
    }

    public function testPrimaryKey(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/delete/017f22e2-79b0-7cc3-98c4-dc0c0c07398f" role="button"><span>🗑</span></a><a href="/update/017f22e2-79b0-7cc3-98c4-dc0c0c07398f" role="button"><span>✎</span></a></td>
            HTML,
            CrudColumn::create()->primaryKey('uuid')->renderDataCell($this->rowCustomId, 'blocked_at'),
        );
    }

    public function testRenderDataCellWithEmpty(): void
    {
        $this->assertEmpty(CrudColumn::create()->renderDataCell([], 'blocked_at'));
    }

    public function testUrlPath(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/user/admin/delete/1" role="button"><span>🗑</span></a><a href="/user/admin/update/1" role="button"><span>✎</span></a></td>
            HTML,
            CrudColumn::create()->urlPath('/user/admin')->renderDataCell($this->row, 'blocked_at'),
        );
    }
}
