<?php

/**
 * This file collects all imports for the UniversalInterpreter
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

// basic filters
include_once("universalfilter/interpreter/executers/base/IUniversalFilterNodeExecuter.interface.php");
include_once("universalfilter/interpreter/executers/base/AbstractUniversalFilterNodeExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/IdentifierExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/ConstantExecuter.class.php");

include_once("universalfilter/interpreter/executers/base/BaseEvaluationEnvironmentFilterExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/FilterByExpressionExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/ColumnSelectionFilterExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/SortFieldsFilterExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/DatasetJoinFilterExecuter.class.php");

include_once("universalfilter/interpreter/executers/base/BaseHashingFilterExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/DistinctFilterExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/DataGrouperExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/LimitFilterExecuter.class.php");

include_once("universalfilter/interpreter/executers/implementations/TableAliasExecuter.class.php");

// some tools
include_once("universalfilter/interpreter/executers/tools/ExecuterDateTimeTools.class.php");

// functions
include_once("universalfilter/interpreter/executers/implementations/UnaryFunctionExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/UnaryFunctionExecuters.php");
include_once("universalfilter/interpreter/executers/implementations/BinaryFunctionExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/BinaryFunctionExecuters.php");
include_once("universalfilter/interpreter/executers/implementations/TernaryFunctionExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/TernaryFunctionExecuters.php");
include_once("universalfilter/interpreter/executers/implementations/AggregatorFunctionExecuter.class.php");
include_once("universalfilter/interpreter/executers/implementations/AggregatorFunctionExecuters.php");
include_once("universalfilter/interpreter/executers/implementations/CheckInFunctionExecuter.class.php");

//externally calculated
include_once("universalfilter/interpreter/executers/implementations/ExternallyCalculatedFilterNodeExecuter.class.php");