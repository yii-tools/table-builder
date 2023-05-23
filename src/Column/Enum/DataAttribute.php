<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column\Enum;

/**
 * Enumeration of the data attributes.
 */
enum DataAttribute: string
{
    case ACTION = 'data-action';
    case CANCEL_TEXT = 'data-cancel-text';
    case CONFIRM_TEXT = 'data-confirm-text';
    case ICON = 'data-icon';
    case METHOD = 'data-method';
    case MESSAGE = 'data-message';
    case TITLE = 'data-title';
}
