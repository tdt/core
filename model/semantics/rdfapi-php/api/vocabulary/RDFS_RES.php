<?php

/**
 *   RDF Vocabulary Description Language 1.0: RDF Schema (RDFS) Vocabulary (ResResource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Daniel Westphal (dawe@gmx.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of the
 *   RDF Schema (RDFS).
 *   For details about RDFS see: http://www.w3.org/TR/rdf-schema/.
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class RDFS_RES {

    public static function RESOURCE() {
        return new ResResource(RDF_SCHEMA_URI . 'Resource');
    }

    public static function LITERAL() {
        return new ResResource(RDF_SCHEMA_URI . 'Literal');
    }

    public static function RDFS_CLASS() {
        return new ResResource(RDF_SCHEMA_URI . 'Class');
    }

    public static function DATATYPE() {
        return new ResResource(RDF_SCHEMA_URI . 'Datatype');
    }

    public static function CONTAINER() {
        return new ResResource(RDF_SCHEMA_URI . 'Container');
    }

    public static function CONTAINER_MEMBERSHIP_PROPERTY() {
        return new ResResource(RDF_SCHEMA_URI . 'ContainerMembershipProperty');
    }

    public static function SUB_CLASS_OF() {
        return new ResResource(RDF_SCHEMA_URI . 'subClassOf');
    }

    public static function SUB_PROPERTY_OF() {
        return new ResResource(RDF_SCHEMA_URI . 'subPropertyOf');
    }

    public static function DOMAIN() {
        return new ResResource(RDF_SCHEMA_URI . 'domain');
    }

    public static function RANGE() {
        return new ResResource(RDF_SCHEMA_URI . 'range');
    }

    public static function LABEL() {
        return new ResResource(RDF_SCHEMA_URI . 'label');
    }

    public static function COMMENT() {
        return new ResResource(RDF_SCHEMA_URI . 'comment');
    }

    public static function MEMBER() {
        return new ResResource(RDF_SCHEMA_URI . 'member');
    }

    public static function SEEALSO() {
        return new ResResource(RDF_SCHEMA_URI . 'seeAlso');
    }

    public static function IS_DEFINED_BY() {
        return new ResResource(RDF_SCHEMA_URI . 'isDefinedBy');
    }

}

?>