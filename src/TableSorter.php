<?php

declare(strict_types=1);

namespace Yii\TableBuilder;

use InvalidArgumentException;
use PHPForge\Html\Helper\CssClass;
use PHPForge\Html\Tag;
use PHPForge\Widget\AbstractWidget;
use Yiisoft\Strings\Inflector;

use function array_merge;
use function str_starts_with;

/**
 * Renders a list of sort links for the given sort definition.
 *
 * It generates hyperlinks for each attribute declared in the sort definition.
 */
final class TableSorter extends AbstractWidget
{
    private string $column = '';
    private int $currentPage = 1;
    private array $linkAttributes = [];
    private string $pageName = 'page';
    private int $pageSize = 10;
    private string $pageSizeName = 'page-size';
    private string $separator = ',';
    private string $sortClassAsc = 'asc';
    private string $sortClassDesc = 'desc';
    private string $sortParamName = 'sort';
    private array $sortParams = [];
    private array $urlQueryParameters = [];
    private string $urlPath = '';

    /**
     * Return a new instance specifying the column.
     *
     * @param string $value The column name.
     */
    public function column(string $value): self
    {
        $new = clone $this;
        $new->column = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the current page.
     *
     * @param int $value The current page.
     */
    public function currentPage(int $value): self
    {
        $new = clone $this;
        $new->currentPage = $value;

        return $new;
    }

    /**
     * Returns a new instance specifying the `HTML` attributes for link attributes `<a>`.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function linkAttributes(array $values): self
    {
        $new = clone $this;
        $new->linkAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance specifying the `HTML` class for link attributes `<a>`.
     *
     * @param string $value The CSS class for link attributes `<a>`.
     */
    public function linkClass(string $value): self
    {
        $new = clone $this;
        CssClass::add($new->linkAttributes, $value);

        return $new;
    }

    /**
     * Return a new instance specifying the name of query parameter for page.
     *
     * @param string $value The name of query parameter for page.
     */
    public function pageName(string $value): static
    {
        $new = clone $this;
        $new->pageName = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the page size.
     *
     * @param int $value The page size.
     */
    public function pageSize(int $value): static
    {
        $new = clone $this;
        $new->pageSize = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the name of query parameter for page size.
     *
     * @param string $value The name of query parameter for page size.
     */
    public function pageSizeName(string $value): static
    {
        $new = clone $this;
        $new->pageSizeName = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the separator.
     *
     * @param string $value The separator.
     */
    public function separator(string $value): static
    {
        $new = clone $this;
        $new->separator = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the CSS class for an ascending sorter link when the current sort matches the
     * specified attribute.
     *
     * @param string $value The CSS class for an ascending sorter link.
     */
    public function sortClassAsc(string $value): self
    {
        $new = clone $this;
        $new->sortClassAsc = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the CSS class for a descending sorter link when the current sort matches the
     * specified attribute.
     *
     * @param string $value The CSS class for a descending sorter link.
     */
    public function sortClassDesc(string $value): self
    {
        $new = clone $this;
        $new->sortClassDesc = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the name of query parameter for sort.
     *
     * @param string $value The name of query parameter for sort.
     */
    public function sortParamName(string $value): self
    {
        $new = clone $this;
        $new->sortParamName = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the sorter parameters.
     *
     * @param array $values The sorter parameters
     */
    public function sortParams(array $values): self
    {
        $new = clone $this;
        $new->sortParams = $values;

        return $new;
    }

    /**
     * Return a new instance specifying the url query parameters.
     *
     * @param array $value The query parameters of the route.
     */
    public function urlQueryParameters(array $value): self
    {
        $new = clone $this;
        $new->urlQueryParameters = $value;

        return $new;
    }

    /**
     * Return a new instance specifying the url path.
     *
     * @param string $value The url path.
     */
    public function urlPath(string $value): self
    {
        $new = clone $this;
        $new->urlPath = $value;

        return $new;
    }

    /**
     * Executes the widget.
     *
     * This method renders the sort links.
     *
     * @throws InvalidArgumentException If the configuration is invalid.
     *
     * @return string The rendered sort links.
     */
    protected function run(): string
    {
        return $this->renderSorterLink();
    }

    /**
     * Creates a URL for sorting the data by the specified attribute.
     *
     * This method will consider the current sorting status given by {@see attributeOrders}.
     *
     * For example, if the current page already sorts the data by the specified attribute in ascending order,
     * then the URL created will lead to a page that sorts the data by the specified attribute in descending order.
     *
     * @throws InvalidArgumentException if the attribute is unknown
     *
     * @return string the URL for sorting. False if the attribute is invalid.
     *
     * {@see attributeOrders}
     * {@see params}
     */
    private function createUrl(): string
    {
        return "$this->urlPath?" . http_build_query(
            array_merge(
                $this->sortParams,
                [$this->pageName => $this->currentPage],
                [$this->pageSizeName => $this->pageSize],
            )
        );
    }

    /**
     * Generates a hyperlink that links to the sort action to sort by the specified attribute.
     *
     * Based on the sort direction, the CSS class of the generated hyperlink will be appended with "asc" or "desc".
     *
     * @throws InvalidArgumentException If the column is unknown.
     *
     * @return string The generated hyperlink.
     */
    private function renderSorterLink(): string
    {
        $linkAttributes = $this->linkAttributes;
        /** @psalm-var string[] $sortParams */
        $sortParams = $this->sortParams;
        $sortParam = $sortParams[$this->sortParamName] ?? '';

        if ($this->urlQueryParameters !== []) {
            str_starts_with($sortParam, '-')
                ? CssClass::add($linkAttributes, $this->sortClassAsc)
                : CssClass::add($linkAttributes, $this->sortClassDesc);
        }

        $linkAttributes['data-sort'] = implode($this->separator, $sortParams);
        $linkAttributes['href'] = $this->createUrl();

        $inflector = new Inflector();
        $label = $inflector->toHumanReadable($this->column);

        return Tag::create('a', $label, $linkAttributes);
    }
}
