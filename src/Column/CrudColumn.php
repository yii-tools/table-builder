<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use PHPForge\Html\Tag;
use Yii\TableBuilder\Column\Enum\DataAttribute;

use function array_key_exists;
use function in_array;

/**
 * Implementation of the CRUD column for the table builder.
 */
final class CrudColumn extends AbstractColumn
{
    /** @psalm-var string[] */
    private array $actions = ['delete', 'update'];
    /** @psalm-var string[][] */
    private array $actionsAttributes = [];
    private array $buttons = [];
    private string $primaryKey = 'id';
    private string $urlPath = '';

    /**
     * Returns a new instance specifying the actions of the CRUD column.
     *
     * @param array $value The actions of the CRUD column.
     *
     * @psalm-param string[] $value
     */
    public function actions(array $value): self
    {
        $new = clone $this;
        $new->actions = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the crud columns `HTML` attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @psalm-param string[][] $values
     */
    public function actionsAttributes(array $values): self
    {
        $new = clone $this;
        $new->actionsAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance by add the class attribute for the specified action.
     *
     * @param string $action The action name.
     * @param string $value The class attribute value.
     */
    public function addActionClass(string $action, string $value): self
    {
        $new = clone $this;
        $new->actionsAttributes[$action]['class'] = $value;

        return $new;
    }

    /**
     * Returns a new instance by add custom button column for the specified action.
     *
     * @param string $name The name of the button column.
     * @param ButtonColumn $buttonColumn The button column.
     */
    public function addButtonColumn(string $name, ButtonColumn $buttonColumn): self
    {
        $new = clone $this;
        $new->buttons[$name] = $buttonColumn;

        return $new;
    }

    /**
     * Returns a new instance by add the data attribute for the specified action.
     *
     * @param string $action The action name.
     * @param DataAttribute $dataAttribute The data attribute.
     * @param string $value The data attribute value.
     */
    public function addDataAttribute(string $action, DataAttribute $dataAttribute, string $value): self
    {
        $new = clone $this;
        $new->actionsAttributes[$action][$dataAttribute->value] = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the buttons of the CRUD column.
     *
     * @param array $values The buttons of the CRUD column. The key is the name of the button and the value is the button
     * column.
     */
    public function buttons(array $values): self
    {
        $new = clone $this;
        $new->buttons = $values;

        return $new;
    }

    /**
     * Returns a new instance by add the primary key attribute for actions of the CRUD column.
     *
     * @param string $action The primary key attribute.
     */
    public function primaryKey(string $value): self
    {
        $new = clone $this;
        $new->primaryKey = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the url path of the CRUD column.
     *
     * @param string $value The url path of the CRUD column.
     */
    public function urlPath(string $value): self
    {
        $new = clone $this;
        $new->urlPath = $value;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDataCell(array|object $data, int|string $key): string
    {
        $attributes = $this->attributes;

        /** @psalm-var ButtonColumn[] $buttons */
        $buttons = $this->generateButtons();

        if ($data === []) {
            return '';
        }

        $content = '';

        foreach ($buttons as $name => $button) {
            if (in_array($name, $this->actions, true)) {
                $primaryKeyData = (string) (is_array($data) ? $data[$this->primaryKey] : $data->{$this->primaryKey});
                if ($button->getType() === 'link' && $button->getHref() === '') {
                    $button = $button->href($this->urlPath . '/' . $name . '/' . $primaryKeyData);
                }
                $content .= $button->renderDataCell($data, $key, false);
            }
        }

        return $content ? Tag::create('td', $content, $attributes) : '';
    }

    /**
     * @return array The actions attributes.
     */
    private function getActionsAttributes(string $action): array
    {
        return $this->actionsAttributes[$action] ?? [];
    }

    /**
     * @return array The generated buttons for the CRUD column.
     */
    private function generateButtons(): array
    {
        $buttons = $this->buttons;

        /** @psalm-var ButtonColumn[] $defaultButtons */
        $defaultButtons = [
            'delete' => ButtonColumn::create()
                ->content(Tag::create('span', '🗑'), false)
                ->contentAttributes($this->getActionsAttributes('delete'))
                ->label('delete')
                ->type('link'),
            'update' => ButtonColumn::create()
                ->content(Tag::create('span', '✎'), false)
                ->contentAttributes($this->getActionsAttributes('update'))
                ->label('update')
                ->type('link'),
            'view' => ButtonColumn::create()
                ->content(Tag::create('span', '🔎'), false)
                ->contentAttributes($this->getActionsAttributes('view'))
                ->label('view')
                ->type('link'),
        ];

        foreach ($this->actions as $action) {
            if (array_key_exists($action, $defaultButtons) && !array_key_exists($action, $buttons)) {
                $buttons[$action] = $defaultButtons[$action];
            }
        }

        return $buttons;
    }
}
