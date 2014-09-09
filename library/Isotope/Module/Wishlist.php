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

namespace Isotope\Module;

use Isotope\Isotope;
use Isotope\Module\Cart;
use Isotope\WishlistFrontend;


/**
 * Class Wishlist
 *
 * Front end module Isotope "Wishlist".
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Christian de la Haye <service@delahaye.de>
 */
class Wishlist extends Cart
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_iso_wishlist';

    /**
     * FORM_SUBMIT value for this module
     * @var string
     */
    protected $strFormId = 'iso_wishlist_update_';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ISOTOPE ECOMMERCE: WISHLIST ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Add current module ID to FORM_SUBMIT
        $this->strFormId .= $this->id;

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile()
    {
        if (WishlistFrontend::getWishlist()->isEmpty()) {
            $this->Template->empty   = true;
            $this->Template->type    = 'empty';
            $this->Template->message = $this->iso_emptyMessage ? $this->iso_noProducts : $GLOBALS['TL_LANG']['MSC']['noItemsInWishlist'];

            return;
        }

        // Remove from cart
        if (\Input::get('remove') > 0 && WishlistFrontend::getWishlist()->deleteItemById((int) \Input::get('remove'))) {
            \Controller::redirect(preg_replace('/([?&])remove=[^&]*(&|$)/', '$1', \Environment::get('request')));
        }

        $objTemplate = new \Isotope\Template($this->iso_collectionTpl);

        WishlistFrontend::getWishlist()->addToTemplate(
            $objTemplate,
            array(
                 'gallery' => $this->iso_gallery,
                 'sorting' => WishlistFrontend::getWishlist()->getItemsSortingCallable($this->iso_orderCollectionBy),
            )
        );

        $blnReload   = false;
        $arrQuantity = \Input::post('quantity');
        $arrItems    = $objTemplate->items;

        foreach ($arrItems as $k => $arrItem) {

            // Update cart data if form has been submitted
            if (\Input::post('FORM_SUBMIT') == $this->strFormId && is_array($arrQuantity) && isset($arrQuantity[$arrItem['id']])) {
                $blnReload = true;
                WishlistFrontend::getWishlist()->updateItemById($arrItem['id'], array('quantity' => $arrQuantity[$arrItem['id']]));
                continue; // no need to generate $arrProductData, we reload anyway
            }


            $arrItem['remove_href']  = \Haste\Util\Url::addQueryString('remove=' . $arrItem['id']);
            $arrItem['remove_title'] = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['removeProductLinkTitle'], $arrItem['name']));
            $arrItem['remove_link']  = $GLOBALS['TL_LANG']['MSC']['removeProductLinkText'];

            $arrItems[$k] = $arrItem;
        }

        $arrButtons = $this->generateButtons();

        // Reload the page if no button has handled it
        if ($blnReload) {

            // Unset payment and shipping method because they could get invalid due to the change
            // @todo change this to check availability, but that's an API/BC break
            if (($objShipping = WishlistFrontend::getWishlist()->getShippingMethod()) !== null && !$objShipping->isAvailable()) {
                WishlistFrontend::getWishlist()->setShippingMethod(null);
            }

            if (($objPayment = WishlistFrontend::getWishlist()->getPaymentMethod()) !== null && !$objPayment->isAvailable()) {
                WishlistFrontend::getWishlist()->setPaymentMethod(null);
            }

            \Controller::reload();
        }

        $objTemplate->items         = $arrItems;
        $objTemplate->isEditable    = true;
        $objTemplate->linkProducts  = true;
        $objTemplate->formId        = $this->strFormId;
        $objTemplate->formSubmit    = $this->strFormId;
        $objTemplate->action        = \Environment::get('request');
        $objTemplate->buttons       = $arrButtons;
        $objTemplate->custom        = '';

        // HOOK: order status has been updated
        if (isset($GLOBALS['ISO_HOOKS']['compileWishlist']) && is_array($GLOBALS['ISO_HOOKS']['compileWishlist'])) {
            $strCustom = '';

        	foreach ($GLOBALS['ISO_HOOKS']['compileWishlist'] as $callback) {
        		$objCallback = \System::importStatic($callback[0]);
        		$strCustom .= $objCallback->$callback[1]($this);
        	}

        	$objTemplate->custom = $strCustom;
        }

        $this->Template->empty      = false;
        $this->Template->collection = WishlistFrontend::getWishlist();
        $this->Template->products   = $objTemplate->parse();
    }

    /**
     * Generate buttons for cart template
     * @return  array
     */
    protected function generateButtons()
    {
        $arrButtons = parent::generateButtons();
        $arrButtons['update']['label'] = $GLOBALS['TL_LANG']['MSC']['updateWishlistBT'];
        return $arrButtons;
    }
}
