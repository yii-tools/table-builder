<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use Yii\Html\Helper\CssClass;
use Yii\Html\Tag;
use Yii\Widget\Attribute;

use function ucfirst;

/**
 * Base class for table columns.
 */
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

    /**
     * Renders the data cell for a specific row and column.
     *
     * @param array|object $data The data associated with the row.
     * @param int|string $key The key associated with the row.
     *
     * @return string The rendered data cell.
     */
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

    /**
     * Returns a new instance specifying the empty cell content for the column.
     *
     * @param string $value The empty cell content for the column.
     */
    public function emptyCell(string $value): static
    {
        $new = clone $this;
        $new->emptyCell = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the footer content for the column.
     *
     * @param string $value The footer content for the column.
     */
    public function footer(string $value): static
    {
        $new = clone $this;
        $new->footer = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the footer `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function footerAttributes(array $values): static
    {
        $new = clone $this;
        $new->footerAttributes = $values;

        return $new;
    }

    /**
     * @return bool Whether the column is visible.
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Returns a new instance specifying the label for the column.
     *
     * @param string $value The label for the column.
     */
    public function label(string $value): static
    {
        $new = clone $this;
        $new->label = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the label `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function labelAttributes(array $values): static
    {
        $new = clone $this;
        $new->labelAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the `CSS` `HTML` class attribute of the column label.
     *
     * @param string $value The `CSS` `HTML` class attribute of the column label.
     */
    public function labelClass(string $value): static
    {
        $new = clone $this;
        CssClass::add($new->labelAttributes, $value);

        return $new;
    }

    /**
     * Returns a new instance specifying the column name for the column.
     *
     * @param string $value The column name for the column.
     */
    public function name(string $value): static
    {
        $new = clone $this;
        $new->name = $value;

        return $new;
    }

    /**
     * @return string Renders the footer cell of the column.
     */
    public function renderFooterCell(): string
    {
        $cellContent = $this->renderFooterCellContent();

        if ($cellContent === '') {
            return '';
        }

        return Tag::create('td', $cellContent, $this->footerAttributes);
    }

    /**
     * @return string Renders the header cell of the column.
     */
    public function renderHeaderCell(): string
    {
        return Tag::create('th', $this->renderHeaderCellContent(), $this->labelAttributes);
    }

    /**
     * Returns a new instance specifying whether the column is visible.
     *
     * @param bool $value Whether the column is visible.
     */
    public function visible(bool $value): static
    {
        $new = clone $this;
        $new->visible = $value;

        return $new;
    }

    /**
     * @return static Returns a new instance of the column.
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @return string Renders the footer cell content of the column.
     */
    private function renderFooterCellContent(): string
    {
        return $this->footer !== '' ? $this->footer : $this->emptyCell;
    }

    /**
     * @return string Renders the header cell content of the column.
     */
    private function renderHeaderCellContent(): string
    {
        return ucfirst($this->label) ?: ucfirst($this->name);
    }
}
