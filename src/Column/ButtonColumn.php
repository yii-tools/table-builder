<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use Closure;
use PHPForge\Html\Helper\CssClass;
use PHPForge\Html\Helper\Encode;
use PHPForge\Html\Tag;
use PHPForge\Widget\Attribute;
use Stringable;
use Yii\TableBuilder\Column\Enum\DataAttribute;

use function array_merge;
use function is_bool;
use function is_callable;
use function is_string;

/**
 * Implementation of the button column for the table builder.
 */
final class ButtonColumn extends AbstractColumn
{
    use Attribute\Custom\HasData;
    use Attribute\HasId;
    use Attribute\Input\HasType;

    private string|Closure|Stringable $content = '';
    private array $contentAttributes = [];
    private string|Closure $contentClass = '';
    private string|Closure $href = '';

    /**
     * Returns a new instance specifying the data attribute.
     *
     * @param DataAttribute $dataAttribute The data attribute.
     * @param mixed $value The value of the data attribute.
     */
    public function addDataAttribute(DataAttribute $dataAttribute, mixed $value): self
    {
        $new = clone $this;
        $new->contentAttributes[$dataAttribute->value] = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the content of the button column.
     *
     * @param Closure|string|Stringable $value The content of the button column.
     * @param bool $encode Whether to encode the content value.
     */
    public function content(string|Closure|Stringable $value, bool $encode = true): self
    {
        if (is_string($value) && $encode === true) {
            $value = Encode::content($value);
        }

        $new = clone $this;
        $new->content = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the content `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function contentAttributes(array $values): self
    {
        $new = clone $this;
        $new->contentAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the `CSS` `HTML` class attribute of the button column.
     *
     * @param Closure|string $value The `CSS` attribute of the button column.
     *
     * @link https://html.spec.whatwg.org/#classes
     */
    public function contentClass(string|Closure $value): self
    {
        $new = clone $this;
        $new->contentClass = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the data attributes `HTML` attributes of the button column.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function dataAttributes(array $values): self
    {
        $new = clone $this;
        $new->contentAttributes = array_merge($values, $new->contentAttributes);

        return $new;
    }

    /**
     * @return Closure|string The href attribute of the button column.
     */
    public function getHref(): string|Closure
    {
        return $this->href;
    }

    /**
     * @return string The type attribute of the button column.
     */
    public function getType(): string
    {
        return isset($this->contentAttributes['type']) && is_string($this->contentAttributes['type'])
            ? $this->contentAttributes['type']
            : 'link';
    }

    /**
     * Returns a new instance specifying the URL that the hyperlink points to.
     *
     * Links aren't restricted to HTTP-based URLs they can use any URL scheme supported by browsers.
     *
     * @param Closure|string $value The URL that the hyperlink points to.
     *
     * @link https://html.spec.whatwg.org/multipage/links.html#ping
     */
    public function href(string|Closure $value): self
    {
        $new = clone $this;
        $new->href = $value;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDataCell(array|object $data, int|string $key, bool $container = true): string
    {
        return match ($container) {
            true => Tag::create('td', $this->getDataCellContent($data, $key), $this->attributes),
            default => $this->getDataCellContent($data, $key),
        };
    }

    /**
     * Returns a new instance specifying a string specifying the type of control to render.
     *
     * For example, to create a checkbox, a value of checkbox is used.
     *
     * If omitted (or an unknown value is specified), the input type text is used, creating a plaintext input field.
     *
     * @param string $value The type of control to render.
     *
     * @link https://html.spec.whatwg.org/multipage/input.html#attr-input-type
     */
    public function type(string $value): self
    {
        $new = clone $this;
        $new->contentAttributes['type'] = $value;

        return $new;
    }

    /**
     * @param array|object $data The data associated with the row.
     * @param int|string $key The key associated with the row.
     *
     * @return string The content of the data cell.
     */
    private function getDataCellContent(array|object $data, int|string $key): string
    {
        $contentAttributes = $this->contentAttributes;
        $class = $this->contentClass;
        $content = $this->content !== '' ? $this->content : $this->emptyCell;
        $contentClass = $this->contentClass;
        $href = $this->href;
        $type = 'link';

        if (isset($contentAttributes['type']) && is_string($contentAttributes['type'])) {
            $type = $contentAttributes['type'];
        }

        $contentAttributes['type'] = $type;

        if ($content instanceof Stringable) {
            $content = $content->__toString();
        }

        if (is_callable($class)) {
            $class = (string) $contentClass($data, $key, $this);
        }

        if (is_callable($content)) {
            $content = (string) $content($data, $key, $this);
        }

        if (is_callable($href)) {
            $href = (string) $href($data, $key, $this);
        }

        if ($this->href !== '') {
            $contentAttributes['href'] = $href;
        }

        /** @psalm-var array<string, string|closure> $contentAttributes */
        foreach ($contentAttributes as $name => $value) {
            if ($value instanceof Closure) {
                $contentAttributes[$name] = (string) $value($data, $key, $this);
            }
        }

        CssClass::add($contentAttributes, $class);

        return match ($type) {
            'link' => $this->renderButtonLink($content, $contentAttributes),
            default => $this->renderButton($content, $contentAttributes),
        };
    }

    /**
     * @param string $content The content of the button.
     * @param array $contentAttributes The content `HTML` attributes.
     *
     * @return string The rendered button.
     */
    private function renderButton(string $content, array $contentAttributes): string
    {
        return Tag::create('button', $content, $contentAttributes);
    }

    /**
     * @param string $content The content of the button.
     * @param array $contentAttributes The content `HTML` attributes.
     *
     * @return string The rendered button link.
     */
    private function renderButtonLink(string $content, array $contentAttributes): string
    {
        unset($contentAttributes['type']);

        $contentAttributes['role'] = 'button';

        if (
            isset($contentAttributes['disabled']) &&
            is_bool($contentAttributes['disabled']) &&
            $contentAttributes['disabled'] === true
        ) {
            CssClass::add($contentAttributes, 'disabled');
            $contentAttributes['aria-disabled'] = 'true';

            unset($contentAttributes['disabled']);
        }

        return Tag::create('a', $content, $contentAttributes);
    }
}
