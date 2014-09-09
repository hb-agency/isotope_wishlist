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

use Isotope\Automator;
use Isotope\Model\Config;
use Isotope\Model\ProductCollection\Wishlist;


/**
 * Class Isotope\WishlistAutomator
 *
 * Provide methods to run Isotope automated jobs.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 */
class WishlistAutomator extends Automator
{

    /**
     * Remove wishlists that have not been accessed for a given number of days
     */
    public function deleteOldWishlists()
    {
        $t = Wishlist::getTable();
        $objCarts = Wishlist::findBy(array("type='wishlist'", "$t.member=0", "$t.tstamp<?"), array(time() - $GLOBALS['TL_CONFIG']['iso_cartTimeout']));

        if (($intPurged = $this->deleteOldCollections($objCarts)) > 0) {
            \System::log('Deleted ' . $intPurged . ' old guest wishlists', __METHOD__, TL_CRON);
        }
    }
}
