<?php
class CLanguageFormat
{
	function FormatWiki($strWikiContent)
	{
		return $strWikiContent;
	}
	
	function FormatHtml($strHtmlContent)
	{
		$strSpaces = '['.chr(0x20).chr(0xa0).chr(0x0a).',.(){}<>]';
		$astrSources = array(
			// Nombre ordinaux
			'/('.$strSpaces.')1(er|re)(s?)(?='.$strSpaces.')/',
			'/('.$strSpaces.')2nd(e?)(s?)(?='.$strSpaces.')/',
			'/('.$strSpaces.')([23456789])e(s?)(?='.$strSpaces.')/',
			'/('.$strSpaces.')([0123456789]{2,})e(s?)(?='.$strSpaces.')/',
	
			// Espace insécables
			'/ (!|\?|:|;|»)/', 
			'/(«) /'
		);
		$astrDestinations = array(
			// Nombre ordinaux
			'${1}1<sup>$2$3</sup>$4',
			'${1}2<sup>nd$2$3</sup>$4',
			'$1$2<sup>e$3</sup>$4',
			'$1$2<sup>e$3</sup>$4',
	
			// Espace insécables
			' $1',
			'$1 '
		);
		
		return preg_replace($astrSources, $astrDestinations, $strHtmlContent);
	}
}
?>