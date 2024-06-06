<?php namespace OnTarget\Catalog\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use OnTarget\Catalog\Models\Property;
use OnTarget\Catalog\Models\PropertyValue;

/**
 * Properties Form Widget
 *
 * @link https://docs.octobercms.com/3.x/extend/forms/form-widgets.html
 */
class Properties extends FormWidgetBase
{
    protected $defaultAlias = 'catalog_properties';

    public function init()
    {
    }

    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('properties');
    }

    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;

        $this->vars['properties'] = $this->model?->category?->properties;
    }

    public function loadAssets()
    {
        $this->addCss('css/properties.css');
        $this->addJs('js/properties.js');
    }

    public function getSaveValue($value)
    {
        return $value;
    }

    public function createFormWidget(Property $property, string $type = 'dropdown')
    {
        return match($type) {
            default => $this->dropdown($property)
        };
    }

    public function dropdown(Property $property)
    {
        $formField = $this->newFormField($property);
        $formField->value = $this->model->property_values()
            ->where('property_id',$property->id)
            ->first()
            ?->id;
        $formField->label = $property->name;
        $formField->type = 'dropdown';
        $formField->span = 'auto';
        $formField->emptyOption = "- Выберите значение -";
        $formField->options = $property->values
            ->mapWithKeys(fn($v) => [$v->id => $v->name])
            ->toArray();

        $widget = $this->makePartial(
            sprintf('~/modules/backend/widgets/form/partials/%s', '_field_dropdown'),
            ['field' => $formField, 'value' => '']
        );

        return $this->makePartial('dropdown', ['widget' => $widget, 'field' => $property]);
    }

    protected function newFormField($property, $subkey = null): FormField
    {
        $subkey    = $subkey ? '[' . $subkey . ']' : '';
        $fieldName = sprintf('%s[%s]%s', $this->fieldPrefix(), $property->id, $subkey);

        return new FormField($fieldName, $property->name);
    }

    public function fieldPrefix(): string
    {
        return $this->formField->config['fieldPrefix'] ?? 'PropertyValues';
    }
}
