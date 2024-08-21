<?php

namespace OnTarget\Catalog\Models;

use Backend\Models\ImportModel;
use Exception;

class CategoryImport extends ImportModel
{
    public array $rules = [
        'id' => ['sometimes', 'integer', 'min:1', 'exists:OnTarget\Catalog\Models\Category,id'],
        'name' => ['required','string','min:2','max:250'],
        'slug' => ['sometimes','string','min:2','max:250'],
        'description' => ['sometimes','string','min:15','max:2500'],
        'is_active' => ['sometimes','integer','min:0','max:1'],
        'sort_order' => ['sometimes','integer','min:1'],
        'measure_unit_name' => ['sometimes','string'],
    ];

    /**
     * @inheritDoc
     */
    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {
            try {
                $category = Category::query()->findOrNew($data['id']);

                $exists = $category->exists;

                $category->name = $data['name'];
                $category->slug = $data['slug'] ?? str_slug($data['name']);
                $category->description = $data['description'] ?? '';
                $category->is_active = (bool)($data['is_active'] ?? 0);
                $category->parent_id = $this->getParentByPath($data['parent_path'])?->id;
                $measureUnitName = !empty($data['measure_unit_name']) ? $data['measure_unit_name'] : 'шт.';
                $category->measure_unit_id = $this->getMeasureUnitId($measureUnitName);

                $category->save();

                if($exists) {
                    $this->logUpdated();
                } else {
                    $this->logCreated();
                }

            }
            catch (Exception $ex) {
                trace_log($ex);
                $this->logError($row, $ex->getMessage());
            }

        }
    }

    /**
     * @param string $path
     * @param string $delimiter
     * @return Category|null
     */
    public function getParentByPath(string $path, string $delimiter = '|') : ?Category
    {
        $categories = explode($delimiter, $path);
        $parent = null;

        foreach ($categories as $categoryName) {
            if (!$parent) {
                $parent = Category::query()->where('name', $categoryName)->first();
            } else {
                $parent = $parent->children()->where('name', $categoryName)->first();
            }

            if (!$parent) {
                return null;
            }
        }

        return $parent;
    }

    /**
     * @param string $measureUnitName
     * @return int|null
     */
    public function getMeasureUnitId(string $measureUnitName) : ?int
    {
        return MeasureUnit::query()
            ->firstOrCreate(['name' => $measureUnitName], ['name' => $measureUnitName])
            ?->id;
    }
}
