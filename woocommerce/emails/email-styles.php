<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-styles.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
?>

body{
	overflow: visible !important;
}


#wrapper {
    background-color: #fff;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    color: #000;
    margin: 0;
    width: 100%;
}

#template_container {
	padding: 0 50px;
}

#template_header {
    border-bottom: 0;
    line-height: 100%;
    vertical-align: middle;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
}

#template_footer td {
    padding: 0;
}

#template_footer #credit {
    border:0;
    font-family: Arial;
    font-size:10px;
    line-height:125%;
    text-align:center;
    padding: 0;
}

#body_content table td {
    padding: 10px 0;
}

#body_content table td td {
    padding: 12px;
}

#body_content table td th {
    padding: 12px;
}

#body_content p {
    margin: 0 0 16px;
}

#body_content_inner {
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 12px;
    line-height: 150%;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

.td {
    border-right: 1px solid #000;
    border-top: 1px solid #000;
}

.td.first-child{
	border-left: 1px solid #000;
}

.td.last-line{
	border-bottom: 1px solid #000;
}

.text {
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
}

.link {
	
}

#header_wrapper {
    padding: 10px 0;
    display: block;
}

#template_header_image img{
	padding: 0 50px;
	width: 600px;
	height: auto;
}

table { page-break-inside:auto }
tr    { page-break-inside:avoid; page-break-after:auto }
thead { display:table-header-group }
tfoot { display:table-row-group }

h1 {
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 18px;
    font-weight: normal;
    line-height: 150%;
    margin: 0;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
    -webkit-font-smoothing: antialiased;
}

h2 {
    display: block;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 16px;
    font-weight: normal;
    line-height: 130%;
    margin: 16px 0 8px;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

h3 {
    display: block;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 14px;
    font-weight: normal;
    line-height: 130%;
    margin: 16px 0 8px;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

a {
	color: #000;
    font-weight: normal;
    text-decoration: underline;
}

ul{
	list-style: none;
	padding: 0;
}

img {
    border: none;
    display: inline;
    font-size: 14px;
    font-weight: bold;
    height: auto;
    line-height: 100%;
    outline: none;
    text-decoration: none;
    text-transform: capitalize;
}

table#addresses tr td{
	padding: 0px;
	border-top: none;
	border-right: none;
}

#addresses h3{
	font-size: 12px;
}

#addresses p.text{
	font-size: 10px;
}

<?php
