<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ontarget_catalog_products', function (Blueprint $table) {
            $table->smallInteger('rank')
                ->default(0)
                ->index();
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_products', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};
