<?php namespace OnTarget\Catalog\Models;

use Backend\Models\ExportModel;

class CategoryExport extends ExportModel
{

    public function exportData($columns, $sessionKey = null)
    {
        return Category::query()
            ->with('measure_unit')
            ->get()
            ->toArray();
    }
}
