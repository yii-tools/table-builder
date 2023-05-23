<?php

declare(strict_types=1);

namespace Yii\TableBuilder\Column;

use Yii\Html\Tag;
use Yii\TableBuilder\Column\Enum\DataAttribute;

use function array_key_exists;

final class CrudColumn extends AbstractColumn
{
    /** @psalm-var string[] */
    private array $actions = [
        'delete' => 'delete',
        'update' => 'update',
        'view' => 'view',
    ];
    /** @psalm-var string[][] */
    private array $actionsAttributes = [];
    private array $buttons = [];
    private array $contentAttributes = [];
    private string $primaryKey = 'id';
    private string $urlPath = '';

    /** @psalm-param string[] $value */
    public function actions(array $value): self
    {
        $new = clone $this;
        $new->actions = $value;

        return $new;
    }

    /** @psalm-param string[][] $values */
    public function actionsAttributes(array $values): self
    {
        $new = clone $this;
        $new->actionsAttributes = $values;

        return $new;
    }

    public function addActionClass(string $action, string $value): self
    {
        $new = clone $this;
        $new->actionsAttributes[$action]['class'] = $value;

        return $new;
    }

    public function addButtonColumn(string $name, ButtonColumn $buttonColumn): self
    {
        $new = clone $this;
        $new->buttons[$name] = $buttonColumn;

        return $new;
    }

    public function addDataAttribute(string $action, DataAttribute $dataAttribute, string $value): self
    {
        $new = clone $this;
        $new->actionsAttributes[$action][$dataAttribute->value] = $value;

        return $new;
    }

    public function primaryKey(string $value): self
    {
        $new = clone $this;
        $new->primaryKey = $value;

        return $new;
    }

    public function urlPath(string $value): self
    {
        $new = clone $this;
        $new->urlPath = $value;

        return $new;
    }

    public function renderDataCell(array|object $data, int|string $key): string
    {
        /** @psalm-var ButtonColumn[] $buttons */
        $buttons = $this->loadDefaultButtons();

        if ($data === []) {
            return '';
        }

        $content = '';

        foreach ($buttons as $name => $button) {
            if (isset($this->actions[$name])) {
                $primaryKeyData = (string) (is_array($data) ? $data[$this->primaryKey] : $data->{$this->primaryKey});
                $button = $button->href($this->urlPath . '/' . $this->actions[$name] . '/' . $primaryKeyData);
                $content .= $button->renderDataCell($data, $key, false);
            }
        }

        return $content ? Tag::create('td', $content) : '';
    }

    public static function create(): static
    {
        return new self();
    }

    private function getActionsAttributes(string $action): array
    {
        return $this->actionsAttributes[$action] ?? [];
    }

    private function loadDefaultButtons(): array
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
            if (array_key_exists($action, $defaultButtons)) {
                $buttons[$action] = $defaultButtons[$action];
            }
        }

        return $buttons;
    }
}
