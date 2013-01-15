<?php

/**
 *   OWL Vocabulary (Resource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Daniel Westphal (dawe@gmx.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of theWeb
 *   Ontology Language (OWL). For details about OWL see:
 *   http://www.w3.org/TR/owl-ref/
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class OWL {

    // OWL concepts
    public static function ANNOTATION_PROPERTY() {
        return new Resource(OWL_NS . 'AnnotationProperty');
    }

    public static function ALL_DIFFERENT() {
        return new Resource(OWL_NS . 'AllDifferent');
    }

    public static function ALL_VALUES_FROM() {
        return new Resource(OWL_NS . 'allValuesFrom');
    }

    public static function BACKWARD_COMPATIBLE_WITH() {
        return new Resource(OWL_NS . 'backwardCompatibleWith');
    }

    public static function CARDINALITY() {
        return new Resource(OWL_NS . 'cardinality');
    }

    public static function OWL_CLASS() {
        return new Resource(OWL_NS . 'Class');
    }

    public static function COMPLEMENT_OF() {
        return new Resource(OWL_NS . 'complementOf');
    }

    public static function DATATYPE() {
        return new Resource(OWL_NS . 'Datatype');
    }

    public static function DATATYPE_PROPERTY() {
        return new Resource(OWL_NS . 'DatatypeProperty');
    }

    public static function DATA_RANGE() {
        return new Resource(OWL_NS . 'DataRange');
    }

    public static function DATATYPE_RESTRICTION() {
        return new Resource(OWL_NS . 'DatatypeRestriction');
    }

    public static function DEPRECATED_CLASS() {
        return new Resource(OWL_NS . 'DeprecatedClass');
    }

    public static function DEPRECATED_PROPERTY() {
        return new Resource(OWL_NS . 'DeprecatedProperty');
    }

    public static function DISTINCT_MEMBERS() {
        return new Resource(OWL_NS . 'distinctMembers');
    }

    public static function DIFFERENT_FROM() {
        return new Resource(OWL_NS . 'differentFrom');
    }

    public static function DISJOINT_WITH() {
        return new Resource(OWL_NS . 'disjointWith');
    }

    public static function EQUIVALENT_CLASS() {
        return new Resource(OWL_NS . 'equivalentClass');
    }

    public static function EQUIVALENT_PROPERTY() {
        return new Resource(OWL_NS . 'equivalentProperty');
    }

    public static function FUNCTIONAL_PROPERTY() {
        return new Resource(OWL_NS . 'FunctionalProperty');
    }

    public static function HAS_VALUE() {
        return new Resource(OWL_NS . 'hasValue');
    }

    public static function INCOMPATIBLE_WITH() {
        return new Resource(OWL_NS . 'incompatibleWith');
    }

    public static function IMPORTS() {
        return new Resource(OWL_NS . 'imports');
    }

    public static function INTERSECTION_OF() {
        return new Resource(OWL_NS . 'intersectionOf');
    }

    public static function INVERSE_FUNCTIONAL_PROPERTY() {
        return new Resource(OWL_NS . 'InverseFunctionalProperty');
    }

    public static function INVERSE_OF() {
        return new Resource(OWL_NS . 'inverseOf');
    }

    public static function MAX_CARDINALITY() {
        return new Resource(OWL_NS . 'maxCardinality');
    }

    public static function MIN_CARDINALITY() {
        return new Resource(OWL_NS . 'minCardinality');
    }

    public static function NOTHING() {
        return new Resource(OWL_NS . 'Nothing');
    }

    public static function OBJECT_CLASS() {
        return new Resource(OWL_NS . 'ObjectClass');
    }

    public static function OBJECT_PROPERTY() {
        return new Resource(OWL_NS . 'ObjectProperty');
    }

    public static function OBJECT_RESTRICTION() {
        return new Resource(OWL_NS . 'ObjectRestriction');
    }

    public static function ONE_OF() {
        return new Resource(OWL_NS . 'oneOf');
    }

    public static function ON_PROPERTY() {
        return new Resource(OWL_NS . 'onProperty');
    }

    public static function ONTOLOGY() {
        return new Resource(OWL_NS . 'Ontology');
    }

    public static function PRIOR_VERSION() {
        return new Resource(OWL_NS . 'priorVersion');
    }

    public static function PROPERTY() {
        return new Resource(OWL_NS . 'Property');
    }

    public static function RESTRICTION() {
        return new Resource(OWL_NS . 'Restriction');
    }

    public static function SAME_AS() {
        return new Resource(OWL_NS . 'sameAs');
    }

    public static function SAME_CLASS_AS() {
        return new Resource(OWL_NS . 'sameClassAs');
    }

    public static function SAME_INDIVIDUAL_AS() {
        return new Resource(OWL_NS . 'sameIndividualAs');
    }

    public static function SAME_PROPERTY_AS() {
        return new Resource(OWL_NS . 'samePropertyAs');
    }

    public static function SOME_VALUES_FROM() {
        return new Resource(OWL_NS . 'someValuesFrom');
    }

    public static function SYMMETRIC_PROPERTY() {
        return new Resource(OWL_NS . 'SymmetricProperty');
    }

    public static function THING() {
        return new Resource(OWL_NS . 'Thing');
    }

    public static function TRANSITIVE_PROPERTY() {
        return new Resource(OWL_NS . 'TransitiveProperty');
    }

    public static function UNION_OF() {
        return new Resource(OWL_NS . 'unionOf');
    }

    public static function VERSION_INFO() {
        return new Resource(OWL_NS . 'versionInfo');
    }

}

?>