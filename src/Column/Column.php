<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use Closure;
use PHPForge\Html\Tag;

use function array_key_exists;
use function call_user_func;
use function is_array;

/**
 * Implementation of the column for the table builder.
 */
final class Column extends AbstractColumn
{
    private mixed $value = null;

    /**
     * Returns a new instance specifying the data label.
     *
     * @param string $value The data label.
     */
    public function dataLabel(string $value): self
    {
        $new = clone $this;
        $new->attributes['data-label'] = $value;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDataCell(array|object $data, int|string $key): string
    {
        if ($data === []) {
            return '';
        }

        $attributes = $this->attributes;

        /**
         * @var string $name
         * @var mixed $value
         */
        foreach ($attributes as $name => $value) {
            if ($value instanceof Closure) {
                /** @var mixed */
                $attributes[$name] = $value($data, $key, $this);
            }
        }

        if (!array_key_exists("data-$key", $attributes) && $this->label !== '') {
            $attributes["data-$key"] = $this->name;
        }

        return Tag::create('td', $this->getDataCellValue($data, $key), $attributes);
    }

    /**
     * Returns a new instance specifying the value of the column.
     *
     * @param mixed $value The value of the column.
     */
    public function value(mixed $value): self
    {
        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * @param array|object $data The data associated with the row.
     * @param int|string $key The key associated with the row.
     *
     * @return string The data cell value.
     */
    private function getDataCellValue(array|object $data, int|string $key): string
    {
        if ($this->value instanceof Closure) {
            return (string) call_user_func($this->value, $data, $key, $this);
        }

        if ($this->value !== null) {
            return (string) $this->value;
        }

        $value = is_array($data) ? (string) $data[$this->name] : (string) $data->{$this->name};

        return $value ?: $this->emptyCell;
    }
}
