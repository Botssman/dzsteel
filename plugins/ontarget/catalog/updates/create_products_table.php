<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateProductsTable Migration
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
        Schema::create('ontarget_catalog_products', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('category_id');
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(1);
            $table->string('name');
            $table->string('slug');
            $table->string('vendor_code');
            $table->decimal('price', 8, 2, true)->default(0.0);
            $table->decimal('old_price', 8, 2, true)->nullable();
            $table->integer('weight')->unsigned()->nullable()->comment('Вес, г.');
            $table->integer('height')->unsigned()->nullable()->comment('Высота, мм.');
            $table->integer('length')->unsigned()->nullable()->comment('Длина, мм.');
            $table->integer('width')->unsigned()->nullable()->comment('Ширина, мм.');
            $table->text('description')->nullable();
            $table->longText('extra')->nullable()
                ->comment('Доп. свойства и характеристики');

            $table->index('category_id');
            $table->index('is_active');

            $table->unique('vendor_code');

            $table->foreign('category_id')->references('id')->on('ontarget_catalog_categories');

        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('ontarget_catalog_products');
    }
};
