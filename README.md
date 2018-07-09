# ZFc Form Module

Features:
- registered `AbstractFactory` which allow create most of `Form` without factory declaration;
- auto resolving for `DoctrineHydrator` and `Translator`;
- twitter bootstrap template rendering (only html template without core override);
- save `Form` with Ajax;
- Add/Remove buttons for dynamic elements.

## Usage

Form can be rendered in template as `<?= $this->partial('form::form', ['form' => $form]) ?>`.
Or in action as `return (new ViewModel(['form' => $form])->setTemplate('form::form')`.

## Custom options
- **inline**

This option will try to render $fieldset's elements inline one by one with `col-sm-*` class. 

```php
$fieldset->add([
    'name' => $name,
    'type' => Fieldset::class,
    'options' => [
        'inline' => true,
    ],
]);
```

- **column**

This option will take number of columns for element.
```php
$element = $fieldset->add([
    'name' => 'value',
    'type' => 'text',
    'options' => [
        'column' => 6,
    ],
]);
```
