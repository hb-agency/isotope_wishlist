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

namespace Isotope;

use Isotope\Isotope;
use Isotope\Frontend;
use Isotope\Model\ProductCollection\Wishlist;

class WishlistFrontend extends Frontend
{

    /**
     * Current wishlist instance
     * @var \Isotope\Model\ProductCollection\Wishlist
     */
    protected static $objWishlist;


    /**
     * Callback for add_to_wishlist button
     * @param object
     * @param array
     */
    public function addToWishlist($objProduct, array $arrConfig = array())
    {
        $objModule   = $arrConfig['module'];
        $intQuantity = ($objModule->iso_use_quantity && intval(\Input::post('quantity_requested')) > 0) ? intval(\Input::post('quantity_requested')) : 1;

        if (static::getWishlist()->addProduct($objProduct, $intQuantity, $arrConfig) !== false) {
            $_SESSION['ISO_CONFIRM'][] = $GLOBALS['TL_LANG']['MSC']['addedToWishlist'];

            if (!$objModule->iso_addProductWislistJumpTo) {
                $this->reload();
            }

            \Controller::redirect(\Haste\Util\Url::addQueryString('continue=' . base64_encode(\Environment::get('request')), $objModule->iso_addProductWislistJumpTo));
        }
    }


    /**
     * Get the currently active Isotope cart
     * @return \Isotope\Model\ProductCollection\Cart|null
     */
    public static function getWishlist()
    {
        if (null === static::$objWishlist && TL_MODE == 'FE') {
            if ((static::$objWishlist = Wishlist::findForCurrentStore()) !== null) {
                static::$objWishlist->mergeGuestWishlist();
            }
        }

        return static::$objWishlist;
    }
}
