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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
				"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>&Config.Title; Historique : &Page.Name;</title>
<meta name="robots" content="noindex,nofollow"/>
<meta name="Generator" content="&Config.Version;"/>
<?php include('styleswitcher.php') ?>
</head>

<body>
<p id="Logo">&Config.Title;</p>

<h1>Historique : &Page.Name;</h1>

<div id="Content">
&Page.Html;
</div>

<form method="post" action="">
<div>
<select name="Date" size="10">
&Page.History;
</select>
</div>
<p id="PPreviewSave"><input type="submit" id="Preview" name="Preview" value="Prévisualiser"/><input type="submit" id="Save" name="Save" value="Restaurer"/></p>
</form>

<hr id="UtilsSeparator"/>
<ul id="Utils">
	<li><a href="&Config.WikiURI;&Theme.DefaultPage;">&Theme.DefaultPage;</a></li>
	<li><a href="&Config.WikiURI;&Theme.ListPage;">&Theme.ListPage;</a></li>
	<li><a href="&Config.WikiURI;&Theme.ChangesPage;">&Theme.ChangesPage;</a></li>
	<li><a href="&Config.EditURI;&Page.Name;#Wiki">Éditer cette page</a></li>
	<li><a href="&Config.WikiURI;&Page.Name;">Retour à la page</a></li>
</ul>

</body>
</html>
