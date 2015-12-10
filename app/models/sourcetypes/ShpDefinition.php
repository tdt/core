<?php

/**
 * Shape definition model, all processing is done based on the
 * SHP specification http://www.esri.com/library/whitepapers/pdfs/shapefile.pdf.
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class ShpDefinition extends SourceType
{

    protected $table = 'shpdefinitions';

    protected $fillable = array('uri', 'epsg', 'description', 'title', 'map_property');

    /**
     * Relationship with the TabularColumns model.
     */
    public function tabularColumns()
    {
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Relationship with the Geo properties model.
     * this will probably break the relationship or displaying of the data.
     * If so every line or entry needs to have a geo property, or has to be parsed at runtime.
     */
    public function geo()
    {
        return $this->morphMany('GeoProperty', 'source');
    }

    /**
     * Because we have related models, and non hard defined foreign key relationships
     * we have to delete our related models ourselves.
     */
    public function delete()
    {

         // Get the related columns
        $columns = $this->tabularColumns()->getResults();

        foreach ($columns as $column) {
            $column->delete();
        }

        // Get the related geo properties
        $geo_properties = $this->geo()->getResults();

        foreach ($geo_properties as $geo_property) {
            $geo_property->delete();
        }

        return parent::delete();
    }
}
