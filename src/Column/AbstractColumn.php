<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use Yii\Html\Helper\CssClass;
use Yii\Html\Tag;
use Yii\Widget\Attribute;

use function ucfirst;

abstract class AbstractColumn
{
    use Attribute\Custom\HasAttributes;

    protected array $attributes = [];
    protected string $emptyCell = 'empty cell';
    protected string $label = '';
    protected string $name = '';
    private array $labelAttributes = [];
    private string $footer = '';
    private array $footerAttributes = [];
    private bool $visible = true;

    final public function __construct()
    {
    }

    abstract public function renderDataCell(array|object $data, int|string $key): string;

    /**
     * Returns a new instance specifying the `CSS` `HTML` class attribute of the widget.
     *
     * @param string $value The `CSS` attribute of the widget.
     *
     * @link https://html.spec.whatwg.org/#classes
     */
    public function class(string $value): static
    {
        $new = clone $this;
        CssClass::add($new->attributes, $value);

        return $new;
    }

    public function emptyCell(string $value): static
    {
        $new = clone $this;
        $new->emptyCell = $value;

        return $new;
    }

    public function footer(string $value): static
    {
        $new = clone $this;
        $new->footer = $value;

        return $new;
    }

    public function footerAttributes(array $value): static
    {
        $new = clone $this;
        $new->footerAttributes = $value;

        return $new;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function label(string $value): static
    {
        $new = clone $this;
        $new->label = $value;

        return $new;
    }

    public function labelAttributes(array $value): static
    {
        $new = clone $this;
        $new->labelAttributes = $value;

        return $new;
    }

    public function labelClass(string $value): static
    {
        $new = clone $this;
        CssClass::add($new->labelAttributes, $value);

        return $new;
    }

    public function name(string $value): static
    {
        $new = clone $this;
        $new->name = $value;

        return $new;
    }

    public function renderFooterCell(): string
    {
        $cellContent = $this->renderFooterCellContent();

        if ($cellContent === '') {
            return '';
        }

        return Tag::create('td', $cellContent, $this->footerAttributes);
    }

    public function renderHeaderCell(): string
    {
        return Tag::create('th', $this->renderHeaderCellContent(), $this->labelAttributes);
    }

    public function visible(bool $value): static
    {
        $new = clone $this;
        $new->visible = $value;

        return $new;
    }

    public static function create(): static
    {
        return new static();
    }

    protected function renderHeaderCellContent(): string
    {
        return $this->label !== '' ? $this->label : ucfirst($this->name);
    }

    private function renderFooterCellContent(): string
    {
        return $this->footer !== '' ? $this->footer : $this->emptyCell;
    }
}
