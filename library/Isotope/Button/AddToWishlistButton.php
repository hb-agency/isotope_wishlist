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

namespace Isotope\Button;

use Isotope\Isotope;
use Isotope\WishlistFrontend;

class AddToWishlistButton extends Isotope
{
	
    /**
     * Callback for isoButton Hook
     * @param array
     * @return array
     */
	public static function run($arrButtons)
	{
		WishlistFrontend::getWishlist();
		
		$arrButtons['add_to_wishlist'] = array('label' => $GLOBALS['TL_LANG']['MSC']['buttonLabel']['add_to_wishlist'], 'callback' => array('\Isotope\WishlistFrontend', 'addToWishlist'));
		
		return $arrButtons;
	}
}
