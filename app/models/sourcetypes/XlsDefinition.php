<?php

use Tdt\Core\DataControllers\XLSController;

/**
 * Excell definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XlsDefinition extends SourceType
{

    protected $table = 'xlsdefinitions';

    protected $fillable = array('uri', 'sheet', 'has_header_row', 'start_row', 'description');

    /**
     * Relationship with the TabularColumns model.
     */
    public function tabularColumns()
    {
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Overwrite the magic __get function to retrieve the primary key
     * parameter. This isn't a real parameter but a derived one from the tabularcolumns
     * relation.
     */
    public function __get($name)
    {

        if ($name == 'pk') {

            // Retrieve the primary key from the columns
            // Get the related columns
            $columns = $this->tabularColumns()->getResults();

            foreach ($columns as $column) {
                if ($column->is_pk) {
                    return $column->index;
                }
            }

            return null;

        }

        return parent::__get($name);
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

        return parent::delete();
    }
}
