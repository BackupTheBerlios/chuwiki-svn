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
			'/('.$strSpaces.')([0123456789]*1)st(s?)(?='.$strSpaces.')/',
			'/('.$strSpaces.')([0123456789]*2)nd(s?)(?='.$strSpaces.')/',
			'/('.$strSpaces.')([0123456789]*[03456789])th(s?)(?='.$strSpaces.')/',
		);
		$astrDestinations = array(
			// Nombre ordinaux
			'$1$2<sup>st$3</sup>$4',
			'$1$2<sup>nd$3</sup>$4',
			'$1$2<sup>th$3</sup>$4',
		);
		
		return preg_replace($astrSources, $astrDestinations, $strHtmlContent);
	}
}
?>