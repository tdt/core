<?php

/**
 *   RSS Vocabulary (Resource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Tobias Gauß (tobias.gauss@web.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of
 *   RSS.
 *   For details about RSS see: http://purl.org/rss/1.0/.
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
class RSS {

    public static function CHANNEL() {
        return new Resource(RSS_NS . 'channel');
    }

    public static function IMAGE() {
        return new Resource(RSS_NS . 'image');
    }

    public static function ITEM() {
        return new Resource(RSS_NS . 'item');
    }

    public static function TEXTINPUT() {
        return new Resource(RSS_NS . 'textinput');
    }

    public static function ITEMS() {
        return new Resource(RSS_NS . 'items');
    }

    public static function TITLE() {
        return new Resource(RSS_NS . 'title');
    }

    public static function LINK() {
        return new Resource(RSS_NS . 'link');
    }

    public static function URL() {
        return new Resource(RSS_NS . 'url');
    }

    public static function DESCRIPTION() {
        return new Resource(RSS_NS . 'description');
    }

    public static function NAME() {
        return new Resource(RSS_NS . 'name');
    }

}

?>