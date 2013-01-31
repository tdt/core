<?php

/**
 *   Dublin Core Vocabulary (ResResource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Chris Bizer (chris@bizer.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of the Dublin
 *   Core Vocabulary. For details about DC see: http://dublincore.org/
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class DC_RES {

    // DC concepts
    public static function CONTRIBUTOR() {
        return new ResResource(DC_NS . 'contributor');
    }

    public static function COVERAGE() {
        return new ResResource(DC_NS . 'coverage');
    }

    public static function CREATOR() {
        return new ResResource(DC_NS . 'creator');
    }

    public static function DATE() {
        return new ResResource(DC_NS . 'date');
    }

    public static function DESCRIPTION() {
        return new ResResource(DC_NS . 'description');
    }

    public static function FORMAT() {
        return new ResResource(DC_NS . 'format');
    }

    public static function IDENTIFIER() {
        return new ResResource(DC_NS . 'identifier');
    }

    public static function LANGUAGE() {
        return new ResResource(DC_NS . 'language');
    }

    public static function PUBLISHER() {
        return new ResResource(DC_NS . 'publisher');
    }

    public static function RIGHTS() {
        return new ResResource(DC_NS . 'rights');
    }

    public static function SOURCE() {
        return new ResResource(DC_NS . 'source');
    }

    public static function SUBJECT() {
        return new ResResource(DC_NS . 'subject');
    }

    public static function TITLE() {
        return new ResResource(DC_NS . 'title');
    }

    public static function TYPE() {
        return new ResResource(DC_NS . 'type');
    }

    // Other Elements and Element Refinements
    public static function ABSTRACT_() {
        return new ResResource(DCTERM_NS . 'abstract');
    }

    public static function ACCESS_RIGHTS() {
        return new ResResource(DCTERM_NS . 'accessRights');
    }

    public static function ALTERNATIVE() {
        return new ResResource(DCTERM_NS . 'alternative');
    }

    public static function AUDIENCE() {
        return new ResResource(DCTERM_NS . 'audience');
    }

    public static function AVAILABLE() {
        return new ResResource(DCTERM_NS . 'available');
    }

    public static function BIBLIOGRAPHIC_CITATION() {
        return new ResResource(DCTERM_NS . 'bibliographicCitation');
    }

    public static function CONFORMS_TO() {
        return new ResResource(DCTERM_NS . 'conformsTo');
    }

    public static function CREATED() {
        return new ResResource(DCTERM_NS . 'created');
    }

    public static function DATE_ACCEPTED() {
        return new ResResource(DCTERM_NS . 'dateAccepted');
    }

    public static function DATE_COPYRIGHTED() {
        return new ResResource(DCTERM_NS . 'dateCopyrighted');
    }

    public static function DATE_SUBMITTED() {
        return new ResResource(DCTERM_NS . 'dateSubmitted');
    }

    public static function EDUCATION_LEVEL() {
        return new ResResource(DCTERM_NS . 'educationLevel');
    }

    public static function EXTENT() {
        return new ResResource(DCTERM_NS . 'extent');
    }

    public static function HAS_FORMAT() {
        return new ResResource(DCTERM_NS . 'hasFormat');
    }

    public static function HAS_PART() {
        return new ResResource(DCTERM_NS . 'hasPart');
    }

    public static function HAS_VERSION() {
        return new ResResource(DCTERM_NS . 'hasVersion');
    }

    public static function IS_FORMAT_OF() {
        return new ResResource(DCTERM_NS . 'isFormatOf');
    }

    public static function IS_PART_OF() {
        return new ResResource(DCTERM_NS . 'isPartOf');
    }

    public static function IS_REFERENCED_BY() {
        return new ResResource(DCTERM_NS . 'isReferencedBy');
    }

    public static function IS_REPLACED_BY() {
        return new ResResource(DCTERM_NS . 'isReplacedBy');
    }

    public static function IS_REQUIRED_BY() {
        return new ResResource(DCTERM_NS . 'isRequiredBy');
    }

    public static function ISSUED() {
        return new ResResource(DCTERM_NS . 'issued');
    }

    public static function IS_VERSION_OF() {
        return new ResResource(DCTERM_NS . 'isVersionOf');
    }

    public static function LICENSE() {
        return new ResResource(DCTERM_NS . 'license');
    }

    public static function MEDIATOR() {
        return new ResResource(DCTERM_NS . 'mediator');
    }

    public static function MEDIUM() {
        return new ResResource(DCTERM_NS . 'medium');
    }

    public static function MODIFIED() {
        return new ResResource(DCTERM_NS . 'modified');
    }

    public static function REFERENCES() {
        return new ResResource(DCTERM_NS . 'references');
    }

    public static function REPLACES() {
        return new ResResource(DCTERM_NS . 'replaces');
    }

    public static function REQUIRES() {
        return new ResResource(DCTERM_NS . 'requires');
    }

    public static function RIGHTS_HOLDER() {
        return new ResResource(DCTERM_NS . 'rightsHolder');
    }

    public static function SPATIAL() {
        return new ResResource(DCTERM_NS . 'spatial');
    }

    public static function TABLE_OF_CONTENTS() {
        return new ResResource(DCTERM_NS . 'tableOfContents');
    }

    public static function TEMPORAL() {
        return new ResResource(DCTERM_NS . 'temporal');
    }

    public static function VALID() {
        return new ResResource(DCTERM_NS . 'valid');
    }

    // Encoding schemes
    public static function BOX() {
        return new ResResource(DCTERM_NS . 'Box');
    }

    public static function DCMI_TYPE() {
        return new ResResource(DCTERM_NS . 'DCMIType');
    }

    public static function IMT() {
        return new ResResource(DCTERM_NS . 'IMT');
    }

    public static function ISO3166() {
        return new ResResource(DCTERM_NS . 'ISO3166');
    }

    public static function ISO639_2() {
        return new ResResource(DCTERM_NS . 'ISO639-2');
    }

    public static function LCC() {
        return new ResResource(DCTERM_NS . 'LCC');
    }

    public static function LCSH() {
        return new ResResource(DCTERM_NS . 'LCSH');
    }

    public static function MESH() {
        return new ResResource(DCTERM_NS . 'MESH');
    }

    public static function PERIOD() {
        return new ResResource(DCTERM_NS . 'Period');
    }

    public static function POINT() {
        return new ResResource(DCTERM_NS . 'Point');
    }

    public static function RFC1766() {
        return new ResResource(DCTERM_NS . 'RFC1766');
    }

    public static function RFC3066() {
        return new ResResource(DCTERM_NS . 'RFC3066');
    }

    public static function TGN() {
        return new ResResource(DCTERM_NS . 'TGN');
    }

    public static function UDC() {
        return new ResResource(DCTERM_NS . 'UDC');
    }

    public static function URI() {
        return new ResResource(DCTERM_NS . 'URI');
    }

    public static function W3CDTF() {
        return new ResResource(DCTERM_NS . 'W3CDTF');
    }

    // DCMI Type Vocabulary
    public static function COLLECTION() {
        return new ResResource(DCMITYPE_NS . 'Collection');
    }

    public static function DATASET() {
        return new ResResource(DCMITYPE_NS . 'Dataset');
    }

    public static function EVENT() {
        return new ResResource(DCMITYPE_NS . 'Event');
    }

    public static function IMAGE() {
        return new ResResource(DCMITYPE_NS . 'Image');
    }

    public static function INTERACTIVE_RESOURCE() {
        return new ResResource(DCMITYPE_NS . 'Interactive_Resource');
    }

    public static function MOVINGIMAGE() {
        return new ResResource(DCMITYPE_NS . 'Moving_Image');
    }

    public static function PHYSICALOBJECT() {
        return new ResResource(DCMITYPE_NS . 'Physical_Object');
    }

    public static function SERVICE() {
        return new ResResource(DCMITYPE_NS . 'Service');
    }

    public static function SOFTWARE() {
        return new ResResource(DCMITYPE_NS . 'Software');
    }

    public static function SOUND() {
        return new Resource(DCMITYPE_NS . 'Sound');
    }

    public static function STILLIMAGE() {
        return new ResResource(DCMITYPE_NS . 'Still_Image');
    }

    public static function TEXT() {
        return new ResResource(DCMITYPE_NS . 'Text');
    }

}

?>