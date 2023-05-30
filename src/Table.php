<?php

declare(strict_types=1);

namespace Yii\TableBuilder;

use Yii\Html\Tag;
use Yii\TableBuilder\Column\AbstractColumn;
use Yii\Widget\AbstractWidget;
use Yii\Widget\Attribute;

/**
 * Represents a table widget used for building HTML tables.
 */
final class Table extends AbstractWidget
{
    use Attribute\Custom\HasAttributes;

    private array $attributes = [];
    private bool $canBeShowFooter = false;
    private int $count = 0;
    private string $emptyText = 'empty table';
    private array $headerAttributes = [];
    private string $layout = '{table}' . PHP_EOL . '{pagination}';
    private array $rowAttributes = [];
    private array $rowHeaderAttributes = [];
    private array $rowFooterAttributes = [];
    private string $toolbar = '';

    public function __construct(private readonly TableConfigurationInterface $configurator, array $definitions = [])
    {
        parent::__construct($definitions);
    }

    /**
     * Returns a new instance specifying when the table footer can be shown.
     *
     * @param bool $value Whether the table footer can be shown.
     */
    public function canBeShowFooter(bool $value): self
    {
        $new = clone $this;
        $new->canBeShowFooter = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the empty table text.
     *
     * @param string $value The empty table text.
     */
    public function emptyText(string $value): self
    {
        $new = clone $this;
        $new->emptyText = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the header `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function headerAttributes(array $values): self
    {
        $new = clone $this;
        $new->headerAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the row `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function rowAttributes(array $values): self
    {
        $new = clone $this;
        $new->rowAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the toolbar for the table.
     *
     * @param string $value The toolbar for the table.
     */
    public function toolbar(string $value): self
    {
        $new = clone $this;
        $new->toolbar = $value;

        return $new;
    }

    protected function run(): string
    {
        return trim(
            strtr(
                $this->layout,
                [
                    '{table}' => $this->renderItems(),
                    '{pagination}' => $this->configurator->getPagination(),
                ],
            )
        );
    }

    /**
     * @return array The data for the table.
     */
    private function getData(): array
    {
        $data = [];

        /** @psalm-var mixed $reader */
        foreach ($this->configurator->getIterator() as $reader) {
            /** @psalm-var mixed */
            $data[] = $reader;
        }

        $this->count = count($data);

        return $data;
    }

    /**
     * Render items for the table.
     */
    private function renderItems(): string
    {
        $columns = $this->configurator->getColumns();

        $content = array_filter([
            $this->toolbar,
            $this->renderTableBody($columns),
            $this->renderTableHeader($columns),
            $this->renderTableFooter($columns),
        ]);

        return Tag::create('table', implode(PHP_EOL, $content), $this->attributes);
    }

    /**
     * Render a table body with the given columns.
     *
     * @psalm-param AbstractColumn[] $columns
     */
    private function renderTableBody(array $columns): string
    {
        $data = $this->getData();
        $rows = [];
        $rowAttributes = $this->rowAttributes;

        /** @psalm-var array<int,array|object> $data */
        foreach ($data as $index => $row) {
            $rows[] = $this->renderTableRow($columns, $row, $index);
        }

        $emptyRows = $this->configurator->getPageSize() - $this->count;

        if ($emptyRows > 0) {
            foreach (range(1, $emptyRows) as $ignored) {
                $emptyCells = [];

                foreach ($columns as $ignored1) {
                    $emptyCells[] = Tag::create('td');
                }

                $rows[] = Tag::create('tr', implode(PHP_EOL, $emptyCells), $rowAttributes);
            }
        }

        if ($rows === [] && $this->emptyText !== '') {
            $colspan = count($columns);

            $rowAttributes['colspan'] = $colspan;

            return
                Tag::begin('tbody') .
                    Tag::create('tr', Tag::create('td', $this->emptyText, $rowAttributes)) .
                Tag::end('tbody');
        }

        /** @psalm-var array<array-key,string> $rows */
        return Tag::create('tbody', implode(PHP_EOL, $rows));
    }

    /**
     * Render a table footer with the given columns.
     *
     * @psalm-param AbstractColumn[] $columns
     */
    private function renderTableFooter(array $columns): string
    {
        $cells = [];

        if ($this->canBeShowFooter === false) {
            return '';
        }

        foreach ($columns as $column) {
            $renderFooterCell = $column->renderFooterCell();

            if ($renderFooterCell !== '') {
                $cells[] = $renderFooterCell;
            }
        }

        if ($cells === []) {
            return '';
        }

        $content = Tag::create('tr', implode(PHP_EOL, $cells), $this->rowFooterAttributes);

        return Tag::create('tfoot', $content);
    }

    /**
     * Render a table header with the given columns.
     *
     * @psalm-param AbstractColumn[] $columns
     */
    private function renderTableHeader(array $columns): string
    {
        $cell = [];
        $cells = '';

        foreach ($columns as $column) {
            $cell[] = $column->renderHeaderCell();
        }

        if ($cell !== []) {
            $cells = implode(PHP_EOL, $cell);
        }

        $content = Tag::create('tr', $cells, $this->rowHeaderAttributes);

        return Tag::create('thead', $content, $this->headerAttributes);
    }

    /**
     * Render a table row with the given columns.
     *
     * @psalm-param AbstractColumn[] $columns
     */
    private function renderTableRow(array $columns, array|object $data, int $index): string
    {
        $cells = [];
        $content = '';

        foreach ($columns as $key => $column) {
            if ($column->isVisible()) {
                $cells[] = $column->renderDataCell($data, $key);
            }
        }

        if ($cells !== []) {
            $content = implode(PHP_EOL, $cells);
        }

        return Tag::create('tr', $content, $this->rowAttributes);
    }
}
