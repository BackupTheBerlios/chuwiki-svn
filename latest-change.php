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

require(dirname(__FILE__) . '/sdk/sdk.php');
/////////////////////////////////////////////////////////////

function FormatRfc1123Date($strDate)
{
	$strYear = substr($strDate, 0, 4);
	$strMonth = substr($strDate, 4, 2);
	$strDay = substr($strDate, 6, 2);
	$strHour = substr($strDate, 8, 2);
	$strMinute = substr($strDate, 10, 2);
	$strSecond = substr($strDate, 12, 2);
	$date = mktime($strHour, $strMinute, $strSecond, $strMonth, $strDay, $strYear);
	return gmdate('D, d M Y H:i:s', $date) . ' GMT';
}

function FormatIsoShort8601Date($strDate)
{
	$strYear = substr($strDate, 0, 4);
	$strMonth = substr($strDate, 4, 2);
	$strDay = substr($strDate, 6, 2);
	$strHour = substr($strDate, 8, 2);
	$strMinute = substr($strDate, 10, 2);
	$strSecond = substr($strDate, 12, 2);
	$date = mktime($strHour, $strMinute, $strSecond, $strMonth, $strDay, $strYear);
	return date('Y-m-d H:i:s', $date);
}

$astrLatestChanges = GetLatestChangePageList();
$strLatestDate = reset($astrLatestChanges);
$strURI = 'http://' . $_SERVER['SERVER_NAME'] . $k_strWikiURI;

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
////////////////////////////////////////////////////////////////////////////////
?>
<rss version="2.0">
	<channel>
		<title>ChuWiki - Changements récents</title>
		<link><?php echo $strURI ?></link>
		<description></description>

		<lastBuildDate><?php echo FormatRfc1123Date($strLatestDate) ?></lastBuildDate>

<?php
foreach($astrLatestChanges as $strPage => $strDate)
{
?>
		<item>
			<title><?php echo $strPage ?></title>
			<link><?php echo $strURI ?>/wiki/<?php echo rawurlencode($strPage) ?></link>
			<pubDate><?php echo FormatRfc1123Date($strDate) ?></pubDate>
			<dc:date><?php echo FormatIsoShort8601Date($strDate) ?></dc:date>
		</item>
<?php
}
?>
   </channel>
</rss>
