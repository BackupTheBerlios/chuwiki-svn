<?php
////////////////////////////////////////////////////////////////////////////////
// ***** BEGIN LICENSE BLOCK *****
// This file is part of ChuWiki.
// Copyright (c) 2004 Vincent Robert and contributors. All rights
// reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA//
//
// ***** END LICENSE BLOCK *****
////////////////////////////////////////////////////////////////////////////////

error_reporting(E_ALL);

$k_strVersion = 'ChuWiki 1.3α';

// Les fonctions d'ouverture de fichier doivent utiliser ou non 
// la zlib selon que celle-ci est présente ou pas
if ( function_exists('gzfile') ) // zlib disponible
{
	$ChuFile = 'gzfile';
	$ChuOpen = 'gzopen';
	$ChuWrite = 'gzwrite';
	$ChuClose = 'gzclose';
	$k_strExtension = 'gz';

	// Active la compression du contenu
	ob_start('ob_gzhandler');
}
else
{
	$ChuFile = 'file';
	$ChuOpen = 'fopen';
	$ChuWrite = 'fwrite';
	$ChuClose = 'fclose';
	$k_strExtension = 'txt';
	ob_start();
}


// Chargement des configuration
$k_aConfig = ParseIniFile(dirname(__FILE__) . '/../configuration.ini');
$k_aLangConfig = ParseIniFile(dirname(__FILE__) . '/../' . $k_aConfig['LanguagePath'] . '/' . 'lang.ini');

///////////////////////////////////////////////////////////////////

// Construction de l'URI où est installé ChuWiki
$k_strWikiURI = dirname($_SERVER['SCRIPT_NAME']) . '/';
if ( $k_strWikiURI == '//' || $k_strWikiURI == './' )
{
	$k_strWikiURI = '/';
}

///////////////////////////////////////////////////////////////////
function ParseIniFile($strFileName)
{
	if( !file_exists($strFileName) )
	{
		Error('Fichier de configuration manquant ' . $strFileName);
	}
	
	$strContent = LoadFile($strFileName);
	$astrLines = explode("\n", $strContent);

	$aVars = array();

	foreach($astrLines as $strLine)
	{
		// Commentaires
		if( substr($strLine, 0, 1) == ';' )
		{
			continue;
		}
		
		$nMiddle = strpos($strLine, '=');
		if( $nMiddle)
		{
			$strName = trim(substr($strLine, 0, $nMiddle));
			$strValue = trim(substr($strLine, $nMiddle + 1));

			$aVars[$strName] = $strValue;
		}
	}
	
	return $aVars;
}

// Utile seulement pour les templates souhaitant
// accéder en PHP à des variables de la config
function GetConfigVar($strVarName)
{
	global $k_aConfig;
	return $k_aConfig[$strVarName];
}

// Utile seulement pour les templates souhaitant
// accéder en PHP à des variables de la config
function GetLangVar($strVarName)
{
	global $k_aLangConfig;
	return $k_aLangConfig[$strVarName];
}

function GetUriInfo()
{
	global $k_aConfig;

	$strPage = '';

	// L'URI peut être composée de 3 parties :
	// le script, le séparateur de page, et la page
	// Il faut extraire le script et la page	

	// Sans PathInfo
	if( $k_aConfig['UsePathInfo'] != 'true' )
	{
		$strPage = urldecode($_SERVER['QUERY_STRING']);
	}
	// Avec PathInfo
	else if ( isset($_SERVER['PATH_INFO']) )
	{
		$strPage = substr($_SERVER['PATH_INFO'], 1);
	}

	$nStart = strrpos($_SERVER['REQUEST_URI'], $strPage);
	$strScript = substr($_SERVER['REQUEST_URI'], 0, $nStart);

	$strSeparator = GetPageSeparator();
	$nSeparatorLength = strlen($strSeparator);
	if( substr($strScript, -$nSeparatorLength) != $strSeparator )
	{
		// Il n'y a pas de séparateur à la fin du script, on l'ajoute
		$strScript .= $strSeparator;
	}
	
	return array('Page' => $strPage, 'Script' => $strScript);
}

function FileNameEncode($strFileName)
{
	$strReturn = rawurlencode($strFileName);
	return $strReturn;
}

function Error($strMessage)
{
    header('Content-Type: text/html;charset=UTF-8');
	echo '<h1>Error</h1>' . "\n";
	echo '<p>' . $strMessage . '</p>';
	exit();
}

function ErrorUnableToWrite()
{
	Error('Impossible d\'écrire cette page, veuillez vérifier que vous possédez les droits d\'écriture dans le répertoire des pages');
}

function GetCurrentPage()
{
	global $k_aConfig, $k_aLangConfig, $k_strWikiURI;

	$strPage = '';
	
	// Récupère la page demandée
	$aInfo = GetUriInfo();
	$strPage = $aInfo['Page'];
	$strScript = $aInfo['Script'];

	// Gestion de magic_quotes
	if ( get_magic_quotes_gpc() )
	{
		$strPage = stripslashes($strPage);
	}

	// Si la page n'est pas spécifiée, on redirige vers la page par défaut
	if ( $strPage == '' )
	{
		header('Location: ' . $strScript . $k_aLangConfig['DefaultPage']);
		exit();
	}

	// Si la page contient des caractères invalides, on les remplace par des tirets et on redirige
	if ( strstr($strPage, '/') !== FALSE || strstr($strPage, '"') !== FALSE )
	{
		$aBads = array('/', '"');
		$strPage = str_replace($aBads, '-', $strPage);
	
		header('Location: ' . $strScript .  $strPage);
		exit();
	}

	return $strPage;
}

function GetPageSeparator()
{
	global $k_aConfig;
	
	if( $k_aConfig['UsePathInfo'] == 'true' )
	{
		return '/';
	}
	else
	{
		return '?';
	}
}

function GetPagePath()
{
	global $k_aConfig;
	return dirname(__FILE__) . '/../' . FileNameEncode($k_aConfig['PagePath']);
}

function GetScriptURI($strScriptName)
{
	global $k_strWikiURI, $k_aConfig;
	return $k_strWikiURI . $k_aConfig[$strScriptName . 'Script'] . GetPageSeparator();
}


// Merci à Darken pour cette fonction
function VerifyUtf8($str)
{
	$nLength = strlen($str);
	$iDst = 0;
	$nByteSequence = 0;
	$nUcs4 = 0;

	for($iSrc = 0; $iSrc < $nLength; ++$iSrc)
	{
		$nByte = ord($str[$iSrc]);

		if( $nByteSequence == 0)
		{
			$nUcs4 = 0;

			if( $nByte <= 0x7F)
			{
				// ascii
				$iDst++;
			}
			else if( ($nByte & 0xE0) == 0xC0)
			{
				// 110xxxxx 10xxxxxx
				$nUcs4 = $nByte & 0x1F;
				$nByteSequence = 1;
			}
			else if( ($nByte & 0xF0) == 0xE0)
			{
				// 1110xxxx 10xxxxxx 10xxxxxx
				$nUcs4 = $nByte & 0x0F;
				$nByteSequence = 2;
			}
			else if( ($nByte & 0xF8) == 0xF0)
			{
				// 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
				$nUcs4 = $nByte & 0x07;
				$nByteSequence = 3;
			}
			else if( ($nByte & 0xFC) == 0xF8)
			{
				// 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
				$nUcs4 = $nByte & 0x03;
				$nByteSequence = 4;
			}
			else if( ($nByte & 0xFE) == 0xFC)
			{
				// 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
				$nUcs4 = $nByte & 0x01;
				$nByteSequence = 5;
			}
			else
			{
				// Bad byte sequence starter
				$strBeg = substr($str, 0, $iSrc);
				$strBeg = XhtmlSpecialChars($strBeg);
				echo "<p>$strBeg &lt;-- BAD UTF-8 SEQUENCE STARTER</p>";
				return false;
			}
		}
		else
		{
			// Remaining bytes
			if( ($nByte & 0xC0) != 0x80)
			{
				// Bad byte in sequence
				$strBeg = substr($str, 0, $iSrc);
				$strBeg = XhtmlSpecialChars($strBeg);
				echo "<p>$strBeg &lt;-- BAD UTF-8 SEQUENCE BYTE</p>";
				return false;
			}

			$nUcs4 <<= 6;
			$nUcs4 |= ($nByte & 0x3F);
			$nByteSequence--;

			if( $nByteSequence == 0)
			{
				// OK - Store
				//nUcs4
				$iDst++;
			}
		}
	}
	return true;
}


function LoadFile($strFilePath)
{
	global $ChuFile;

	$strFilePath = str_replace('%2F', '/', FileNameEncode($strFilePath));

	if ( !is_file($strFilePath) )
	{
		return '';
	}

	$strContent = implode('', $ChuFile($strFilePath));
	$strContent = str_replace("\r", '', $strContent);

	if( !VerifyUtf8($strContent) )
	{
		Error('Le fichier ' . $strFilePath . ' n\'est pas correctement enregistré en UTF-8');
	}
	
	return $strContent;
}

function InterpretPhpFile($strFilePath)
{
	ob_start();
	include($strFilePath);
	$strContent = ob_get_contents();
	ob_end_clean();
	return $strContent;
}

function GetLatestDateFilePath($strPage)
{
	$strPagePath = GetPagePath();
	$strPage = FileNameEncode($strPage);

	return $strPagePath . '/' . $strPage . '/latest-change.txt';
}

function SaveDateLatest($strPage, $strDateLatest)
{
	$strChangeFile = GetLatestDateFilePath($strPage);
	$file = @fopen($strChangeFile, 'w');
	if ( $file === FALSE )
	{
		return;
	}
	fwrite($file, $strDateLatest);
	fclose($file);	
	@chmod($strChangeFile, 0777);
}

function GetWikiContentFile($strPage, $strDate)
{
	global $k_strExtension;
	return GetPagePath(). '/' . $strPage .  '/' . $strDate . '.' . $k_strExtension;
}

function GetLatestDate($strPage)
{
	$strDateLatestFilePath = GetLatestDateFilePath($strPage);
	$strDateLatest = @implode('', file($strDateLatestFilePath));
	$strFileLatest = GetWikiContentFile($strPage, $strDateLatest);

	// Si le cache n'existe pas ou que la page indiquée a été supprimée
	// On va devoir recréer le cache
	if( $strDateLatest == '' || !is_file($strFileLatest) )
	{
		$aHistory = GetHistory($strPage);
		$strDateLatest = reset($aHistory);

		// Comme on est passé par l'ancienne méthode 
		// qui n'utilisait pas le cache,
		// on peut maintenant enregistrer le cache
		SaveDateLatest($strPage, $strDateLatest);
	}
	return $strDateLatest;
}

function GetWikiContent($strPage)
{
	$strLatestDate = GetLatestDate($strPage);

	return GetSavedWikiContent($strPage, $strLatestDate);
}

function GetSavedWikiContent($strPage, $strDate)
{
	global $k_strExtension;

	$strSavePath = GetWikiContentFile($strPage, $strDate);
	$strContent =  LoadFile($strSavePath);

	return $strContent;
}

function RenderPage($strPage)
{
	$strWikiContent = GetWikiContent($strPage);
	$strModifiedWikiContent = $strWikiContent . GetSpecialContent($strPage);

	return Render($strModifiedWikiContent);
}

function Render($strWikiContent)
{
	global $k_aConfig, $k_aLangConfig;

	if ( $strWikiContent == '' )
	{
		$strWikiContent = $k_aLangConfig['NoWikiContent'];
	}

	// On utilise le fichier de formatage de la langue s'il existe	
	$strFileFormat = $k_aConfig['LanguagePath'] . '/format.php';
	$formatter = null;
	if( file_exists($strFileFormat) )
	{
		require_once(dirname(__FILE__) . '/../' . $strFileFormat);

		if( class_exists('CLanguageFormat') )
		{
			$formatter = new CLanguageFormat();
		}
	}
	
	// Modification du contenu wiki par la langue
	if(	is_a($formatter, 'CLanguageFormat') )
	{
		$strWikiContent = $formatter->FormatWiki($strWikiContent);
	}
	
	// Instanciation de la lib de rendu et rendu wiki
	switch($k_aConfig['Renderer'])
	{
	case 'WikiRenderer':
		require_once(dirname(__FILE__) . '/WikiRenderer/WikiRenderer.lib.php');
		require_once(dirname(__FILE__) . '/WikiRenderer/WikiRenderer_chu.conf.php');

		$Config = new ChuWikiConfig();
		$Renderer = new WikiRenderer($Config);
		$strHtmlContent = $Renderer->Render($strWikiContent);
		break;

	case 'wiki2xhtml':
		require_once(dirname(__FILE__) . '/wiki2xhtml/class.wiki2xhtml.php');
		$Renderer = new wiki2xhtml();
		$strHtmlContent = $Renderer->transform($strWikiContent);
		break;

	default:
		Error('Erreur dans le fichier de configuration : Aucun renderer ou mauvais renderer spécifié. Seulement WikiRenderer ou wiki2xhtml sont autorisés.');
		break;
	}

	// Sans PathInfo, il faut mettre un ? devant les liens vers les pages internes
	if( $k_aConfig['UsePathInfo'] != 'true' )
	{
		$strHtmlContent = preg_replace('/href="(.*)"/', 'href="?\1"', $strHtmlContent);
		$strHtmlContent = preg_replace('/href="\?(\.\..*)"/', 'href="\1"', $strHtmlContent);
		$strHtmlContent = preg_replace('/href="\?(\/.*)"/', 'href="\1"', $strHtmlContent);
		$strHtmlContent = preg_replace('/href="\?([a-zA-Z]+:.*)"/', 'href="\1"', $strHtmlContent);
		$strHtmlContent = preg_replace('/href="\?(#.*)"/', 'href="\1"', $strHtmlContent);
	}

	if ( $k_aConfig['SmileyPath'] != '' )
	{
		require_once(dirname(__FILE__) . '/smiley-replacer.php');
		MakeImageSmileys($strHtmlContent);
	}

	// Modification du contenu HTML par la langue
	if(	is_a($formatter, 'CLanguageFormat') )
	{
		$strHtmlContent = $formatter->FormatHtml($strHtmlContent);
	}
	
	return $strHtmlContent;
}

function LoadTemplate($strTemplate)
{
	global $k_aConfig;

	$strTemplatePath = $k_aConfig['ThemePath'] . '/' . $strTemplate . '.php';
	
	// Un chargement avant pour vérifier l'intégrité
	LoadFile($strTemplatePath);
	
	return InterpretPhpFile($strTemplatePath);
}

function BuildStandardReplacements()
{
	global $k_aConfig, $k_aLangConfig, $k_strVersion, $k_strWikiURI;

	$astrReplacements = array('Vars' => array(), 'Values' => array());

	// Ajout des variables du fichier configuration.ini
	foreach($k_aConfig as $strVar => $strValue)
	{
		AddReplacement($astrReplacements, 'Config.' . $strVar, $strValue);
	}

	// Ajout des variables de configurations supplémentaires
	AddReplacement($astrReplacements, 'Config.URI', $k_strWikiURI);
	AddReplacement($astrReplacements, 'Config.Version', $k_strVersion);
	AddReplacement($astrReplacements, 'Config.PageSeparator', GetPageSeparator());
	AddReplacement($astrReplacements, 'Config.WikiURI', GetScriptURI('Wiki'));
	AddReplacement($astrReplacements, 'Config.EditURI', GetScriptURI('Edit'));
	AddReplacement($astrReplacements, 'Config.HistoryURI', GetScriptURI('History'));

	// Ajout des variables da la langue
	foreach($k_aLangConfig as $strVar => $strValue)
	{
		AddReplacement($astrReplacements, 'Lang.' . $strVar, $strValue);
	}

	// Ajout des variables de langue supplémentaires
	AddReplacement($astrReplacements, 'Lang.Rules', LoadFile($k_aConfig['LanguagePath'] . '/rules.html'));
	
	return $astrReplacements;
}

function AddReplacement(&$astrReplacements, $strVar, $strValue)
{
	$astrReplacements['Vars'][] = '&' . $strVar . ';';
	$astrReplacements['Values'][] = $strValue;
}

function ReplaceAll($strContent, $astrReplacements)
{
	return str_replace($astrReplacements['Vars'], $astrReplacements['Values'], $strContent);
}

function CreateDir($strDir)
{
	if( !is_dir($strDir) )
	{
		mkdir($strDir);
		chmod($strDir, 0777);
	}
}

function Save($strPage, $strWikiContent)
{
	global $k_strExtension, $k_aConfig, $ChuOpen, $ChuWrite, $ChuClose;

	$strPageEncoded = FileNameEncode($strPage);

	// Création du répertoire des pages
	$strSavePath = GetPagePath();
	CreateDir($strSavePath);
	
	// Création du répertoire de la page
	$strSavePath .= '/' . $strPageEncoded;
	CreateDir($strSavePath);

	if( file_exists($strSavePath . '/lock') )
	{
		// Cette page est protégée
		ErrorUnableToWrite();
	}

	// On enregistre le contenu du fichier
	$strDate = date('YmdHis');
	$strSavePath .= '/' . $strDate . '.' . $k_strExtension;
	$file = $ChuOpen($strSavePath, 'w9');
	if ( $file === FALSE )
	{
		// Impossible d'ouvrir le fichier en écriture
		ErrorUnableToWrite();
	}
	$ChuWrite($file, $strWikiContent);
	$ChuClose($file);
	@chmod($strSavePath, 0777);

	// On enregistre le fichier indiquant le dernier changement	
	SaveDateLatest($strPage, $strDate);
}

function FormatDate($date)
{
	return $strDate = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) 
			. ' T ' . substr($date, 8, 2) . ':' . substr($date, 10, 2) . ':' . substr($date, 12, 2);
}

function IsArchiveFile($strFile)
{
	$astr = explode('.', $strFile);
	if( preg_match('/[0-9]{14}/', $astr[0]) == 1)
	{
		return true;
	}
	return false;
}

function GetHistory($strPage)
{
	global $k_aConfig;

	$strPagePath = GetPagePath();
	$strPage = FileNameEncode($strPage);

	$aHistory = array();

	$strDateLatestFilePath = GetLatestDateFilePath($strPage);
	$strDirPath = $strPagePath . '/' . $strPage;
	$dir = @opendir($strDirPath);
	if ( $dir !== FALSE )
	{
		while( true )
		{
			$strEntry = readdir($dir);
			if( $strEntry === false )
			{
				break;
			}
			$strFilePath = $strDirPath . '/' . $strEntry;
			if ( IsArchiveFile($strEntry) )
			{
				$astr = explode('.', $strEntry);
				$aHistory[] = $astr[0];
			}
		}
		closedir($dir);
	}
	rsort($aHistory);

	return $aHistory;
}


function GetPageList()
{
	global $k_aConfig;

	$strPagePath = GetPagePath();

	$astrList = array();
	if( !is_dir($strPagePath) )
	{
		return $astrList;
	}
	
	$dir = opendir($strPagePath);
	while( true )
	{
		$strEntry = readdir($dir);
		if( $strEntry === false )
		{
			break;
		}
		$strFullPath = $strPagePath . '/' . $strEntry;
		if ( $strEntry != '.' && $strEntry != '..' && is_dir($strFullPath) )
		{
			$strEntry = rawurldecode($strEntry);
			$astrList[$strEntry] = GetLatestDate($strEntry);
		}
	}
	closedir($dir);

	return $astrList;
}

function GetSortedPageList()
{
	$astrList = GetPageList();
	asort($astrList);

	return $astrList;
}

function GetLatestChangePageList()
{
	$astrList = GetPageList();
	arsort($astrList);

	return $astrList;
}

function GetPageListContent()
{
	global $k_aConfig;

	$astrList = GetPageList();

	$strContent = '';
	foreach($astrList as $strEntry => $date)
	{
			$strContent .= "\n" . '-[' . $strEntry . ']';
	}

	return $strContent;
}

function GetRecentChangeContent()
{
	global $k_aConfig, $k_strWikiURI;

	define('CookieName', 'RecentChanges');

	$astrList = GetLatestChangePageList();

	// Récupération de la dernière visite
	$dateLastVisit = isset($_COOKIE[CookieName]) ? $_COOKIE[CookieName] : 0;

	$strContent = '';
	$strDayPrev = '';
	foreach($astrList as $strEntry => $date)
	{
		$strDay = substr($date, 0, 8);
		$strTime = substr($date, 8);

		if( $strDay != $strDayPrev )
		{
			$strContent .= "\n" . '!' . substr($strDay, 0, 4)
								. '-' . substr($strDay, 4, 2) 
								. '-' . substr($strDay, 6, 2);
		}

		$bNew = ( ($date - $dateLastVisit) > 0 );

		$strContent .= "\n" . '- ';
		if ( $bNew )
		{
			$strContent .= '__';
		}
		$strContent .= substr($strTime, 0, 2) . ':' . substr($strTime, 2, 2) 
					. ' [' . $strEntry . ']';
		if ( $bNew )
		{
			$strContent .= '__';
		}

		$strDayPrev = $strDay;
	}

	// Enregistrement de la dernière date
	$dateLatest = reset($astrList);
	setcookie(CookieName, $dateLatest, time() + 3600 * 24 * 365, $k_strWikiURI);

	return $strContent;
}

function GetSpecialContent($strPage)
{
	global $k_aLangConfig;

	$strSpecial = '';

	// Si c'est la page de listage, on ajoute la liste après.
	if ( $strPage == $k_aLangConfig['ListPage'] )
	{
		$strSpecial .= GetPageListContent();
	}

	// Si c'est la page de changement, on les ajoute après
	if ( $strPage == $k_aLangConfig['ChangesPage'] )
	{
		$strSpecial .= GetRecentChangeContent();
	}

	return $strSpecial;
}

function WriteXhtmlHeader()
{
	$strCharset = 'UTF-8';

	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT");             // Date du passé
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // toujours modifié
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
  
	if ( @stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') ) 
	{
		header('Content-type: application/xhtml+xml; charset=' . $strCharset);
		echo '<?xml version="1.0" encoding="' . $strCharset . '"?>' . "\n";
	}
	else 
	{
		header('Content-type: text/html; charset=' . $strCharset . '');
	}
}


?>