<?php

class MpdfHooks {

	/**
	 * Perform the export operation
	 */
	public static function onUnknownAction( $action, $article ) {
		global $wgOut, $wgRequest;
		global $wgServer, $wgArticlePath, $wgScriptPath, $wgUploadPath, $wgUploadDirectory, $wgScript;

		if( $action == 'mpdf' ) {

			$title = $article->getTitle();
			$titletext = $title->getPrefixedText();
			$filename = str_replace( array('\\', '/', ':', '*', '?', '"', '<', '>', "\n", "\r" ), '_', $titletext );
			$text = $article->fetchContent();

			$wgOut->setPrintable();

			$wgOut->addWikiText( $text );
			$wgOut->setHTMLTitle( $titletext );
			
			ob_start();
			$wgOut->output();
			$html=ob_get_contents();
			ob_end_clean();

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
	 */
	public static function onSkinTemplateTabs( $skin, &$actions) {
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
        
        public static function mpdftags_Render( &$parser )
        {
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
