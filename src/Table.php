<?php

declare(strict_types=1);

namespace Yii\TableBuilder;

use Yii\Html\Tag;
use Yii\TableBuilder\Column\AbstractColumn;
use Yii\Widget\AbstractWidget;
use Yii\Widget\Attribute;

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

    public function __construct(private readonly TableConfigurationInterface $configurator)
    {
    }

    public function canBeShowFooter(bool $value): self
    {
        $new = clone $this;
        $new->canBeShowFooter = $value;

        return $new;
    }

    public function emptyText(string $value): self
    {
        $new = clone $this;
        $new->emptyText = $value;

        return $new;
    }

    public function headerAttributes(array $value): self
    {
        $new = clone $this;
        $new->headerAttributes = $value;

        return $new;
    }

    public function rowAttributes(array $value): self
    {
        $new = clone $this;
        $new->rowAttributes = $value;

        return $new;
    }

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
