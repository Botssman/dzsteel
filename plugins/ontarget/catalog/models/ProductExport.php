<?php

namespace OnTarget\Catalog\Models;

use Backend\Models\ExportModel;
use OnTarget\Catalog\Classes\Scopes\ActiveScope;
use OnTarget\Catalog\Classes\Traits\ExcelExport;
use OnTarget\Catalog\Classes\Traits\ExcelProcessor;

class ProductExport extends ExportModel
{
    use ExcelExport;
    use ExcelProcessor;

    /**
     * @inheritDoc
     */
    public function exportData($columns, $sessionKey = null)
    {
        // TODO: Implement exportData() method.
    }

    public function getCategoryIdOptions()
    {
        return Category::query()
            ->tap(fn() => new ActiveScope)
            ->pluck( 'name', 'id');
    }
}
