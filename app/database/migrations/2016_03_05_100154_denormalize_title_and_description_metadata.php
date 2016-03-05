<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DenormalizeTitleAndDescriptionMetadata extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('definitions', function ($table) {
            $table->string('title', 255);
        });

        // Denormalize the definitions by copying the existing titles and descriptions
        foreach (\Definition::all() as $definition) {
            $source_model = new $definition->source_type();
            $source = $source_model->find($definition->source_id);

            if (!empty($source->title)) {
                $definition->title = $source->title;
            }

            if (!empty($source->description)) {
                $definition->description = $source->description;
            }

            $definition->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('definitions', function ($table) {
            $table->dropColumn('title');
        });
    }
}
