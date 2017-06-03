<?php
$allowedTags = array (
		"<menu>",
		"<command>",
		"<summary>",
		"<details>",
		"<meter>",
		"<progress>",
		"<output>",
		"<keygen>",
		"<textarea>",
		"<option>",
		"<optgroup>",
		"<datalist>",
		"<select>",
		"<button>",
		"<input>",
		"<label>",
		"<legend>",
		"<fieldset>",
		"<form>",
		"<th>",
		"<td>",
		"<tr>",
		"<tfoot>",
		"<thead>",
		"<tbody>",
		"<col>",
		"<colgroup>",
		"<caption>",
		"<table>",
		"<math>",
		"<svg>",
		"<area>",
		"<map>",
		"<canvas>",
		"<track>",
		"<source>",
		"<audio>",
		"<video>",
		"<param>",
		"<object>",
		"<embed>",
		"<iframe>",
		"<img>",
		"<del>",
		"<ins>",
		"<wbr>",
		"<br>",
		"<br/>",
		"<span>",
		"<font>",
		"<bdo>",
		"<bdi>",
		"<rp>",
		"<rt>",
		"<ruby>",
		"<mark>",
		"<u>",
		"<b>",
		"<i>",
		"<sup>",
		"<sub>",
		"<kbd>",
		"<samp>",
		"<var>",
		"<code>",
		"<time>",
		"<data>",
		"<abbr>",
		"<dfn>",
		"<q>",
		"<cite>",
		"<s>",
		"<small>",
		"<strong>",
		"<em>",
		"<a>",
		"<div>",
		"<figcaption>",
		"<figure>",
		"<dd>",
		"<dt>",
		"<dl>",
		"<li>",
		"<ul>",
		"<ol>",
		"<blockquote>",
		"<pre>",
		"<hr>",
		"<p>",
		"<address>",
		"<footer>",
		"<header>",
		"<hgroup>",
		"<aside>",
		"<article>",
		"<nav>",
		"<section>" 
);
natcasesort ( $allowedTags );

$allowedTags = implode ( "", $allowedTags );
define ( "HTML5_ALLOWED_TAGS", $allowedTags );
?>