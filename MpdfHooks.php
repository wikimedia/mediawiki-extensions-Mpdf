<?php

class MpdfHooks {

	/**
	 * @param Parser &$parser
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setFunctionHook( 'mpdftags', 'MpdfHooks::mpdftags_Render' );
	}

	/**
	 * Add PDF to actions tabs in MonoBook based skins
	 * @param Skin $skin
	 * @param array &$actions
	 *
	 * @return bool true
	 */
	public static function onSkinTemplateTabs( $skin, &$actions ) {
		global $wgMpdfTab;

		if ( $wgMpdfTab ) {
			$actions['mpdf'] = [
				'class' => false,
				'text' => wfMessage( 'mpdf-action' )->text(),
				'href' => $skin->getTitle()->getLocalURL( "action=mpdf" ),
			];
		}
		return true;
	}

	/**
	 * Add PDF to actions tabs in vector based skins
	 * @param Skin $skin
	 * @param array &$actions
	 *
	 * @return bool true
	 */
	public static function onSkinTemplateNavigation( $skin, &$actions ) {
		global $wgMpdfTab;

		if ( $wgMpdfTab ) {
			$actions['views']['mpdf'] = [
				'class' => false,
				'text' => wfMessage( 'mpdf-action' )->text(),
				'href' => $skin->getTitle()->getLocalURL( "action=mpdf" ),
			];
		}
		return true;
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
		return $parser->insertStripItem( $return, $parser->mStripState );
	}

}
