<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePropertiesTable Migration
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
        Schema::create('ontarget_catalog_properties', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('is_active')->default(1);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->index('slug');
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('ontarget_catalog_properties');
    }
};
