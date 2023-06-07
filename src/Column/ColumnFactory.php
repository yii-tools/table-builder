<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use function call_user_func_array;
use function str_ends_with;
use function substr;

final class ColumnFactory
{
    public static function factory(AbstractColumn $column): AbstractColumn
    {
        /** @var array<string, mixed> $definitions */
        $definitions = $column->definitions;

        /** @var mixed $arguments */
        foreach ($definitions as $action => $arguments) {
            if (str_ends_with($action, '()')) {
                /** @var mixed $setter */
                $setter = call_user_func_array([$column, substr($action, 0, -2)], $arguments);

                if ($setter instanceof $column) {
                    /** @var AbstractColumn $column */
                    $column = $setter;
                }
            }
        }

        return $column;
    }
}
