<?php

declare(strict_types=1);

namespace Yii\TableBuilder;

use IteratorAggregate;
use Traversable;
use Yii\Html\Helper\CssClass;
use Yii\TableBuilder\Column\AbstractColumn;
use Yii\TableBuilder\Column\Column;

final class TableConfiguration implements TableConfigurationInterface
{
    protected array $attributes = [];
    protected array $queryParams = [];
    protected string $urlPath = '';
    /** @psalm-var AbstractColumn[] */
    private array $columns = [];
    /** @psalm-var string[] */
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

    public function addColumn(string $name, AbstractColumn $column): self
    {
        $new = clone $this;
        $new->columns[$name] = $column;

        return $new;
    }

    /**
     * @psalm-param string[] $values
     */
    public function addColumnLabel(string $name, string $value): self
    {
        $new = clone $this;
        $new->columnsLabel[$name] = $value;

        return $new;
    }

    public function addColumnValue(string $name, mixed $value): self
    {
        $new = clone $this;
        $new->columnsValue[$name] = $value;

        return $new;
    }

    public function columnsAttributes(array $values): self
    {
        $new = clone $this;
        $new->columnsAttributes = $values;

        return $new;
    }

    public function columnsClass(string $value): self
    {
        $new = clone $this;
        CssClass::add($new->columnsAttributes, $value);

        return $new;
    }

    public function columnsLabelClass(string $value): self
    {
        $new = clone $this;
        $new->columnsLabelClass = $value;

        return $new;
    }

    public function exceptColumns(string ...$values): self
    {
        $new = clone $this;
        $new->exceptColumns = $values;

        return $new;
    }

    /**
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

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPagination(): string
    {
        return $this->pagination;
    }

    public function pagination(string $value): self
    {
        $new = clone $this;
        $new->pagination = $value;

        return $new;
    }

    public function queryParams(array $value): self
    {
        $new = clone $this;
        $new->queryParams = $value;

        return $new;
    }

    public function sortParams(array $value): self
    {
        $new = clone $this;
        $new->sortParams = $value;

        return $new;
    }

    public function urlPath(string $value): self
    {
        $new = clone $this;
        $new->urlPath = $value;

        return $new;
    }

    /**
     * This function tries to guess the columns to show from the given data if {@see columns} aren't explicitly
     * specified.
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
