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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>&Config.Title; Édition : &Page.Name;</title>
<meta name="robots" content="noindex,nofollow"/>
<meta name="Generator" content="&Config.Version;"/>
<?php include('styleswitcher.php') ?>
</head>

<body>
<p id="Logo">&Config.Title;</p>

<h1>Édition : &Page.Name;</h1>

<div id="Content">
&Page.Html;
</div>

<form method="post" action="#">
<div>
<textarea id="Wiki" name="Wiki" cols="80" rows="20">&Page.Wiki;</textarea>
</div>
<p id="PPreviewSave"><input type="submit" id="Preview" name="Preview" value="Prévisualiser"/><input type="submit" id="Save" name="Save" value="Sauvegarder"/></p>
</form>

<div id="Rules">
<h2>Règles de formatage</h2>
<dl>
	<dt><code>_texte_</code></dt><dd>Faire une emphase</dd>
	<dt><code>__texte__</code></dt><dd>Faire une emphase forte</dd>
	<dt><code>@@texte@@</code></dt><dd>Faire un petit code</dd>
	<dt><code>''texte|langue|source''</code></dt><dd>Faire une petite citation</dd>
	<dt><code>&gt;texte</code></dt><dd>Faire un paragraphe de citation</dd>
	<dt><code>[texte|URI|langue|titre]</code></dt><dd>Faire un lien vers une page, les paramètres sont optionnels</dd>
	<dt><code>((image|texte alternatif|alignement))</code></dt><dd>Ajouter une image, alignement peut valoir G(auche), D(roite) ou C(entre)</dd>
	<dt><code>texte</code></dt><dd>Tout texte écrit simplement sera transformé en paragraphes.</dd>
	<dt><code>!titre1, !!titre2, !!!titre3, etc</code></dt><dd>Créer un titre d'un niveau égal au nombre de !</dd>
	<dt><code>-texte ou *texte</code></dt><dd>Faire une liste d'éléments non numérotés</dd>
	<dt><code>#texte</code></dt><dd>Faire une liste d'éléments numérotés</dd>
	<dt><code>;titre:définition</code></dt><dd>Faire une définition/liste de définitions</dd>
	<dt><code>??acronyme|titre??</code></dt><dd>Faire un acronyme</dd>
	<dt><code>[ESPACE]texte</code></dt><dd>Le texte sera préformaté, utile pour écrire des bouts de code</dd>
	<dt><code>==== ou ---- (au moins 4)</code></dt><dd>Une ligne de séparation horizontale</dd>
	<dt><code>\</code></dt><dd>Si vous ne voulez pas que la syntaxe wiki s'applique, faites précéder les caractères spéciaux par des anti-slashs.</dd>
</dl>
</div>

<hr id="UtilsSeparator"/>
<ul id="Utils">
	<li><a href="&Config.WikiURI;&Config.DefaultPage;">&Config.DefaultPage;</a></li>
	<li><a href="&Config.WikiURI;&Config.ListPage;">&Config.ListPage;</a></li>
	<li><a href="&Config.WikiURI;&Config.ChangesPage;">&Config.ChangesPage;</a></li>
	<li><a href="&Config.WikiURI;&Page.Name;">Retour à la page</a></li>
	<li><a href="&Config.HistoryURI;&Page.Name;">Historique de cette page</a></li>
</ul>

</body>
</html>
