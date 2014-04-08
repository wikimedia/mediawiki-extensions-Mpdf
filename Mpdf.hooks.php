<?php

class MpdfHooks {

	/**
	 * Perform the export operation
	 *
	 * @param $action
	 * @param Article $article
	 *
	 * @return bool
	 */
	public static function onUnknownAction( $action, $article ) {
		global $wgOut, $wgRequest;

		if( $action == 'mpdf' ) {

			$title = $article->getTitle();
			$titletext = $title->getPrefixedText();
			$filename = str_replace( array('\\', '/', ':', '*', '?', '"', '<', '>', "\n", "\r" ), '_', $titletext );

			$options = $article->getParserOptions();
			$options->setIsPrintable( true );
			$article->mParserOptions = $options;
			$article->view();
			$html = $article->getContext()->getOutput()->getHTML();

			// Initialise PDF variables
			$format  = $wgRequest->getText( 'format' );

			// If format=html in query-string, return html content directly
			if( $format == 'html' ) {
				$wgOut->disable();
				header( "Content-Type: text/html" );
				header( "Content-Disposition: attachment; filename=\"$filename.html\"" );
				print $html;
			}
			else { //return pdf file
				include("mpdf/mpdf.php");
				$mpdf=new mPDF(); 

				$mpdf->WriteHTML( $html );
				$mpdf->Output( $filename.'.pdf', 'D' );
			}
			return false;
		}

		return true;
	}


	/**
	 * Add PDF to actions tabs in MonoBook based skins
	 * @param Skin $skin
	 * @param array $actions
	 *
	 * @return bool true
	 */
	public static function onSkinTemplateTabs( $skin, &$actions ) {
		global $wgMpdfTab;

		if ( $wgMpdfTab ) {
			$actions['mpdf'] = array(
				'class' => false,
				'text' => wfMsg( 'mpdf-action' ),
				'href' => $skin->getTitle()->getLocalURL( "action=mpdf" ),
			);
		}
		return true;
	}


	/**
	 * Add PDF to actions tabs in vector based skins
	 * @param Skin $skin
	 * @param array $actions
	 *
	 * @return bool true
	 */
	public static function onSkinTemplateNavigation( $skin, &$actions ) {
		global $wgMpdfTab;

		if ( $wgMpdfTab ) {
			$actions['views']['mpdf'] = array(
				'class' => false,
				'text' => wfMsg( 'mpdf-action' ),
				'href' => $skin->getTitle()->getLocalURL( "action=mpdf" ),
			);
		}
		return true;
	}

	/**
	 * @param $parser Parser
	 * @return mixed
	 */
	public static function mpdftags_Render( &$parser ) {
		// Get the parameters that were passed to this function
		$params = func_get_args();
		array_shift( $params );

		// Replace open and close tag for security reason
		$params = str_replace(array('<', '>'), array('&lt;', '&gt;'), $params);

		// Insert mpdf tags between <!--mpdf ... mpdf-->
		$ret = "<!--mpdf ";
		foreach ($params as $value) {
			$ret.="<".  $value ." />\n";
		}

		//Return mpdf tags as raw html
		return $parser->insertStripItem( $ret."mpdf-->\n", $parser->mStripState );
	}
}
