<?php

/**
 *   Dublin Core Vocabulary (Resource)
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
class DC {

    // DC concepts
    public static function CONTRIBUTOR() {
        return new Resource(DC_NS . 'contributor');
    }

    public static function COVERAGE() {
        return new Resource(DC_NS . 'coverage');
    }

    public static function CREATOR() {
        return new Resource(DC_NS . 'creator');
    }

    public static function DATE() {
        return new Resource(DC_NS . 'date');
    }

    public static function DESCRIPTION() {
        return new Resource(DC_NS . 'description');
    }

    public static function FORMAT() {
        return new Resource(DC_NS . 'format');
    }

    public static function IDENTIFIER() {
        return new Resource(DC_NS . 'identifier');
    }

    public static function LANGUAGE() {
        return new Resource(DC_NS . 'language');
    }

    public static function PUBLISHER() {
        return new Resource(DC_NS . 'publisher');
    }

    public static function RIGHTS() {
        return new Resource(DC_NS . 'rights');
    }

    public static function SOURCE() {
        return new Resource(DC_NS . 'source');
    }

    public static function SUBJECT() {
        return new Resource(DC_NS . 'subject');
    }

    public static function TITLE() {
        return new Resource(DC_NS . 'title');
    }

    public static function TYPE() {
        return new Resource(DC_NS . 'type');
    }

    // Other Elements and Element Refinements
    public static function ABSTRACT_() {
        return new Resource(DCTERM_NS . 'abstract');
    }

    public static function ACCESS_RIGHTS() {
        return new Resource(DCTERM_NS . 'accessRights');
    }

    public static function ALTERNATIVE() {
        return new Resource(DCTERM_NS . 'alternative');
    }

    public static function AUDIENCE() {
        return new Resource(DCTERM_NS . 'audience');
    }

    public static function AVAILABLE() {
        return new Resource(DCTERM_NS . 'available');
    }

    public static function BIBLIOGRAPHIC_CITATION() {
        return new Resource(DCTERM_NS . 'bibliographicCitation');
    }

    public static function CONFORMS_TO() {
        return new Resource(DCTERM_NS . 'conformsTo');
    }

    public static function CREATED() {
        return new Resource(DCTERM_NS . 'created');
    }

    public static function DATE_ACCEPTED() {
        return new Resource(DCTERM_NS . 'dateAccepted');
    }

    public static function DATE_COPYRIGHTED() {
        return new Resource(DCTERM_NS . 'dateCopyrighted');
    }

    public static function DATE_SUBMITTED() {
        return new Resource(DCTERM_NS . 'dateSubmitted');
    }

    public static function EDUCATION_LEVEL() {
        return new Resource(DCTERM_NS . 'educationLevel');
    }

    public static function EXTENT() {
        return new Resource(DCTERM_NS . 'extent');
    }

    public static function HAS_FORMAT() {
        return new Resource(DCTERM_NS . 'hasFormat');
    }

    public static function HAS_PART() {
        return new Resource(DCTERM_NS . 'hasPart');
    }

    public static function HAS_VERSION() {
        return new Resource(DCTERM_NS . 'hasVersion');
    }

    public static function IS_FORMAT_OF() {
        return new Resource(DCTERM_NS . 'isFormatOf');
    }

    public static function IS_PART_OF() {
        return new Resource(DCTERM_NS . 'isPartOf');
    }

    public static function IS_REFERENCED_BY() {
        return new Resource(DCTERM_NS . 'isReferencedBy');
    }

    public static function IS_REPLACED_BY() {
        return new Resource(DCTERM_NS . 'isReplacedBy');
    }

    public static function IS_REQUIRED_BY() {
        return new Resource(DCTERM_NS . 'isRequiredBy');
    }

    public static function ISSUED() {
        return new Resource(DCTERM_NS . 'issued');
    }

    public static function IS_VERSION_OF() {
        return new Resource(DCTERM_NS . 'isVersionOf');
    }

    public static function LICENSE() {
        return new Resource(DCTERM_NS . 'license');
    }

    public static function MEDIATOR() {
        return new Resource(DCTERM_NS . 'mediator');
    }

    public static function MEDIUM() {
        return new Resource(DCTERM_NS . 'medium');
    }

    public static function MODIFIED() {
        return new Resource(DCTERM_NS . 'modified');
    }

    public static function REFERENCES() {
        return new Resource(DCTERM_NS . 'references');
    }

    public static function REPLACES() {
        return new Resource(DCTERM_NS . 'replaces');
    }

    public static function REQUIRES() {
        return new Resource(DCTERM_NS . 'requires');
    }

    public static function RIGHTS_HOLDER() {
        return new Resource(DCTERM_NS . 'rightsHolder');
    }

    public static function SPATIAL() {
        return new Resource(DCTERM_NS . 'spatial');
    }

    public static function TABLE_OF_CONTENTS() {
        return new Resource(DCTERM_NS . 'tableOfContents');
    }

    public static function TEMPORAL() {
        return new Resource(DCTERM_NS . 'temporal');
    }

    public static function VALID() {
        return new Resource(DCTERM_NS . 'valid');
    }

    // Encoding schemes
    public static function BOX() {
        return new Resource(DCTERM_NS . 'Box');
    }

    public static function DCMI_TYPE() {
        return new Resource(DCTERM_NS . 'DCMIType');
    }

    public static function IMT() {
        return new Resource(DCTERM_NS . 'IMT');
    }

    public static function ISO3166() {
        return new Resource(DCTERM_NS . 'ISO3166');
    }

    public static function ISO639_2() {
        return new Resource(DCTERM_NS . 'ISO639-2');
    }

    public static function LCC() {
        return new Resource(DCTERM_NS . 'LCC');
    }

    public static function LCSH() {
        return new Resource(DCTERM_NS . 'LCSH');
    }

    public static function MESH() {
        return new Resource(DCTERM_NS . 'MESH');
    }

    public static function PERIOD() {
        return new Resource(DCTERM_NS . 'Period');
    }

    public static function POINT() {
        return new Resource(DCTERM_NS . 'Point');
    }

    public static function RFC1766() {
        return new Resource(DCTERM_NS . 'RFC1766');
    }

    public static function RFC3066() {
        return new Resource(DCTERM_NS . 'RFC3066');
    }

    public static function TGN() {
        return new Resource(DCTERM_NS . 'TGN');
    }

    public static function UDC() {
        return new Resource(DCTERM_NS . 'UDC');
    }

    public static function URI() {
        return new Resource(DCTERM_NS . 'URI');
    }

    public static function W3CDTF() {
        return new Resource(DCTERM_NS . 'W3CDTF');
    }

    // DCMI Type Vocabulary
    public static function COLLECTION() {
        return new Resource(DCMITYPE_NS . 'Collection');
    }

    public static function DATASET() {
        return new Resource(DCMITYPE_NS . 'Dataset');
    }

    public static function EVENT() {
        return new Resource(DCMITYPE_NS . 'Event');
    }

    public static function IMAGE() {
        return new Resource(DCMITYPE_NS . 'Image');
    }

    public static function INTERACTIVERESOURCE() {
        return new Resource(DCMITYPE_NS . 'Interactive_Resource');
    }

    public static function MOVINGIMAGE() {
        return new Resource(DCMITYPE_NS . 'Moving_Image');
    }

    public static function PHYSICALOBJECT() {
        return new Resource(DCMITYPE_NS . 'Physical_Object');
    }

    public static function SERVICE() {
        return new Resource(DCMITYPE_NS . 'Service');
    }

    public static function SOFTWARE() {
        return new Resource(DCMITYPE_NS . 'Software');
    }

    public static function SOUND() {
        return new Resource(DCMITYPE_NS . 'Sound');
    }

    public static function STILLIMAGE() {
        return new Resource(DCMITYPE_NS . 'Still_Image');
    }

    public static function TEXT() {
        return new Resource(DCMITYPE_NS . 'Text');
    }

}

?>