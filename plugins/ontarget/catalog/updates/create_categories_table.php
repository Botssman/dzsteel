<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateCategoriesTable Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('ontarget_catalog_categories', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(1);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->longText('extra')->nullable()
                ->comment('Доп. свойства и характеристики');

            $table->index('parent_id');
            $table->index('is_active');
            $table->index(['nest_left', 'nest_right']);
            $table->index('nest_depth');
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('ontarget_catalog_categories');
    }
};
