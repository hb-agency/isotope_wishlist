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


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['isotope']['iso_wishlist'] = 'Isotope\Module\Wishlist';


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['buttons'][] = array('Isotope\Button\AddToWishlistButton', 'run');


/**
 * Product collections
 */
\Isotope\Model\ProductCollection::registerModelType('wishlist', 'Isotope\Model\ProductCollection\Wishlist');


/**
 * Cron Jobs
 */
$GLOBALS['TL_CRON']['daily'][] = array('Isotope\WishlistAutomator', 'deleteOldWishlists');