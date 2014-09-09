<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://isotopeecommerce.org
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace Isotope\Model\ProductCollection;

use Isotope\Isotope;
use Isotope\Model\Config;
use Isotope\Model\Address;
use Isotope\Model\ProductCollection;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Interfaces\IsotopeProductCollection;


class Wishlist extends Cart implements IsotopeProductCollection
{

    /**
     * Name of the temporary cart cookie
     * @var string
     */
    protected static $strCookie = 'ISOTOPE_TEMP_WISHLIST';

    /**
     * Load the current cart
     * @param   Config
     * @return  Cart
     */
    public static function findForCurrentStore()
    {
        global $objPage;

        if (TL_MODE != 'FE' || null === $objPage || $objPage->rootId == 0) {
            return null;
        }

        $time     = time();
        $strHash  = \Input::cookie(static::$strCookie);
        $intStore = (int) \PageModel::findByPk($objPage->rootId)->iso_store_id;

        //  Check to see if the user is logged in.
        if (FE_USER_LOGGED_IN !== true) {
            if ($strHash == '') {
                $strHash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? \Environment::get('ip') : '') . $intStore . static::$strCookie);
                \System::setCookie(static::$strCookie, $strHash, $time + $GLOBALS['TL_CONFIG']['iso_cartTimeout'], $GLOBALS['TL_CONFIG']['websitePath']);
            }

            $objWishlist = static::findOneBy(array('type=?', 'uniqid=?', 'store_id=?'), array('wishlist', $strHash, $intStore));
        } else {
            $objWishlist = static::findOneBy(array('type=?', 'member=?', 'store_id=?'), array('wishlist', \FrontendUser::getInstance()->id, $intStore));
        }

        // Create new cart
        if ($objWishlist === null) {

            $objConfig 		= Config::findByRootPageOrFallback($objPage->rootId);
            $objWishlist 	= new static();

            // Can't call the individual rows here, it would trigger markModified and a save()
            $objWishlist->setRow(array_merge($objWishlist->row(), array(
                'tstamp'    => $time,
                'member'    => (FE_USER_LOGGED_IN === true ? \FrontendUser::getInstance()->id : 0),
                'uniqid'    => (FE_USER_LOGGED_IN === true ? '' : $strHash),
                'config_id' => $objConfig->id,
                'store_id'  => $intStore,
            )));

        } else {
            $objWishlist->tstamp = $time;
        }

        return $objWishlist;
    }

    /**
     * Merge guest wishlist if necessary
     */
    public function mergeGuestWishlist()
    {
        $this->ensureNotLocked();

        $strHash = \Input::cookie(static::$strCookie);

        // Temporary cart available, move to this cart. Must be after creating a new cart!
        if (FE_USER_LOGGED_IN === true && $strHash != '' && $this->member > 0) {
            $blnMerge = $this->countItems() > 0 ? true : false;

            if (($objTemp = static::findOneBy(array('type=?', 'uniqid=?', 'store_id=?'), array('wishlist', $strHash, $this->store_id))) !== null) {
                $arrIds = $this->copyItemsFrom($objTemp);

                if ($blnMerge && !empty($arrIds)) {
                    $_SESSION['ISO_CONFIRM'][] = $GLOBALS['TL_LANG']['MSC']['wishlistMerged'];
                }

                $objTemp->delete();
            }

            // Delete cookie
            \System::setCookie(static::$strCookie, '', (time() - 3600), $GLOBALS['TL_CONFIG']['websitePath']);
            \System::reload();
        }
    }

}
