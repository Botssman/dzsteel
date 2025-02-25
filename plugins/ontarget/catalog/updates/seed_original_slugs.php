<?php namespace OnTarget\Catalog\Updates;

use OnTarget\Catalog\Models\PropertyValue;
use Seeder;
class SeedUsersTable extends Seeder
{
    public function run()
    {
        PropertyValue::all()->each(function (PropertyValue $value){
            $value->update([
                'original_slug' => str_slug($value->name)
            ]);
        });
    }
}
