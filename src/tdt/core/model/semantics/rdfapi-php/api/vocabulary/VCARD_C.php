<?php

/**
 *   vCard profile defined by RFC 2426 - Vocabulary (Resource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Daniel Westphal (dawe@gmx.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of the
 *   vCard Vocabulary.
 *   For details about vCard see: http://www.w3.org/TR/vcard-rdf .
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class VCARD {

    // VCARD concepts
    public static function UID() {
        return new Resource(VCARD_NS . 'UID');
    }

    public static function ORGPROPERTIES() {
        return new Resource(VCARD_NS . 'ORGPROPERTIES');
    }

    public static function ADRTYPES() {
        return new Resource(VCARD_NS . 'ADRTYPES');
    }

    public static function NPROPERTIES() {
        return new Resource(VCARD_NS . 'NPROPERTIES');
    }

    public static function EMAILTYPES() {
        return new Resource(VCARD_NS . 'EMAILTYPES');
    }

    public static function TELTYPES() {
        return new Resource(VCARD_NS . 'TELTYPES');
    }

    public static function ADRPROPERTIES() {
        return new Resource(VCARD_NS . 'ADRPROPERTIES');
    }

    public static function TZTYPES() {
        return new Resource(VCARD_NS . 'TZTYPES');
    }

    public static function STREET() {
        return new Resource(VCARD_NS . 'Street');
    }

    public static function AGENT() {
        return new Resource(VCARD_NS . 'AGENT');
    }

    public static function SOURCE() {
        return new Resource(VCARD_NS . 'SOURCE');
    }

    public static function BDAY() {
        return new Resource(VCARD_NS . 'BDAY');
    }

    public static function REV() {
        return new Resource(VCARD_NS . 'REV');
    }

    public static function SORT_STRING() {
        return new Resource(VCARD_NS . 'SORT_STRING');
    }

    public static function ORGNAME() {
        return new Resource(VCARD_NS . 'Orgname');
    }

    public static function CATEGORIES() {
        return new Resource(VCARD_NS . 'CATEGORIES');
    }

    public static function N() {
        return new Resource(VCARD_NS . 'N');
    }

    public static function PCODE() {
        return new Resource(VCARD_NS . 'Pcode');
    }

    public static function PREFIX() {
        return new Resource(VCARD_NS . 'Prefix');
    }

    public static function PHOTO() {
        return new Resource(VCARD_NS . 'PHOTO');
    }

    public static function FN() {
        return new Resource(VCARD_NS . 'FN');
    }

    public static function SUFFIX() {
        return new Resource(VCARD_NS . 'Suffix');
    }

    public static function VCARD_CLASS() {
        return new Resource(VCARD_NS . 'CLASS');
    }

    public static function ADR() {
        return new Resource(VCARD_NS . 'ADR');
    }

    public static function REGION() {
        return new Resource(VCARD_NS . 'Region');
    }

    public static function GEO() {
        return new Resource(VCARD_NS . 'GEO');
    }

    public static function EXTADD() {
        return new Resource(VCARD_NS . 'Extadd');
    }

    public static function GROUP() {
        return new Resource(VCARD_NS . 'GROUP');
    }

    public static function EMAIL() {
        return new Resource(VCARD_NS . 'EMAIL');
    }

    public static function FAMILY() {
        return new Resource(VCARD_NS . 'Family');
    }

    public static function TZ() {
        return new Resource(VCARD_NS . 'TZ');
    }

    public static function NAME() {
        return new Resource(VCARD_NS . 'NAME');
    }

    public static function ORGUNIT() {
        return new Resource(VCARD_NS . 'Orgunit');
    }

    public static function COUNTRY() {
        return new Resource(VCARD_NS . 'Country');
    }

    public static function SOUND() {
        return new Resource(VCARD_NS . 'SOUND');
    }

    public static function TITLE() {
        return new Resource(VCARD_NS . 'TITLE');
    }

    public static function MAILER() {
        return new Resource(VCARD_NS . 'MAILER');
    }

    public static function OTHER() {
        return new Resource(VCARD_NS . 'Other');
    }

    public static function LOCALITY() {
        return new Resource(VCARD_NS . 'Locality');
    }

    public static function POBOX() {
        return new Resource(VCARD_NS . 'Pobox');
    }

    public static function KEY() {
        return new Resource(VCARD_NS . 'KEY');
    }

    public static function PRODID() {
        return new Resource(VCARD_NS . 'PRODID');
    }

    public static function GIVEN() {
        return new Resource(VCARD_NS . 'Given');
    }

    public static function LABEL() {
        return new Resource(VCARD_NS . 'LABEL');
    }

    public static function TEL() {
        return new Resource(VCARD_NS . 'TEL');
    }

    public static function NICKNAME() {
        return new Resource(VCARD_NS . 'NICKNAME');
    }

    public static function ROLE() {
        return new Resource(VCARD_NS . 'ROLE');
    }

}

?>