<?php

namespace OnTarget\Catalog\Classes\Events;

use Backend\Widgets\Form;
use OnTarget\Catalog\Behaviors\ImportBehavior;
use OnTarget\Catalog\Models\ProductExport;
use OnTarget\Catalog\Models\ProductImport;
use October\Rain\Events\Dispatcher;

class ImportExportHandler
{
    public function subscribe(Dispatcher $dispatcher): void
    {
        $dispatcher->listen('backend.form.extendFields', [self::class, 'extendForms']);
    }

    public function extendForms(Form $widget): void
    {
        if (!$widget->getController()->implement) {
            return;
        }

        if (!in_array(
            ImportBehavior::class,
            $widget->getController()->implement)
        ) {
            return;
        }

        if ($widget->model instanceof ProductImport) {
            $widget->addFields([
                'category_id' => [
                    'label' =>'Категория импорта',
                    'type' => 'dropdown',
                    'order' => 100,
                    'emptyOption' => '- Выберите категорию -',
                    'required' => 'true'
                ],
                'import_file' => [
                    'label'      => 'Файл импорта',
                    'type'       => 'fileupload',
                    'mode'       => 'file',
                    'span'       => 'full',
                    'fileTypes'  => ['xlsx'],
                    'useCaption' => false,
                ],
                'file_format' => [
                    'label'   => 'File Format',
                    'type'    => 'dropdown',
                    'options' => [
                        'csv' => 'CSV/Excel'
                    ],
                    'default' => 'csv',
                    'span'    => 'right',
                    'cssClass' => 'hidden'
                ],
                'column_matcher' => [
                    'type' => 'partial',
                    'path' => '~/modules/backend/behaviors/importexportcontroller/partials/_import_column_matcher.php',
                    'dependsOn' => ['category_id','import_file', 'file_format', 'first_row_titles', 'format_delimiter', 'format_enclosure', 'format_escape', 'format_encoding']
                ]
            ]);
        }

        if ($widget->model instanceof ProductExport) {
            $widget->addFields([
                'category_id' => [
                    'label' =>'Категория экспорта',
                    'type' => 'dropdown',
                    'order' => 100,
                    'emptyOption' => '- Выберите категорию -',
                    'required' => 'true'
                ],
                'file_format' => [
                    'label' => 'Выходной формат',
                    'type' => 'dropdown',
                    'default' => 'xlsx',
                    'options' => [
                        'xlsx' => 'Excel (.xlsx)',
                        'json' => 'JSON',
                        'csv' => 'CSV',
                        'csv_custom' => 'Пользовательский CSV'
                    ]
                ],
                'step2_section' => [
                    'type' => 'section',
                    'label' => '2. Select columns to export',
                    'trigger' => [
                        'action' => 'hide',
                        'field' => 'category_id',
                        'condition' => 'value[]'
                    ]
                ],
                'export_columns' => [
                    'type' => 'partial',
                    'path' => '~/modules/backend/behaviors/importexportcontroller/partials/_export_columns.php',
                    'span' => 'left',
                    'dependsOn' => [
                        'file_format',
                        'category_id'
                    ],
                    'trigger' => [
                        'action' => 'hide',
                        'field' => 'category_id',
                        'condition' => 'value[]'
                    ]
                ]
            ]);
        }

    }
}
