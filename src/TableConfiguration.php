<?php

declare(strict_types=1);

namespace Yii\TableBuilder;

use IteratorAggregate;
use PHPForge\Html\Helper\CssClass;
use Traversable;
use Yii\TableBuilder\Column\AbstractColumn;
use Yii\TableBuilder\Column\Column;

final class TableConfiguration implements TableConfigurationInterface
{
    protected array $attributes = [];
    protected array $queryParams = [];
    protected string $urlPath = '';
    /** @psalm-var AbstractColumn[] */
    private array $columns = [];
    private array $columnsAttributes = [];
    /** @psalm-var string[] */
    private array $columnsLabel = [];
    private array $columnsLabelAttributes = [];
    private string $columnsLabelClass = '';
    private array $columnsValue = [];
    private array $exceptColumns = [];
    private string $pagination = '';
    private array $sortParams = [];

    public function __construct(
        public IteratorAggregate $iteratorDataProvider,
        public readonly int $page,
        public readonly int $pageSize
    ) {
    }

    /**
     * Returns a new instance by adding a new column to the table.
     *
     * @param string $name The column name.
     * @param AbstractColumn $column The column instance.
     */
    public function addColumn(string $name, AbstractColumn $column): self
    {
        $new = clone $this;
        $new->columns[$name] = $column;

        return $new;
    }

    /**
     * Returns a new instance by adding a new column label to the table.
     *
     * @param string $name The column name.
     * @param string $value The column label.
     *
     * @psalm-param string[] $values
     */
    public function addColumnLabel(string $name, string $value): self
    {
        $new = clone $this;
        $new->columnsLabel[$name] = $value;

        return $new;
    }

    /**
     * Returns a new instance by adding a new column value to the table.
     *
     * @param string $name The column name.
     * @param mixed $value The column value.
     */
    public function addColumnValue(string $name, mixed $value): self
    {
        $new = clone $this;
        $new->columnsValue[$name] = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function columnsAttributes(array $values): self
    {
        $new = clone $this;
        $new->columnsAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the `CSS` `HTML` class attribute of the columns.
     *
     * @param string $value The `CSS` `HTML` class attribute of the columns.
     */
    public function columnsClass(string $value): self
    {
        $new = clone $this;
        CssClass::add($new->columnsAttributes, $value);

        return $new;
    }

    /**
     * Returns a new instance specifying the `CSS` `HTML` class attribute of the columns labels.
     *
     * @param string $value The `CSS` `HTML` class attribute of the columns label.
     */
    public function columnsLabelClass(string $value): self
    {
        $new = clone $this;
        $new->columnsLabelClass = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the columns excluded from the table.
     *
     * @param string ...$values The columns to exclude.
     */
    public function exceptColumns(string ...$values): self
    {
        $new = clone $this;
        $new->exceptColumns = $values;

        return $new;
    }

    /**
     * @return array The columns of the table.
     *
     * @psalm-return AbstractColumn[]
     */
    public function getColumns(): array
    {
        return $this->generateColumns();
    }

    public function getIterator(): Traversable
    {
        return $this->iteratorDataProvider->getIterator();
    }

    /**
     * @return int The current page number.
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return string The pagination widget.
     */
    public function getPagination(): string
    {
        return $this->pagination;
    }

    /**
     * Returns a new instance specifying the widget pagination.
     *
     * @param string $value The widget pagination rendered.
     */
    public function pagination(string $value): self
    {
        $new = clone $this;
        $new->pagination = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the query parameters for the table.
     *
     * @param array $values The query parameters for the table.
     */
    public function queryParams(array $values): self
    {
        $new = clone $this;
        $new->queryParams = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the sort parameters for the table.
     *
     * @param array $values The sort parameters for the table.
     */
    public function sortParams(array $values): self
    {
        $new = clone $this;
        $new->sortParams = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the the URL path for the table.
     */
    public function urlPath(string $value): self
    {
        $new = clone $this;
        $new->urlPath = $value;

        return $new;
    }

    /**
     * @return array The columns generated from the data iterator provider.
     *
     * @psalm-return AbstractColumn[]
     */
    private function generateColumns(): array
    {
        $columns = [];
        $columnsAttributes = $this->columnsAttributes;
        $columnsLabelAttributes = $this->columnsLabelAttributes;

        if ($this->columnsLabelClass !== '') {
            CssClass::add($columnsLabelAttributes, $this->columnsLabelClass);
        }

        /** @psalm-var array[] $dataReader */
        $dataReader = $this->getIterator();

        foreach ($dataReader as $data) {
            /**
             * @var string $name
             * @var mixed $value
             */
            foreach ($data as $name => $value) {
                if (!in_array($name, $this->exceptColumns, true)) {
                    $label = $this->columnsLabel[$name] ?? $name;

                    $column = Column::create()
                        ->attributes($columnsAttributes)
                        ->label($label)
                        ->labelAttributes($columnsLabelAttributes)
                        ->name($name);

                    if (isset($this->columnsValue[$name])) {
                        $column = $column->value($this->columnsValue[$name]);
                    }

                    if (isset($this->sortParams[$name]) && is_array($this->sortParams[$name])) {
                        $column = $column->label(
                            $this->generateColumnSortLink($name, $this->sortParams[$name])
                        );
                    }

                    if (isset($this->columns[$name])) {
                        $columns[$name] = $this->columns[$name];
                    } else {
                        $columns[$name] = $column;
                    }
                }
            }
        }

        foreach ($this->columns as $name => $column) {
            $columns[$name] = $column;
        }

        return $columns;
    }

    /**
     * @return string The link header for the column sort.
     */
    private function generateColumnSortLink(string $column, array $sortParams = []): string
    {
        return TableSorter::widget()
            ->column($column)
            ->currentPage($this->page)
            ->linkClass('text-blue-500 hover:underline')
            ->pageSize($this->pageSize)
            ->sortParams($sortParams)
            ->urlPath($this->urlPath)
            ->urlQueryParameters($this->queryParams)
            ->render();
    }
}
