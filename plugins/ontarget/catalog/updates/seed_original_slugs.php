<?php namespace OnTarget\Catalog\Updates;

use OnTarget\Catalog\Models\PropertyValue;
use Seeder;
use Str;

class SeedUsersTable extends Seeder
{
    public function run()
    {
        PropertyValue::chunk(200, function ($values) {
            foreach ($values as $value) {
                $value->update([
                    'original_slug' => Str::slug($value->name)
                ]);
            }
        });
    }
}
