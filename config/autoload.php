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
 * Register PSR-0 namespace
 */
NamespaceClassLoader::add('Isotope', 'system/modules/isotope_wishlist/library');


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_iso_wishlist'						=> 'system/modules/isotope_wishlist/templates/modules'
));
