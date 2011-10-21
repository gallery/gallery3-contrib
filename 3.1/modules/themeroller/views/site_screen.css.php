<?php defined("SYSPATH") or die("No direct script access.") ?>
/**
 * Gallery 3 <?= $display_name ?> Screen Styles
 *
 * @requires YUI reset, font, grids CSS
 *
 * Sheet organization:
 *  1)  Font sizes, base HTML elements
 *  2)  Reusable content blocks
 *  3)  Page layout containers
 *  4)  Content blocks in specific layout containers
 *  5)  Navigation and menus
 *  6)  Positioning and order
 *  7)  Navigation and menus
 *  8)  jQuery and jQuery UI
 *  9)  Organize module style
 * 10)  Tag module styles
 */

/** *******************************************************************
 *  1) Font sizes, base HTML elements
 **********************************************************************/
html {
  color: #<?= $fcDefault ?>;
}

body, html {
  background-color: #<?= $bgColorDefault ?>;
  font-family: <?= urldecode($ffDefault) ?>;
//  font-size: 13px/1.231;   /*  gallery_line_height */
}

p {
  margin-bottom: 1em;
}

em {
  font-style: oblique;
}

h1, h2, h3, h4, h5, strong, th {
  font-weight: bold;
}

h1 {
  font-size: 1.7em;
}

#g-dialog h1 {
  font-size: 1.1em;
}

h2 {
  font-size: 1.4em;
}

#g-sidebar .g-block h2 {
  font-size: 1.2em;
}

#g-sidebar .g-block li {
  margin-bottom: .6em;
}

#g-content,
#g-site-menu,
h3 {
  font-size: 1.2em;
}

#g-sidebar,
.g-breadcrumbs {
  font-size: .9em;
}

#g-banner,
#g-footer,
.g-message {
  font-size: .8em;
}

#g-album-grid .g-item,
#g-item #g-photo,
#g-item #g-movie {
  font-size: .7em;
}

/* Links ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

a,
.g-menu a,
#g-dialog a,
.g-button,
.g-button:active {
  color: #<?= $fcDefault ?> !important; /* fcDefault; */
  cursor: pointer !important;
  text-decoration: none;
  -moz-outline-style: none;
}

a:hover,
.g-button:hover,
a.ui-state-hover,
input.ui-state-hover,
button.ui-state-hover {
  color: #<?= $fcHover ?> !important; /* fcHover */
  text-decoration: none;
  -moz-outline-style: none;
}

a:hover,
#g-dialog a:hover {
  text-decoration: underline;
}

.g-menu a:hover {
  text-decoration: none;
}

#g-dialog #g-action-status li {
  width: 400px;
  white-space: normal;
  padding-left: 32px;
}

/* Forms ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
form {
  margin: 0;
}

fieldset {
  border: 1px solid #<?= $borderColorContent ?>;
  padding: 0 1em .8em 1em;
}

#g-banner fieldset,
#g-sidebar fieldset {
  border: none;
  padding: 0;
}

legend {
  font-weight: bold;
  color: #<?= $fcDefault ?>;
}

#g-banner legend,
#g-sidebar legend,
input[type="hidden"] {
  display: none;
}

input.textbox,
input[type="text"],
input[type="password"],
textarea {
  background-color: #<?= $bgColorDefault ?>;
  border: 1px solid #<?= $borderColorActive ?>;
  border-top-color: #<?= $borderColorContent ?>;
  border-left-color: #<?= $borderColorContent ?>;
  clear: both;
  color: #<?= $fcContent ?>;
  width: 50%;
}

textarea {
  height: 12em;
  width: 97%;
}

input:focus,
input.textbox:focus,
input[type=text]:focus,
textarea:focus,
option:focus {
  background-color: #<?= $bgColorActive ?>;
  color: #<?= $fcContent ?>;
}


input.checkbox,
input[type=checkbox],
input.radio,
input[type=radio] {
  float: left;
  margin-right: .4em;
}

/* Form layout ~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

form li {
  margin: 0;
  padding: 0 0 .2em 0;
}

form ul {
  margin-top: 0;
}

form ul ul {
  clear: both;
}

form ul ul li {
  float: left;
}

input,
select,
textarea {
  display: block;
  clear: both;
  padding: .2em;
}

input[type="submit"],
input[type="reset"] {
  display: inline;
  clear: none;
  float: left;
}

/* Forms in dialogs and panels ~~~~~~~~~ */

#g-dialog ul li {
  padding-bottom: .8em;
}

#g-dialog fieldset,
#g-panel fieldset {
  border: none;
  padding: 0;
}

#g-panel legend {
  display: none;
}

label,
input[readonly] {
  background-color: #<?= $bgColorContent ?>;
  color: #<?= $fcDefault ?>;
}

#g-dialog input.textbox,
#g-dialog input[type=text],
#g-dialog input[type=password],
#g-dialog textarea {
  width: 97%;
}

/* Short forms ~~~~~~~~~~~~~~~~~~~~~~~ */

.g-short-form legend,
.g-short-form label {
  display: none;
}

.g-short-form fieldset {
  border: none;
  padding: 0;
}

.g-short-form li {
  float: left;
  margin: 0 !important;
  padding: .4em 0;
}

.g-short-form .textbox,
.g-short-form input[type=text] {
  background-color: <?= $bgColorDefault ?>
  color: #<?= $fcContent ?>;
}

.g-short-form .textbox.g-error {
  border: 1px solid #<?= $borderColorError ?>;
  color: #<?= $fcError ?>;
}


.g-short-form .g-cancel {
  display: block;
  margin: .3em .8em;
}

#g-sidebar .g-short-form li {
  padding-left: 0;
  padding-right: 0;
}

/* Tables ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

table {
  width: 100%;
}

#g-content table {
  margin: 1em 0;
}

caption,
th {
  text-align: left;
}

th,
td {
  border: none;
  border-bottom: 1px solid #<?= $borderColorContent ?>;
  padding: .5em;
}

td {
  vertical-align: top;
}

/* Text ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
.g-text-small {
  font-size: .8em;
}

.g-text-big {
  font-size: 1.2em;
}

.g-text-right {
  text-align: right;
}

/** *******************************************************************
 *  2) Reusable content blocks
 *********************************************************************/

.g-block h2 {
  background-color: #<?= $bgColorDefault ?>;
  padding: .3em .8em;
}

.g-block-content {
  margin-top: 1em;
}

/** *******************************************************************
 *  3) Page layout containers
 *********************************************************************/


/* Dimension and scale ~~~~~~~~~~~~~~~~~~~ */
.g-one-quarter {
  width: 25%;
}

.g-one-third {
  width: 33%;
}

.g-one-half {
  width: 50%;
}

.g-two-thirds {
  width: 66%;
}

.g-three-quarters {
  width: 75%;
}

.g-whole {
  width: 100%;
}

/* View container ~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-view {
  background-color: #<?= $bgColorContent ?>;
  border: 1px solid #<?= $borderColorContent ?>;
  border-bottom: none;
}

/* Layout containers ~~~~~~~~~~~~~~~~~~~~~ */

#g-header {
  margin-bottom: 1em;
}

#g-banner {
  background-color: #<?= $bgColorHeader ?>;
  border-bottom: 1px solid #<?= $borderColorHeader ?>;
  color: #<?= $fcHeader?>;
  min-height: 5em;
  padding: 1em 20px;
  position: relative;
}

#g-content {
  padding-left: 20px;
  position: relative;
  width: 696px;
}

#g-sidebar {
  padding: 0 20px;
  width: 220px;
}

#g-footer {
  background-color: #<?= $bgColorHeader ?>;
  border-top: 1px solid #<?= $borderColorHeader ?>;
  margin-top: 20px;
  padding: 10px 20px;
  color: #<?= $fcHeader?>;
}

/* Status and validation messages ~~~~ */

.g-message-block {
  border: 1px solid #<?= $borderColorContent ?>;
}

#g-site-status li {
  border-bottom: 1px solid  #<?= $borderColorContent ?>;
}

/* Breadcrumbs ~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-breadcrumbs li {
  background: transparent url('../images/ico-separator.png') no-repeat scroll left center;
}

.g-breadcrumbs .g-first {
  background: none;
}

/* Pagination ~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-paginator {
}

.g-paginator li {
}

.g-paginator .g-info {
  background: none;
}

/* Dialogs and panels ~~~~~~~~~~~~~~~~~~ */

#g-dialog {
  text-align: left;
}

#g-dialog legend {
  display: none;
}

#g-dialog .g-cancel {
  margin: .4em 1em;
}

#g-panel {
  display: none;
  padding: 1em;
}

/* Inline layout  ~~~~~~~~~~ */

.g-inline li {
  float: left;
  margin-left: 1.8em;
  padding-left: 0 !important;
}

.g-inline li.g-first {
  margin-left: 0;
}

/** *******************************************************************
 *  4) Content blocks in specific layout containers
 *********************************************************************/

/* Header  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-banner #g-quick-search-form {
  clear: right;
  float: right;
  margin-top: 1em;
}

#g-banner #g-quick-search-form input[type='text'] {
  width: 17em;
}

#g-content .g-block h2 {
  background-color: transparent;
  padding-left: 0;
}

#g-login-menu li a {
  color: #<?= $fcHighlight ?> !important;
}

/* Sidebar  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-sidebar .g-block-content {
  padding-left: 1em;
}

#g-sidebar #g-image-block {
  overflow: hidden;
}

/* Album content ~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-content #g-album-grid {
  margin: 1em 0;
  position: relative;
  z-index: 1;
}

#g-content #g-album-grid .g-item {
  background-color: #<?= $bgColorContent ?>;
  border: 1px solid #<?= $bgColorContent ?>;
  float: left;
  padding: .6em 8px;
  position: relative;
  text-align: center;
  width: 213px;
  z-index: 1;
}

#g-content #g-album-grid .g-item h2 {
  margin: 5px 0;
}

#g-content .g-photo h2,
#g-content .g-item .g-metadata {
  display: none;
  margin-bottom: .6em;
}

#g-content #g-album-grid .g-album {
  background-color: #<?= $bgColorDefault ?>;
}

#g-content #g-album-grid .g-album h2 span.g-album {
  background: transparent url('../images/ico-album.png') no-repeat top left;
  display: inline-block;
  height: 16px;
  margin-right: 5px;
  width: 16px;
}

#g-content #g-album-grid .g-hover-item {
  border: 1px solid #<?= $borderColorContent ?>;
  position: absolute !important;
  z-index: 1000 !important;
}

#g-content .g-hover-item h2,
#g-content .g-hover-item .g-metadata {
  display: block;
}

#g-content #g-album-grid #g-place-holder {
  position: relative;
  visibility: hidden;
  z-index: 1;
}

/* Search results ~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-content #g-search-results {
  margin-top: 1em;
  padding-top: 1em;
}

/* Individual photo content ~~~~~~~~~~~~~~ */

#g-item {
  position: relative;
  width: 100%;
}

#g-item #g-photo,
#g-item #g-movie {
  padding: 2.2em 0;
  position: relative;
}

#g-item img.g-resize,
#g-item a.g-movie {
  display: block;
  margin: 0 auto;
}

/* Footer content ~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-footer #g-credits li {
  padding-right: 1.2em;
}

#g-footer #g-credits li a {
  color: #<?= $fcHighlight ?> !important;
}

/* In-line editing  ~~~~~~~~~~~~~~~~~~~~~~ */

#g-in-place-edit-message {
  background-color: #<?= $bgColorContent ?>;
}

/* Permissions  ~~~~~~~~~~~~~~~~~~~~~~~~~~ */
#g-edit-permissions-form td {
  background-image: none;
}

#g-edit-permissions-form fieldset {
  border: 1px solid #<?= $borderColorHighlight ?>;
}

#g-permissions .g-denied {
  background-color: transparent;
}

#g-permissions .g-allowed {
  background-color: transparent;
}

.g-allowed a {
  background-image: url("themeroller/images/ui-icons_<?= $iconColorHighlight ?>_256x240.png") !important;
  display:inline-block;
  margin: auto;
}

.g-denied a {
  background-image: url("themeroller/images/ui-icons_<?= $iconColorError ?>_256x240.png") !important;
  display:inline-block;
  margin: auto;
}

.g-denied a.g-passive,
.g-allowed a.g-passive {
  filter:Alpha(Opacity=35);
  opacity: .55;
}

#g-permissions .g-active a {
  border: 1px solid #<?= $borderColorActive ?>;
  background: #<?= $bgColorActive ?>;
}

/** *******************************************************************
 *  5) States and interactions
 **********************************************************************/

.g-active,
.g-enabled,
.g-available,
.g-selected,
.g-highlight {
  font-weight: bold;
}

.g-inactive,
.g-disabled,
.g-unavailable,
.g-uneditable,
.g-locked,
.g-deselected,
.g-understate {
  color: #<?= $borderColorContent ?>;
  font-weight: normal;
}

.g-editable:hover {
  background-color: #<?= $bgColorActive ?>;
  color: #<?= $iconColorActive ?>
}

form li.g-error,
form li.g-info,
form li.g-success,
form li.g-warning {
  background-image: none;
}

form.g-error input[type="text"],
li.g-error input[type="text"],
form.g-error input[type="password"],
li.g-error input[type="password"],
form.g-error input[type="checkbox"],
li.g-error input[type="checkbox"],
form.g-error input[type="radio"],
li.g-error input[type="radio"],
form.g-error textarea,
li.g-error textarea,
form.g-error select,
li.g-error select {
  border: 2px solid #<?= $fcError ?>;
}

.g-error,
tr.g-error td.g-error,
#g-add-photos-status .g-error {
  background: #<?= $borderColorError ?> url('../images/ico-error.png') no-repeat .4em 50%;
  color: #<?= $fcError ?>;
}

.g-info {
  background: #<?= $bgColorContent ?> url('../images/ico-info.png') no-repeat .4em 50%;
}

.g-success,
#g-add-photos-status .g-success {
  background: #<?= $bgColorContent ?> url('../images/ico-success.png') no-repeat .4em 50%;
}

tr.g-success {
  background-image: none;
}

tr.g-success td.g-success {
  background-image: url('../images/ico-success.png');
}

.g-warning,
tr.g-warning td.g-warning {
  background: #<?= $bgColorWarning ?> url('../images/ico-warning.png') no-repeat .4em 50%;
  color: #<?= $fcWarning ?>;
}

form .g-error {
  background-color: #<?= $bgColorError ?>;
}

.g-default {
  background-color: #<?= $bgColorDefault ?>;
  font-weight: bold;
}

.g-draggable:hover {
  border: 1px dashed #<?= $bgColorHighlight ?>;
}

.ui-sortable .g-target,
.ui-state-highlight {
  background-color: #<?= $bgColorHighlight ?>;
  border: 2px dotted #<?= $borderColorHighlight ?>;
}

/* Ajax loading indicator ~~~~~~~~~~~~~~~~ */

.g-loading-large,
.g-dialog-loading-large {
  background: #<?= $bgColorContent ?> url('../images/loading-large.gif') no-repeat center center !important;
}

.g-loading-small {
  background: #<?= $bgColorContent ?> url('../images/loading-small.gif') no-repeat center center !important;
}

/** *******************************************************************
 *  6) Positioning and order
 **********************************************************************/

.g-left {
  clear: none;
  float: left;
}

.g-right {
  clear: none;
  float: right;
}

.g-first {
}

.g-last {
}

.g-even {
  background-color: #<?= $bgColorContent ?>;
}

.g-odd {
  background-color: #<?= $bgColorDefault ?>;
}

/** *******************************************************************
 *  7) Navigation and menus
 *********************************************************************/

/* Login menu ~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-banner #g-login-menu {
  color: #<?= $fcHeader ?>;
  float: right;
}

#g-banner #g-login-menu li {
  padding-left: 1.2em;
}

/* Site Menu  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-site-menu {
  bottom: 0;
  left: 140px;
  position: absolute;
}

#g-site-menu ul {
  margin-bottom: 0 !important;
}

/* Context Menu  ~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-context-menu {
  background-color: #<?= $bgColorContent ?>;
  bottom: 0;
  left: 0;
  position: absolute;
}

.g-item .g-context-menu {
  display: none;
  margin-top: 2em;
  width: 100%;
}

#g-item .g-context-menu ul {
  display: none;
}

.g-context-menu li {
  border-left: none;
  border-right: none;
  border-bottom: none;
}

.g-context-menu li a {
  display: block;
  line-height: 1.6em;
}

.g-hover-item .g-context-menu {
  display: block;
}

.g-hover-item .g-context-menu li {
  text-align: left;
}

.g-hover-item .g-context-menu a:hover {
  text-decoration: none;
}

/* View Menu  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-view-menu {
  margin-bottom: 1em;
}

#g-view-menu a {
  background-repeat: no-repeat;
  background-position: 50% 50%;
  height: 28px !important;
  width: 43px !important;
}

#g-view-menu #g-slideshow-link {
  background-image: url('../images/ico-view-slideshow.png');
}

#g-view-menu .g-fullsize-link {
  background-image: url('../images/ico-view-fullsize.png');
}

#g-view-menu #g-comments-link {
  background-image: url('../images/ico-view-comments.png');
}

#g-view-menu #g-print-digibug-link {
  background-image: url('../images/ico-print.png');
}

/** *******************************************************************
 *  8) jQuery and jQuery UI
 *********************************************************************/

/* Generic block container ~~~~~~~~~~~~~~~ */

.g-block {
  clear: both;
  margin-bottom: 2.5em;
}

.g-block-content {
}

/* Superfish menu overrides ~~~~~~~~~~~~~~ */
.sf-menu ul {
        width: 12em;
}

ul.sf-menu li li:hover ul,
ul.sf-menu li li.sfHover ul {
        left:   12em;
}

ul.sf-menu li li li:hover ul,
ul.sf-menu li li li.sfHover ul {
        left: 12em;
}

.sf-menu a {
  border-left:1px solid #<?= $borderColorContent ?>;
}

.sf-menu li,
.sf-menu li li,
.sf-menu li li ul li {
  background-color: #<?= $bgColorDefault ?>;
}

.sf-menu li:hover {
  background-color: #<?= $bgColorHover ?>;
}

.sf-menu li:hover,
.sf-menu li.sfHover,
.sf-menu a:focus,
.sf-menu a:hover,
.sf-menu a:active {
  background-color: #<?= $bgColorHover ?> !important;
}

.sf-sub-indicator {
  background-image: url("themeroller/images/ui-icons_<?= $iconColorHighlight ?>_256x240.png");
  height: 16px;
  width: 16px;
}

a > .sf-sub-indicator {
  background-position: -64px -16px !important;
  top: 0.6em;
}

.sf-menu ul a > .sf-sub-indicator {
  background-position: -32px -16px !important;
}

/* jQuery UI Dialog ~~~~~~~~~~~~~~~~~~~~~~ */

.ui-widget-overlay {
  background: #<?= $bgColorOverlay ?>;
  opacity: .7;
}

/* Buttons ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-button {
  display: inline-block;
  margin: 0 4px 0 0;
  padding: .2em .4em;
}

.g-button,
.g-button:hover,
.g-button:active {
  cursor: pointer !important;
  outline: 0;
  text-decoration: none;
  -moz-outline-style: none;
}

button {
  padding: 2px 4px 2px 4px;
}

/* jQuery UI ThemeRoller buttons ~~~~~~~~~ */

.g-buttonset {
  padding-left: 1px;
}

.g-buttonset li {
  float: left;
}

.g-buttonset .g-button {
  margin: 0;
}

.ui-icon-left .ui-icon {
  float: left;
  margin-right: .2em;
}

.ui-icon-right .ui-icon {
  float: right;
  margin-left: .2em;
}

/* Rotate icon, ThemeRoller only provides one of these */

.ui-icon-rotate-ccw {
  background-position: -192px -64px;
}

.ui-icon-rotate-cw {
  background-position: -208px -64px;
}

.g-progress-bar {
  height: 1em;
  width: 100%;
  margin-top: .5em;
  display: inline-block;
}

/* Status and validation messages ~~~~ */

.g-message-block {
  background-position: .4em .3em;
  border: 1px solid #ccc;
  padding: 0;
}

#g-action-status {
  margin-bottom: 1em;
}

#g-action-status li,
p#g-action-status,
div#g-action-status {
  padding: .3em .3em .3em 30px;
}

#g-site-status li {
  border-bottom: 1px solid #ccc;
  padding: .3em .3em .3em 30px;
}

.g-module-status {
  clear: both;
  margin-bottom: 1em;
}

.g-message {
  background-position: 0 50%;
}

/* Breadcrumbs ~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-breadcrumbs {
  clear: both;
  padding: 0 20px;
}

.g-breadcrumbs li {
  background: transparent url('../images/ico-separator.png') no-repeat scroll left center;
  float: left;
  padding: 1em 8px 1em 18px;
}

.g-breadcrumbs .g-first {
  background: none;
  padding-left: 0;
}

.g-breadcrumbs li a,
.g-breadcrumbs li span {
  display: block;
}

#g-dialog ul.g-breadcrumbs {
  margin-left: 0;
  padding-left: 0;
}

/* Pagination ~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-paginator {
  padding: .2em 0;
  width: 100%;
}

.g-paginator li {
  float: left;
  width: 30%;
}

.g-paginator .g-info {
  background: none;
  padding: .2em 0;
  text-align: center;
  width: 40%;
}

/* Dialogs and panels ~~~~~~~~~~~~~~~~~~ */

#g-dialog {
  text-align: left;
}

#g-dialog legend {
  display: none;
}

#g-dialog .g-cancel {
  margin: .4em 1em;
}

#g-panel {
  display: none;
  padding: 1em;
}

/* Inline layout  ~~~~~~~~~~ */

.g-inline li {
  float: left;
  margin-left: 1.8em;
  padding-left: 0 !important;
}

.g-inline li.g-first {
  margin-left: 0;
}

/* Autocomplete  ~~~~~~~~~~ */
.ac_loading {
  background: #<?= $bgColorContent ?> url('../images/loading-small.gif') right center no-repeat !important;
}

/** *******************************************************************
 *  9) Organize module style
 *********************************************************************/
#g-organize {
  background-color: #<?= $bgColorContent ?>;
  border: 0px solid #<?= $borderColorContent ?>;
  color: #<?= $fcContent ?>;
}

#g-organize-hover {
  background-color: #<?= $bgColorHover ?>;
  display: none;
}

#g-organize-active {
  background-color: #<?= $bgColorHighlight ?>;
  display: none;
}

/** *******************************************************************
 * 10) Tag module styles
 *********************************************************************/
/* Tag cloud ~~~~~~~~~~~~~~~~~~~~~~~ */
#g-tag-cloud ul li a {
  text-decoration: none;
}

#g-tag-cloud ul li.size0 a {
  color: #<?= $fcContent ?>;
  font-size: 70%;
  font-weight: 100;
}

#g-tag-cloud ul li.size1 a {
  color: #<?= $fcContent ?>;
  font-size: 80%;
  font-weight: 100;
}

#g-tag-cloud ul li.size2 a {
  color: #<?= $fcContent ?>;
  font-size: 90%;
  font-weight: 300;
}

#g-tag-cloud ul li.size3 a {
  color: #<?= $fcContent ?>;
  font-size: 100%;
  font-weight: 500;
}

#g-tag-cloud ul li.size4 a {
  color: #<?= $fcContent ?>;
  font-size: 110%;
  font-weight: 700;
}

#g-tag-cloud ul li.size5 a {
  color: #<?= $fcContent ?>;
  font-size: 120%;
  font-weight: 900;
}

#g-tag-cloud ul li.size6 a {
  color: #<?= $fcContent ?>;
  font-size: 130%;
  font-weight: 900;
}

#g-tag-cloud ul li.size7 a {
  color: #<?= $fcContent ?>;
  font-size: 140%;
  font-weight: 900;
}

#g-tag-cloud ul li a:hover {
  color: #f30;
  text-decoration: underline;
}
