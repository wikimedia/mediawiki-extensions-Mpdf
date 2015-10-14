<?php
/**
 * Mpdf extension
 * - Converts current page to PDF and sends to browser
 *
 * See http://www.mediawiki.org/Extension:Mpdf for installation and usage details
 * See http://www.organicdesign.co.nz/Extension_talk:Mpdf for development notes and disucssion
 *
 * Started: 2012-06-25
 * Based by PdfBook extension [http://www.mediawiki.org/Extension:PdfBook],
 * 		author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad],
 * 		licence GNU General Public Licence 2.0 or later
 *
 * @file
 * @ingroup Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad], Pavel Astakhov
 * @copyright © 2007 Aran Dunkley, 2012 Pavel Astakhov
 * @licence GNU General Public Licence 2.0 or later
 */
if( !defined( 'MEDIAWIKI' ) ) die( "Not an entry point." );

const MPDF_VERSION = '0.6.0, 2015-10-14';

$wgAutoloadClasses['MpdfHooks'] =			__DIR__ . '/Mpdf.hooks.php';
$wgAutoloadClasses['mPDF'] =				__DIR__ . '/mpdf/mpdf.php';
$wgMessagesDirs['Mpdf'] =					__DIR__ . '/i18n';
$wgExtensionMessagesFiles['Mpdf'] =			__DIR__ . '/Mpdf.i18n.php';
$wgExtensionMessagesFiles['MpdfMagic'] =	__DIR__ . '/Mpdf.i18n.magic.php';

$wgExtensionCredits['parserhook'][] = array(
	'path'           => __FILE__,
	'name'           => "Mpdf",
	'author'         => array ("[http://www.organicdesign.co.nz/nad User:Nad]", "[[mw:User:Pastakhov|Pavel Astakhov]]"),
	'url'            => "http://www.mediawiki.org/wiki/Extension:Mpdf",
	'version'        => MPDF_VERSION,
	'descriptionmsg' => 'mpdf-desc',
);

$wgMpdfTab = false; # Whether or not an action tab is wanted for printing to PDF

$wgHooks['MediaWikiPerformAction'][] = 'MpdfHooks::onMediaWikiPerformAction';

# Hooks for pre-Vector and Vector addtabs.
$wgHooks['SkinTemplateTabs'][] = 'MpdfHooks::onSkinTemplateTabs';
$wgHooks['SkinTemplateNavigation'][] = 'MpdfHooks::onSkinTemplateNavigation';
$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
	$parser->setFunctionHook( 'mpdftags', 'MpdfHooks::mpdftags_Render' );
	return true;
};
