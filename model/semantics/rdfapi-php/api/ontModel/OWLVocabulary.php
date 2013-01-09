<?php

// ----------------------------------------------------------------------------------
// Class: OWLVocabulary
// ----------------------------------------------------------------------------------

/**
 * OWL vocabulary items
 *
 * @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 * @author Daniel Westphal <mail at d-westphal dot de>
 *
 *
 * @package 	ontModel
 * @access	public
 * */
class OWLVocabulary extends OntVocabulary {

    /**
     * Answer the resource that represents the class 'class' in this vocabulary.
     *
     * @return	object ResResource
     * @access	public
     */
    public static function ONTCLASS() {
        return new ResResource(OWL_NS . 'Class');
    }

    /**
     * Answer the predicate that denotes the domain of a property.
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function DOMAIN() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_DOMAIN);
    }

    /**
     * Answer the predicate that denotes comment annotation on an ontology element.
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function COMMENT() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_COMMENT);
    }

    /**
     * Answer the predicate that denotes isDefinedBy annotation on an ontology element
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function IS_DEFINED_BY() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_IS_DEFINED_BY);
    }

    /**
     * Answer the predicate that denotes label annotation on an ontology element
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function LABEL() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_LABEL);
    }

    /**
     * Answer the predicate that denotes the domain of a property.
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function RANGE() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_RANGE);
    }

    /**
     * Answer the predicate that denotes seeAlso annotation on an ontology element
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function SEE_ALSO() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_SEE_ALSO);
    }

    /**
     * Answer the predicate that denotes that one class is a sub-class of another.
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function SUB_CLASS_OF() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_SUBCLASSOF);
    }

    /**
     * Answer the predicate that denotes that one property is a sub-property of another.
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function SUB_PROPERTY_OF() {
        return new ResProperty(RDF_SCHEMA_URI . RDFS_SUBPROPERTYOF);
    }

    public static function ANNOTATION_PROPERTY() {
        return new ResProperty(OWL_NS . 'AnnotationProperty');
    }

    public static function ALL_DIFFERENT() {
        return new ResProperty(OWL_NS . 'AllDifferent');
    }

    public static function ALL_VALUES_FROM() {
        return new ResProperty(OWL_NS . 'allValuesFrom');
    }

    public static function BACKWARD_COMPATIBLE_WITH() {
        return new ResProperty(OWL_NS . 'backwardCompatibleWith');
    }

    public static function CARDINALITY() {
        return new ResProperty(OWL_NS . 'cardinality');
    }

    public static function COMPLEMENT_OF() {
        return new ResProperty(OWL_NS . 'complementOf');
    }

    public static function DATATYPE() {
        return new ResProperty(OWL_NS . 'Datatype');
    }

    public static function DATATYPE_PROPERTY() {
        return new ResProperty(OWL_NS . 'DatatypeProperty');
    }

    public static function DATA_RANGE() {
        return new ResProperty(OWL_NS . 'DataRange');
    }

    public static function DATATYPE_RESTRICTION() {
        return new ResProperty(OWL_NS . 'DatatypeRestriction');
    }

    public static function DEPRECATED_CLASS() {
        return new ResProperty(OWL_NS . 'DeprecatedClass');
    }

    public static function DEPRECATED_PROPERTY() {
        return new ResProperty(OWL_NS . 'DeprecatedProperty');
    }

    public static function DISTINCT_MEMBERS() {
        return new ResProperty(OWL_NS . 'distinctMembers');
    }

    public static function DIFFERENT_FROM() {
        return new ResProperty(OWL_NS . 'differentFrom');
    }

    public static function DISJOINT_WITH() {
        return new ResProperty(OWL_NS . 'disjointWith');
    }

    public static function EQUIVALENT_CLASS() {
        return new ResProperty(OWL_NS . 'equivalentClass');
    }

    public static function EQUIVALENT_PROPERTY() {
        return new ResProperty(OWL_NS . 'equivalentProperty');
    }

    public static function FUNCTIONAL_PROPERTY() {
        return new ResProperty(OWL_NS . 'public static function alProperty');
    }

    public static function HAS_VALUE() {
        return new ResProperty(OWL_NS . 'hasValue');
    }

    public static function INCOMPATIBLE_WITH() {
        return new ResProperty(OWL_NS . 'incompatibleWith');
    }

    public static function IMPORTS() {
        return new ResProperty(OWL_NS . 'imports');
    }

    public static function INTERSECTION_OF() {
        return new ResProperty(OWL_NS . 'intersectionOf');
    }

    public static function INVERSE_FUNCTIONAL_PROPERTY() {
        return new ResProperty(OWL_NS . 'Inversepublic static function alProperty');
    }

    public static function INVERSE_OF() {
        return new ResProperty(OWL_NS . 'inverseOf');
    }

    public static function MAX_CARDINALITY() {
        return new ResProperty(OWL_NS . 'maxCardinality');
    }

    public static function MIN_CARDINALITY() {
        return new ResProperty(OWL_NS . 'minCardinality');
    }

    public static function NOTHING() {
        return new ResProperty(OWL_NS . 'Nothing');
    }

    public static function OBJECT_CLASS() {
        return new ResProperty(OWL_NS . 'ObjectClass');
    }

    public static function OBJECT_PROPERTY() {
        return new ResProperty(OWL_NS . 'ObjectProperty');
    }

    public static function OBJECT_RESTRICTION() {
        return new ResProperty(OWL_NS . 'ObjectRestriction');
    }

    public static function ONE_OF() {
        return new ResProperty(OWL_NS . 'oneOf');
    }

    public static function ON_PROPERTY() {
        return new ResProperty(OWL_NS . 'onProperty');
    }

    public static function ONTOLOGY() {
        return new ResProperty(OWL_NS . 'Ontology');
    }

    public static function PRIOR_VERSION() {
        return new ResProperty(OWL_NS . 'priorVersion');
    }

    public static function PROPERTY() {
        return new ResProperty(OWL_NS . 'Property');
    }

    public static function RESTRICTION() {
        return new ResProperty(OWL_NS . 'Restriction');
    }

    public static function SAME_AS() {
        return new ResProperty(OWL_NS . 'sameAs');
    }

    public static function SAME_CLASS_AS() {
        return new ResProperty(OWL_NS . 'sameClassAs');
    }

    public static function SAME_INDIVIDUAL_AS() {
        return new ResProperty(OWL_NS . 'sameIndividualAs');
    }

    public static function SAME_PROPERTY_AS() {
        return new ResProperty(OWL_NS . 'samePropertyAs');
    }

    public static function SOME_VALUES_FROM() {
        return new ResProperty(OWL_NS . 'someValuesFrom');
    }

    public static function SYMMETRIC_PROPERTY() {
        return new ResProperty(OWL_NS . 'SymmetricProperty');
    }

    public static function THING() {
        return new ResProperty(OWL_NS . 'Thing');
    }

    public static function TRANSITIVE_PROPERTY() {
        return new ResProperty(OWL_NS . 'TransitiveProperty');
    }

    public static function UNION_OF() {
        return new ResProperty(OWL_NS . 'unionOf');
    }

    public static function VERSION_INFO() {
        return new ResProperty(OWL_NS . 'versionInfo');
    }

    public static function OWL_NAMESPACE() {
        return OWL_NS;
    }

    /**
     * Answer the predicate that denotes the rdf:type property.
     *
     * @return	object ResProperty
     * @access	public
     */
    public static function TYPE() {
        return new ResProperty(RDF_NAMESPACE_URI . RDF_TYPE);
    }

}

?>