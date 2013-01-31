<?php

/**
 *   Resource Description Framework (RDF) Vocabulary (Resource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Daniel Westphal (dawe@gmx.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of the
 *   Resource Description Framework (RDF).
 *   For details about RDF see: http://www.w3.org/RDF/.
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class RDF {

    // RDF concepts (constants are defined in constants.php)
    public static function ALT() {
        return new Resource(RDF_NAMESPACE_URI . RDF_ALT);
    }

    public static function BAG() {
        return new Resource(RDF_NAMESPACE_URI . RDF_BAG);
    }

    public static function PROPERTY() {
        return new Resource(RDF_NAMESPACE_URI . RDF_PROPERTY);
    }

    public static function SEQ() {
        return new Resource(RDF_NAMESPACE_URI . RDF_SEQ);
    }

    public static function STATEMENT() {
        return new Resource(RDF_NAMESPACE_URI . RDF_STATEMENT);
    }

    public static function RDF_LIST() {
        return new Resource(RDF_NAMESPACE_URI . RDF_LIST);
    }

    public static function NIL() {
        return new Resource(RDF_NAMESPACE_URI . RDF_NIL);
    }

    public static function TYPE() {
        return new Resource(RDF_NAMESPACE_URI . RDF_TYPE);
    }

    public static function REST() {
        return new Resource(RDF_NAMESPACE_URI . RDF_REST);
    }

    public static function FIRST() {
        return new Resource(RDF_NAMESPACE_URI . RDF_FIRST);
    }

    public static function SUBJECT() {
        return new Resource(RDF_NAMESPACE_URI . RDF_SUBJECT);
    }

    public static function PREDICATE() {
        return new Resource(RDF_NAMESPACE_URI . RDF_PREDICATE);
    }

    public static function OBJECT() {
        return new Resource(RDF_NAMESPACE_URI . RDF_OBJECT);
    }

    public static function DESCRIPTION() {
        return new Resource(RDF_NAMESPACE_URI . RDF_DESCRIPTION);
    }

    public static function ID() {
        return new Resource(RDF_NAMESPACE_URI . RDF_ID);
    }

    public static function ABOUT() {
        return new Resource(RDF_NAMESPACE_URI . RDF_ABOUT);
    }

    public static function ABOUT_EACH() {
        return new Resource(RDF_NAMESPACE_URI . RDF_ABOUT_EACH);
    }

    public static function ABOUT_EACH_PREFIX() {
        return new Resource(RDF_NAMESPACE_URI . RDF_ABOUT_EACH_PREFIX);
    }

    public static function BAG_ID() {
        return new Resource(RDF_NAMESPACE_URI . RDF_BAG_ID);
    }

    public static function RESOURCE() {
        return new Resource(RDF_NAMESPACE_URI . RDF_RESOURCE);
    }

    public static function PARSE_TYPE() {
        return new Resource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE);
    }

    public static function LITERAL() {
        return new Resource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_LITERAL);
    }

    public static function PARSE_TYPE_RESOURCE() {
        return new Resource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_RESOURCE);
    }

    public static function LI() {
        return new Resource(RDF_NAMESPACE_URI . RDF_LI);
    }

    public static function NODE_ID() {
        return new Resource(RDF_NAMESPACE_URI . RDF_NODEID);
    }

    public static function DATATYPE() {
        return new Resource(RDF_NAMESPACE_URI . RDF_DATATYPE);
    }

    public static function SEE_ALSO() {
        return new Resource(RDF_NAMESPACE_URI . RDF_SEEALSO);
    }

}

?>