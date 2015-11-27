<?php

/**
 * Sql Server definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

class SqlDefinition extends SourceType
{
    protected $table = 'sqldefinitions';

    protected $fillable = array(
        'host',
        'port',
        'username',
        'password',
        'database',
        'query',
        'count_query',
     );

    /**
     * Relationship with the TabularColumns model.
     */
    public function tabularColumns()
    {
        return $this->morphMany('TabularColumns', 'tabular');
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
