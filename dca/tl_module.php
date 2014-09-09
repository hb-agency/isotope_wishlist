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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_wishlist'] = $GLOBALS['TL_DCA']['tl_module']['palettes']['iso_cart'];

foreach ($GLOBALS['TL_DCA']['tl_module']['palettes'] as $key=>$val)
{
	$GLOBALS['TL_DCA']['tl_module']['palettes'][$key] = str_replace('iso_addProductJumpTo', 'iso_addProductJumpTo,iso_addProductWislistJumpTo', $GLOBALS['TL_DCA']['tl_module']['palettes'][$key]);
}


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['iso_addProductWislistJumpTo'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_module']['iso_addProductWislistJumpTo'],
    'exclude'                   => true,
    'inputType'                 => 'pageTree',
    'foreignKey'                => 'tl_page.title',
    'eval'                      => array('fieldType'=>'radio', 'tl_class'=>'clr'),
    'explanation'               => 'jumpTo',
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
    'relation'                  => array('type'=>'hasOne', 'load'=>'lazy'),
);