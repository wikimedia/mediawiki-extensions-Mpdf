<?php

use MediaWiki\MediaWikiServices;

class MpdfHooks {

	/**
	 * @param Parser &$parser
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setFunctionHook( 'mpdftags', 'MpdfHooks::mpdftags_Render' );
	}

	/**
	 * Add "PDF Export" link to the toolbox
	 * Called with the SidebarBeforeOutput hook.
	 *
	 * @param Skin $skin
	 * @param array &$sidebar
	 * @return bool
	 */
	public static function onSidebarBeforeOutput( Skin $skin, array &$sidebar ) {
		global $wgMpdfToolboxLink;

		if ( !$wgMpdfToolboxLink ) {
			return true;
		}

		$title = $skin->getTitle();
		if ( $title->isSpecialPage() ) {
			return true;
		}

		$sidebar['TOOLBOX']['mpdf'] = [
			'msg' => 'mpdf-action',
			'href' => $title->getLocalUrl( [ 'action' => 'mpdf' ] ),
			'id' => 't-mpdf',
			'rel' => 'mpdf'
		];

		return true;
	}

	/**
	 * Adds a "PDF Export" link to the set of tabs/actions, if one was
	 * specified.
	 * Called with the SkinTemplateNavigation::Universal hook.
	 *
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	public static function onSkinTemplateNavigationUniversal( SkinTemplate $sktemplate, array &$links ) {
		$mpdfTab = MediaWikiServices::getInstance()->getMainConfig()->get( 'MpdfTab' );

		if ( $mpdfTab ) {
			$links['views']['mpdf'] = [
				'class' => false,
				'text' => wfMessage( 'mpdf-action' )->text(),
				'href' => $sktemplate->getTitle()->getLocalURL( 'action=mpdf' ),
			];
		}
	}

	/**
	 * @param Parser &$parser
	 * @return mixed
	 */
	public static function mpdftags_Render( &$parser ) {
		// Get the parameters that were passed to this function
		$params = func_get_args();
		array_shift( $params );

		// Replace open and close tag for security reason
		$values = str_replace( [ '<', '>' ], [ '&lt;', '&gt;' ], $params );

		// Insert mpdf tags between <!--mpdf ... mpdf-->
		$return = '<!--mpdf';
		foreach ( $values as $val ) {
			$return .= "<" . $val . " />\n";
		}
		$return .= "mpdf-->\n";

		// Return mpdf tags as raw html
		return $parser->insertStripItem( $return );
	}

}
