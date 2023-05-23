<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use Closure;
use Yii\Html\Tag;

use function array_key_exists;
use function call_user_func;
use function is_array;

final class Column extends AbstractColumn
{
    private mixed $value = null;

    public function dataLabel(string $value): self
    {
        $new = clone $this;
        $new->attributes['data-label'] = $value;

        return $new;
    }

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

        if (!array_key_exists('data-label', $attributes) && $this->label !== '') {
            $attributes['data-label'] = $this->name;
        }

        return Tag::create('td', $this->getDataCellValue($data, $key), $attributes);
    }

    public function value(mixed $value): self
    {
        $new = clone $this;
        $new->value = $value;

        return $new;
    }

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
