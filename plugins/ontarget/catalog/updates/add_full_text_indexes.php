<?php namespace OnTarget\Catalog\Updates;

use Db;
use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {

        DB::statement('
            ALTER TABLE ontarget_catalog_products
            ADD COLUMN IF NOT EXISTS search_vector tsvector
            GENERATED ALWAYS AS (
                to_tsvector(\'russian\', coalesce(name, \'\')) ||
                to_tsvector(\'russian\', coalesce(slug, \'\')) ||
                to_tsvector(\'russian\', coalesce(vendor_code, \'\'))
            ) STORED
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS products_search_vector_idx
            ON ontarget_catalog_products
            USING GIN (search_vector)
        ');


        DB::statement('
            ALTER TABLE ontarget_catalog_categories
            ADD COLUMN IF NOT EXISTS search_vector tsvector
            GENERATED ALWAYS AS (
                to_tsvector(\'russian\', coalesce(name, \'\')) ||
                to_tsvector(\'russian\', coalesce(slug, \'\'))
            ) STORED
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS categories_search_vector_idx
            ON ontarget_catalog_categories
            USING GIN (search_vector)
        ');
    }

    public function down()
    {
        DB::statement('DROP INDEX IF EXISTS products_search_vector_idx');
        DB::statement('ALTER TABLE products DROP COLUMN IF EXISTS search_vector');

        DB::statement('DROP INDEX IF EXISTS categories_search_vector_idx');
        DB::statement('ALTER TABLE categories DROP COLUMN IF EXISTS search_vector');
    }
};
