<?php

/**
 *   Friend of a Friend (FOAF) Vocabulary (Resource)
 *
 *   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 *   @author Tobias Gauß (tobias.gauss@web.de)
 *   @package vocabulary
 *
 *   Wrapper, defining resources for all terms of the
 *   Friend of a Friend project (FOAF).
 *   For details about FOAF see: http://xmlns.com/foaf/0.1/.
 *   Using the wrapper allows you to define all aspects of
 *   the vocabulary in one spot, simplifing implementation and
 *   maintainence.
 */
//Made all methods static - Miel Vander Sande


class FOAF {

    public static function AGENT() {
        return new Resource(FOAF_NS . 'Agent');
    }

    public static function DOCUMENT() {
        return new Resource(FOAF_NS . 'Document');
    }

    public static function GROUP() {
        return new Resource(FOAF_NS . 'Group');
    }

    public static function IMAGE() {
        return new Resource(FOAF_NS . 'Image');
    }

    public static function ONLINE_ACCOUNT() {
        return new Resource(FOAF_NS . 'OnlineAccount');
    }

    public static function ONLINE_CHAT_ACCOUNT() {
        return new Resource(FOAF_NS . 'OnlineChatAccount');
    }

    public static function ONLINE_ECOMMERCE_ACCOUNT() {
        return new Resource(FOAF_NS . 'OnlineEcommerceAccount');
    }

    public static function ONLINE_GAMING_ACCOUNT() {
        return new Resource(FOAF_NS . 'OnlineGamingAccount');
    }

    public static function ORGANIZATION() {
        return new Resource(FOAF_NS . 'Organization');
    }

    public static function PERSON() {
        return new Resource(FOAF_NS . 'Person');
    }

    public static function PERSONAL_PROFILE_DOCUMENT() {
        return new Resource(FOAF_NS . 'PersonalProfileDocument');
    }

    public static function PROJECT() {
        return new Resource(FOAF_NS . 'Project');
    }

    public static function ACCOUNT_NAME() {
        return new Resource(FOAF_NS . 'accountName');
    }

    public static function ACCOUNT_SERVICE_HOMEPAGE() {
        return new Resource(FOAF_NS . 'accountServiceHomepage');
    }

    public static function AIM_CHAT_ID() {
        return new Resource(FOAF_NS . 'aimChatID');
    }

    public static function BASED_NEAR() {
        return new Resource(FOAF_NS . 'based_near');
    }

    public static function CURRENT_PROJECT() {
        return new Resource(FOAF_NS . 'currentProject');
    }

    public static function DEPICTION() {
        return new Resource(FOAF_NS . 'depiction');
    }

    public static function DEPICTS() {
        return new Resource(FOAF_NS . 'depicts');
    }

    public static function DNA_CHECKSUM() {
        return new Resource(FOAF_NS . 'dnaChecksum');
    }

    public static function FAMILY_NAME() {
        return new Resource(FOAF_NS . 'family_name');
    }

    public static function FIRST_NAME() {
        return new Resource(FOAF_NS . 'firstName');
    }

    public static function FUNDED_BY() {
        return new Resource(FOAF_NS . 'fundedBy');
    }

    public static function GEEKCODE() {
        return new Resource(FOAF_NS . 'geekcode');
    }

    public static function GENDER() {
        return new Resource(FOAF_NS . 'gender');
    }

    public static function GIVENNAME() {
        return new Resource(FOAF_NS . 'givenname');
    }

    public static function HOLDS_ACCOUNT() {
        return new Resource(FOAF_NS . 'holdsAccount');
    }

    public static function HOMEPAGE() {
        return new Resource(FOAF_NS . 'homepage');
    }

    public static function ICQ_CHAT_ID() {
        return new Resource(FOAF_NS . 'icqChatID');
    }

    public static function IMG() {
        return new Resource(FOAF_NS . 'img');
    }

    public static function INTEREST() {
        return new Resource(FOAF_NS . 'interest');
    }

    public static function JABBER_ID() {
        return new Resource(FOAF_NS . 'jabberID');
    }

    public static function KNOWS() {
        return new Resource(FOAF_NS . 'knows');
    }

    public static function LOGO() {
        return new Resource(FOAF_NS . 'logo');
    }

    public static function MADE() {
        return new Resource(FOAF_NS . 'made');
    }

    public static function MAKER() {
        return new Resource(FOAF_NS . 'maker');
    }

    public static function MBOX() {
        return new Resource(FOAF_NS . 'mbox');
    }

    public static function MBOX_SHA1SUM() {
        return new Resource(FOAF_NS . 'mbox_sha1sum');
    }

    public static function MEMBER() {
        return new Resource(FOAF_NS . 'member');
    }

    public static function MEMBERSHIP_CLASS() {
        return new Resource(FOAF_NS . 'membershipClass');
    }

    public static function MSN_CHAT_ID() {
        return new Resource(FOAF_NS . 'msnChatID');
    }

    public static function MYERS_BRIGGS() {
        return new Resource(FOAF_NS . 'myersBriggs');
    }

    public static function NAME() {
        return new Resource(FOAF_NS . 'name');
    }

    public static function NICK() {
        return new Resource(FOAF_NS . 'nick');
    }

    public static function PAGE() {
        return new Resource(FOAF_NS . 'page');
    }

    public static function PAST_PROJECT() {
        return new Resource(FOAF_NS . 'pastProject');
    }

    public static function PHONE() {
        return new Resource(FOAF_NS . 'phone');
    }

    public static function PLAN() {
        return new Resource(FOAF_NS . 'plan');
    }

    public static function PRIMARY_TOPIC() {
        return new Resource(FOAF_NS . 'primaryTopic');
    }

    public static function PUBLICATIONS() {
        return new Resource(FOAF_NS . 'publications');
    }

    public static function SCHOOL_HOMEPAGE() {
        return new Resource(FOAF_NS . 'schoolHomepage');
    }

    public static function SHA1() {
        return new Resource(FOAF_NS . 'sha1');
    }

    public static function SURNAME() {
        return new Resource(FOAF_NS . 'surname');
    }

    public static function THEME() {
        return new Resource(FOAF_NS . 'theme');
    }

    public static function THUMBNAIL() {
        return new Resource(FOAF_NS . 'thumbnail');
    }

    public static function TIPJAR() {
        return new Resource(FOAF_NS . 'tipjar');
    }

    public static function TITLE() {
        return new Resource(FOAF_NS . 'title');
    }

    public static function TOPIC() {
        return new Resource(FOAF_NS . 'topic');
    }

    public static function TOPIC_INTEREST() {
        return new Resource(FOAF_NS . 'topic_interest');
    }

    public static function WEBLOG() {
        return new Resource(FOAF_NS . 'weblog');
    }

    public static function WORK_INFO_HOMEPAGE() {
        return new Resource(FOAF_NS . 'workInfoHomepage');
    }

    public static function WORKPLACE_HOMEPAGE() {
        return new Resource(FOAF_NS . 'workplaceHomepage');
    }

    public static function YAHOO_CHAT_ID() {
        return new Resource(FOAF_NS . 'yahooChatID');
    }

}

?>