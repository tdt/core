<?php

/**
 *   ATOM Vocabulary (Resource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Tobias Gauß (tobias.gauss@web.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of the
 *   ATOM Vocabulary;.
 *   For details about ATOM see: http://semtext.org/atom/atom.html.
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class ATOM {

    public static function TYPE() {
        return new Resource(ATOM_NS . 'type');
    }

    public static function MODE() {
        return new Resource(ATOM_NS . 'mode');
    }

    public static function NAME() {
        return new Resource(ATOM_NS . 'name');
    }

    public static function URL() {
        return new Resource(ATOM_NS . 'url');
    }

    public static function EMAIL() {
        return new Resource(ATOM_NS . 'email');
    }

    public static function REL() {
        return new Resource(ATOM_NS . 'rel');
    }

    public static function HREF() {
        return new Resource(ATOM_NS . 'href');
    }

    public static function TITLE() {
        return new Resource(ATOM_NS . 'title');
    }

    public static function ATOM_CONSTRUCT() {
        return new Resource(ATOM_NS . 'AtomConstruct');
    }

    public static function CONTENT() {
        return new Resource(ATOM_NS . 'Content');
    }

    public static function PERSON() {
        return new Resource(ATOM_NS . 'Person');
    }

    public static function VALUE() {
        return new Resource(ATOM_NS . 'value');
    }

    public static function LINK() {
        return new Resource(ATOM_NS . 'Link');
    }

    public static function FEED() {
        return new Resource(ATOM_NS . 'Feed');
    }

    public static function VERSION() {
        return new Resource(ATOM_NS . 'version');
    }

    public static function LANG() {
        return new Resource(ATOM_NS . 'lang');
    }

    public static function AUTHOR() {
        return new Resource(ATOM_NS . 'author');
    }

    public static function CONTRIBUTOR() {
        return new Resource(ATOM_NS . 'contributor');
    }

    public static function TAGLINE() {
        return new Resource(ATOM_NS . 'tagline');
    }

    public static function GENERATOR() {
        return new Resource(ATOM_NS . 'generator');
    }

    public static function COPYRIGHT() {
        return new Resource(ATOM_NS . 'copyright');
    }

    public static function INFO() {
        return new Resource(ATOM_NS . 'info');
    }

    public static function MODIFIED() {
        return new Resource(ATOM_NS . 'modified');
    }

    public static function ENTRY() {
        return new Resource(ATOM_NS . 'Entry');
    }

    public static function HAS_CHILD() {
        return new Resource(ATOM_NS . 'hasChild');
    }

    public static function HAS_ENTRY() {
        return new Resource(ATOM_NS . 'hasEntry');
    }

    public static function HAS_LINK() {
        return new Resource(ATOM_NS . 'hasLink');
    }

    public static function HAS_TITLE() {
        return new Resource(ATOM_NS . 'hasTitle');
    }

    public static function ISSUED() {
        return new Resource(ATOM_NS . 'issued');
    }

    public static function CREATED() {
        return new Resource(ATOM_NS . 'created');
    }

    public static function SUMMARY() {
        return new Resource(ATOM_NS . 'summary');
    }

}

?>