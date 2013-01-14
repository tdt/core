<?php

/**
 *   RSS Vocabulary (ResResource)
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
class RSS_RES {

    public static function CHANNEL() {
        return new ResResource(RSS_NS . 'channel');
    }

    public static function IMAGE() {
        return new ResResource(RSS_NS . 'image');
    }

    public static function ITEM() {
        return new ResResource(RSS_NS . 'item');
    }

    public static function TEXTINPUT() {
        return new ResResource(RSS_NS . 'textinput');
    }

    public static function ITEMS() {
        return new ResResource(RSS_NS . 'items');
    }

    public static function TITLE() {
        return new ResResource(RSS_NS . 'title');
    }

    public static function LINK() {
        return new ResResource(RSS_NS . 'link');
    }

    public static function URL() {
        return new ResResource(RSS_NS . 'url');
    }

    public static function DESCRIPTION() {
        return new ResResource(RSS_NS . 'description');
    }

    public static function NAME() {
        return new ResResource(RSS_NS . 'name');
    }

}

?>