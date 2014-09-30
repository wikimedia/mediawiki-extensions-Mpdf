<?php

class MpdfHooks {

	/**
	 *
	 * @param OutputPage $output
	 * @param Article $article
	 * @param Title $title
	 * @param User $user
	 * @param WebRequest $request
	 * @param MediaWiki $wiki
	 * @return boolean
	 */
	public static function onMediaWikiPerformAction( $output, $article, $title, $user, $request, $wiki ) {

		if( $request->getText( 'action' ) == 'mpdf' ) {

			$titletext = $title->getPrefixedText();
			$filename = str_replace( array('\\', '/', ':', '*', '?', '"', '<', '>', "\n", "\r" ), '_', $titletext );

			$options = $article->getParserOptions();
			$options->setIsPrintable( true );
			$options->setEditSection( false );
			$article->mParserOptions = $options;
			$article->view();
			$html = $article->getContext()->getOutput()->getHTML();

			// Initialise PDF variables
			$format  = $request->getText( 'format' );

			// If format=html in query-string, return html content directly
			if( $format == 'html' ) {
				$output->disable();
				header( "Content-Type: text/html" );
				header( "Content-Disposition: attachment; filename=\"$filename.html\"" );
				print $html;
			}
			else { //return pdf file
				$mode = 'utf-8';
				$format = 'A4';
				$marginLeft = 15;
				$marginRight = 15;
				$marginTop = 16;
				$marginBottom = 16;
				$marginHeader = 9;
				$marginFooter = 9;
				$orientation = 'P';
				list( ,$constr ) = explode('<!--mpdf<constructor', $html, 2 );
				if ( $constr ) {
					list( $constr ) = explode( '/>', $constr, 1 );
					$matches = array();
					if ( preg_match( '/format\s*=\s*"(.*?)"/', $constr, $matches ) ){
						$format = $matches[1];
					}
					if ( preg_match( '/margin-left\s*=\s*"?([0-9\.]+)/', $constr, $matches ) ){
						$marginLeft = (float)$matches[1];
					}
					if ( preg_match( '/margin-right\s*=\s*"?([0-9\.]+)/', $constr, $matches ) ){
						$marginRight = (float)$matches[1];
					}
					if ( preg_match( '/margin-top\s*=\s*"?([0-9\.]+)/', $constr, $matches ) ){
						$marginTop = (float)$matches[1];
					}
					if ( preg_match( '/margin-bottom\s*=\s*"?([0-9\.]+)/', $constr, $matches ) ){
						$marginBottom = (float)$matches[1];
					}
					if ( preg_match( '/margin-header\s*=\s*"?([0-9\.]+)/', $constr, $matches ) ){
						$marginHeader = (float)$matches[1];
					}
					if ( preg_match( '/margin-footer\s*=\s*"?([0-9\.]+)/', $constr, $matches ) ){
						$marginFooter = (float)$matches[1];
					}
					if ( preg_match( '/orientation\s*=\s*"(.*?)"/', $constr, $matches ) ){
						$orientation = $matches[1];
					}
				}
				$mpdf=new mPDF( $mode, $format, 0, '', $marginLeft, $marginRight, $marginTop, $marginBottom, $marginHeader, $marginFooter, $orientation );

				$mpdf->WriteHTML( $html );
				$mpdf->Output( $filename.'.pdf', 'D' );
			}
			$output->disable();
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
				'text' => wfMessage( 'mpdf-action' )->text(),
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
		$ret = '<!--mpdf';
		foreach ($params as $value) {
			$ret.="<".  $value ." />\n";
		}

		//Return mpdf tags as raw html
		return $parser->insertStripItem( $ret."mpdf-->\n", $parser->mStripState );
	}
}
