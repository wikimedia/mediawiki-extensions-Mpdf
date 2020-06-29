<?php

class MpdfHooks {

	public static function registerExtension() {
		global $wgHooks;

		if ( class_exists( 'MediaWiki\HookContainer\HookContainer' ) ) {
			// MW 1.35+
			$wgHooks['SidebarBeforeOutput'][] = "MpdfHooks::onSidebarBeforeOutput";
		} else {
			// MW < 1.35
			$wgHooks['BaseTemplateToolbox'][] = "MpdfHooks::onBaseTemplateToolbox";
		}
	}

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
	 * Add "PDF Export" link to the toolbox. Called with the
	 * SidebarBeforeOutput hook, for MW >= 1.35.
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

		$sidebar['TOOLBOX']['mpdf'] = [
			'msg' => 'mpdf-action',
			'href' => $title->getLocalUrl( [ 'action' => 'mpdf' ] ),
			'id' => 't-mpdf',
			'rel' => 'mpdf'
		];

		return true;
	}

	/**
	 * Add "PDF Export" link to the toolbox. Called with the
	 * BaseTemplateToolbox hook, for MW < 1.35.
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
	 * Add "Export PDF" link to the toolbox.
	 *
	 * @param BaseTemplate $skinTemplate
	 * @param array &$toolbox
	 * @return bool
	 */
	public static function onBaseTemplateToolbox( BaseTemplate $skinTemplate, array &$toolbox ) {
		global $wgMpdfToolboxLink;

		if ( !$wgMpdfToolboxLink ) {
			return true;
		}

		$title = $skinTemplate->getSkin()->getTitle();
		// This hook doesn't usually get called for special pages,
		// but sometimes it is.
		if ( $title->isSpecialPage() ) {
			return true;
		}

		$toolbox['mpdf'] = [
			'msg' => 'mpdf-action',
			'href' => $title->getLocalUrl( [ 'action' => 'mpdf' ] ),
			'id' => 't-mpdf',
			'rel' => 'mpdf'
		];

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
