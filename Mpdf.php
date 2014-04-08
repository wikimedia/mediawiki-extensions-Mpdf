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

define( 'MPDF_VERSION', "0.4.1, 2014-04-08" );

$dir = __DIR__;
$wgAutoloadClasses['MpdfHooks'] = $dir . '/Mpdf.hooks.php';
$wgMessagesDirs['Mpdf'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['Mpdf'] = $dir . '/Mpdf.i18n.php';
$wgExtensionMessagesFiles['MpdfMagic'] = $dir . '/Mpdf.i18n.magic.php';
$wgHooks['ParserFirstCallInit'][] = 'mpdf_Setup';

$wgExtensionCredits['parserhook'][] = array(
	'path'           => __FILE__,
	'name'           => "Mpdf",
	'author'         => array ("[http://www.organicdesign.co.nz/nad User:Nad]", "[[mw:User:Pastakhov|Pavel Astakhov]]"),
	'url'            => "http://www.mediawiki.org/wiki/Extension:Mpdf",
	'version'        => MPDF_VERSION,
	'descriptionmsg' => 'mpdf-desc',
);

$wgMpdfTab = false; # Whether or not an action tab is wanted for printing to PDF

$wgHooks['UnknownAction'][] = 'MpdfHooks::onUnknownAction';

# Hooks for pre-Vector and Vector addtabs.
$wgHooks['SkinTemplateTabs'][] = 'MpdfHooks::onSkinTemplateTabs';
$wgHooks['SkinTemplateNavigation'][] = 'MpdfHooks::onSkinTemplateNavigation';

function mpdf_Setup( Parser $parser ) {
	$parser->setFunctionHook( 'mpdftags', 'MpdfHooks::mpdftags_Render' );
	return true;
}
