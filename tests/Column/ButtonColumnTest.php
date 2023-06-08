<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yii\Support\Assert;
use Yii\TableBuilder\Column\ButtonColumn;
use Yii\TableBuilder\Column\Enum\DataAttribute;

final class ButtonColumnTest extends TestCase
{
    private array $row = ['id' => 1, 'name' => 'John Doe', 'blocked_at' => null];

    public function testAddDataAttribute(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button" data-action="block">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->addDataAttribute(DataAttribute::ACTION, 'block')
                ->content('Block')
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td class="test-class"><a role="button">Button</a></td>
            HTML,
            ButtonColumn::create()->class('test-class')->content('Button')->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContent(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button">Block</a></td>
            HTML,
            ButtonColumn::create()->content('Block')->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a id="block-id" role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->contentAttributes(['id' => 'block-id'])
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentAttributesWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a id="block-id" role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->contentAttributes(
                    [
                        'id' => static function (array $data, int|string $key) {
                            return $data[$key] === null ? 'block-id' : 'unblock-id';
                        },
                    ],
                )
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content(
                    static function (array $data, int|string $key) {
                        return $data[$key] === null
                            ? 'Block'
                            : 'Unblock';
                    }
                )
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentWithStringable(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button">Unblock</a></td>
            HTML,
            ButtonColumn::create()
                ->content(
                    new class () {
                        public function __toString(): string
                        {
                            return 'Unblock';
                        }
                    }
                )
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a class="btn btn-primary" role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->contentClass('btn btn-primary')
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentClassWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a class="btn btn-primary" role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content(
                    static function (array $data, int|string $key) {
                        return $data[$key] === null ? 'Block' : 'Unblock';
                    }
                )
                ->contentClass(
                    static function (array $data, int|string $key) {
                        return $data[$key] === null ? 'btn btn-primary' : 'btn btn-danger';
                    }
                )
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testContentWithEncode(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button">Block &amp; Unblock</a></td>
            HTML,
            ButtonColumn::create()->content('Block & Unblock')->renderDataCell($this->row, 'blocked_at'),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button">Block & Unblock</a></td>
            HTML,
            ButtonColumn::create()->content('Block & Unblock', false)->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testDataAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a id="block-id" role="button" data-action="block">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->contentAttributes(['id' => 'block-id'])
                ->dataAttributes(['data-action' => 'block'])
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testDisabled(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a class="disabled" role="button" aria-disabled="true">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->contentAttributes(['disabled' => true])
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testEmptyCell(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a role="button">test-empty-cell</a></td>
            HTML,
            ButtonColumn::create()->emptyCell('test-empty-cell')->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testHref(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/users/2" role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->href('/users/2')
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testHrefWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><a href="/users/1" role="button">Block</a></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->href(
                    static function (array $data, int|string $key) {
                        return '/users/' . $data['id'];
                    }
                )
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }

    public function testGetHref(): void
    {
        $buttonColumn = ButtonColumn::create()->href('/users/2');

        $this->assertsame('/users/2', $buttonColumn->getHref());
    }

    public function testImmutable(): void
    {
        $buttonColumn = ButtonColumn::create();

        $this->assertNotSame($buttonColumn, $buttonColumn->addDataAttribute(DataAttribute::ACTION, 'block'));
        $this->assertNotSame($buttonColumn, $buttonColumn->content(''));
        $this->assertNotSame($buttonColumn, $buttonColumn->contentAttributes([]));
        $this->assertNotSame($buttonColumn, $buttonColumn->contentClass(''));
        $this->assertNotSame($buttonColumn, $buttonColumn->dataAttributes([]));
        $this->assertNotSame($buttonColumn, $buttonColumn->href(''));
        $this->assertNotSame($buttonColumn, $buttonColumn->type(''));
    }

    public function testType(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <td><button type="submit">Block</button></td>
            HTML,
            ButtonColumn::create()
                ->content('Block')
                ->type('submit')
                ->renderDataCell($this->row, 'blocked_at'),
        );
    }
}
