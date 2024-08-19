<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateImportLogsTable Migration
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
        Schema::create('ontarget_catalog_import_logs', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('session_key')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->string('status')->nullable();
            $table->string('file')->nullable();
            $table->longText('results')->nullable();
            $table->longText('product_data')->nullable();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('ontarget_catalog_import_logs');
    }
};
