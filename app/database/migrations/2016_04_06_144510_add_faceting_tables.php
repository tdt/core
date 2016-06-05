<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migrations holds one of those rare moments in which it's necessary to sort of seed the
 * database tables they make
 */
class AddFacetingTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('definition_facet_types', function ($table) {
            $table->increments('id');
            $table->string('facet_name', 255);
        });


        $facet_types = ['rights', 'keyword', 'language', 'theme', 'publisher_name'];

        $facet_type_models = [];

        foreach ($facet_types as $facet) {
            $facet_type = \FacetType::create(['facet_name' => $facet]);
            $facet_type->save();

            if ($facet != 'keyword') {
                $facet_type_model[$facet] = $facet_type;
            } else {
                $facet_type_model['keywords'] = $facet_type;
            }
        }

        Schema::create('definition_facets', function ($table) {
            $table->increments('id');
            $table->integer('definition_id');
            $table->integer('facet_id');
            $table->string('facet_name');
            $table->string('value');
        });

        // Copy all of the facet related info to the new definition_facets table
        $definitions = \Definition::all();

        foreach ($definitions as $definition) {
            foreach ($facet_type_models as $facet_name => $facet_type) {
                if ($facet_name != 'keywords' && !empty($definition->$facet_name)) {
                    $facet = \Facet::create([
                        'definition_id' => $definition->id,
                        'facet_id' => $facet_type->id,
                        'facet_name' => $facet_name,
                        'value' => $definition->$facet_name
                    ]);

                    $facet->save();
                } else {
                    // split the keywords
                    if (!empty($definition->keywords)) {
                        $keywords = explode(',', $definition->keywords);

                        foreach ($keywords as $keyword) {
                            $facet = \Facet::create([
                                'definition_id' => $definition->id,
                                'facet_id' => $facet_type->id,
                                'facet_name' => 'keyword',
                                'value' => $keyword
                            ]);

                            $facet->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('definition_facet_types');
        Schema::drop('definition_facets');
    }
}
