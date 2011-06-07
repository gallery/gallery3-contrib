/**
 * Gallery 3 Admin Redmond Theme Screen Styles
 *
 * @requires YUI reset, font, grids CSS
 *
 * Sheet organization:
 *  1)  Basic HTML elements
 *  2)  Reusable content blocks
 *  3)  Page layout containers
 *  4)  Content blocks in specific layout containers
 *  5)  States and interactions
 *  6)  Positioning and order
 *  7)  Navigation and menus
 *  8)  jQuery and jQuery UI
 *  9)  Module color overrides
 *
 * @todo Review g-buttonset-vertical
 */

/** *******************************************************************
 * 1) Basic HTML elements
 **********************************************************************/
html {
  color: #<?= $fcDefault ?>;
}

body, html {
  background-color: #<?= $bgColorDefault ?>;
  font-family: Lucida Grande, Lucida Sans, Arial, sans-serif; /* ffDefault */
  //font-size: 13px/1.231; /* fsDefault/ gallery_line_height */
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

h3 {
  font-size: 1.2em;
}

/* Links ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

a,
.g-menu a,
#g-dialog a,
.g-button,
.g-button:active {
  color: #<?= $fcDefault ?> !important;
  text-decoration: none;
  -moz-outline-style: none;
}

a:hover,
.g-button:hover,
a.ui-state-hover,
input.ui-state-hover,
button.ui-state-hover {
  color: #<?= $fcHover ?> !important;
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

/* Lists ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

ul.g-text li,
.g-text ul li {
  list-style-type: disc;
  margin-left: 1em;
}

/* Forms ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

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
  padding: 0 .2em;
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
  background-color: <?= $bgColorDefault ?>;
  color: #<?= $fcContent ?>;
  padding: .3em .6em;
  width: 100%;
}

.g-short-form .textbox.g-error {
  border: 1px solid #<?= $borderColorError ?>;
  color: #<?= $fcError ?>;
  padding-left: 24px;
}

.g-short-form .g-cancel {
  display: block;
  margin: .3em .8em;
}

#g-sidebar .g-short-form li {
  padding-left: 0;
  padding-right: 0;
}

fieldset {
  margin-bottom: 1em;
}

#g-content form ul li {
  padding: .4em 0;
}

#g-dialog form {
  width: 270px;
}

#g-dialog fieldset {
  margin-bottom: 0;
}

/* Tables ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

table {
  width: 100%;
}

#g-content table {
  margin: .6em 0 2em 0;
}

caption,
th {
  text-align: left;
}

th,
td {
  border: none;
  border-bottom: 1px solid #<?= $borderColorContent?>;
  padding: .5em;
  vertical-align: middle;
}

th {
  vertical-align: bottom;
  white-space: nowrap;
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
 * 2) Reusable content blocks
 *********************************************************************/

.g-block,
#g-content #g-admin-dashboard .g-block {
  background-color: #<?= $bgColorContent ?>;
  border: 1px solid #<?= $borderColorContent ?>;
  padding: 1em;
}

.g-block h2 {
  background-color: #<?= $fcContent ?>;
  padding: .3em .8em;
}

.g-block-content {
  margin-top: 1em;
}

#g-content .g-block {
  border: none;
  padding: 0;
}

#g-sidebar .g-block-content {
  padding: 0;
}

#g-content .g-selected,
#g-content .g-available .g-block {
  border: 1px solid #<?= $borderColorContent ?>;
  padding: .8em;
}

.g-selected img,
.g-available .g-block img {
  float: left;
  margin: 0 1em 1em 0;
}

.g-selected {
  background: #<?= $bgColorActive ?>;
}

.g-available .g-installed-toolkit:hover {
  cursor: pointer;
  background: #<?= $bgColorContent ?>;
}

.g-available .g-button {
  width: 96%;
}

.g-selected .g-button {
  display: none;
}

.g-unavailable {
  border-color: #<?= $bgColorHeader ?>;
  color: <?= $fcDefault ?>;
  opacity: 0.4;
}

.g-info td {
  background-color: transparent;
  background-image: none;
}

.g-success td {
  background-color: transparent;
  background-image: none;
}

.g-error td {
  background-color: #<?= $borderColorError ?>;
  color: #<?= $fcError ?>;
  background-image: none;
}

.g-warning td {
  background-color: #<?= $bgColorWarning ?> !important;
  background-image: none;
}

.g-module-status.g-info,
#g-log-entries .g-info,
.g-module-status.g-success,
#g-log-entries .g-success {
  background-color: #<?= $bgColorContent ?>;
}

/*** ******************************************************************
 * 3) Page layout containers
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

/* Header  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-header #g-login-menu {
  margin-top: 1em;
  float: right;
}

/* View container ~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-view {
  background-color: #<?= $bgColorContent ?>;
  border: 1px solid #<?= $borderColorContent ?>;
  border-bottom: none;
  min-width: 974px !important;
}

/* Layout containers ~~~~~~~~~~~~~~~~~~~~~ */

#g-header {
  background-color: #<?= $bgColorHeader ?>;
  border-bottom: 1px solid #<?= $borderColorHeader ?>;
  color: #<?= $fcHeader ?>;
  font-size: .8em;
  margin-bottom: 20px;
  padding: 0 20px;
  position: relative;
}

#g-content {
  font-size: 1.1em;
  padding: 0 2em;
  width: 96%;
}

#g-sidebar {
  background-color: #<?= $bgColorContent ?>;
  font-size: .9em;
  padding: 0 20px;
  width: 220px;
}

#g-footer {
  background-color: #<?= $bgColorHeader ?>;
  border-top: 1px solid #<?= $borderColorHeader ?>;
  color: #<?= $fcHeader ?>;
  font-size: .8em;
  margin-top: 20px;
  padding: 10px 20px;
}

/** *******************************************************************
 * 4) Content blocks in specific layout containers
 *********************************************************************/

/* Header  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-header #g-logo {
  background: transparent url('../../../lib/images/logo.png') no-repeat 0 .5em;
  color: #<?= $fcHeader ?> !important;
  display: block;
  height: 65px;
  padding-top: 5px;
  width: 105px;
}

#g-header #g-logo:hover {
  color: #<?= $fcHover ?> !important;
  text-decoration: none;
}

#g-login-menu li a {
  color: #<?= $fcHighlight ?> !important;
}

#g-content .g-block h2 {
  background-color: transparent;
  padding-left: 0;
}

#g-sidebar .g-block-content {
  padding-left: 1em;
}

.g-block .ui-dialog-titlebar {
  margin: -1em -1em 0;
}

#g-sidebar .g-block h2 {
  background: none;
}

/* Photo stream ~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-photo-stream {
  background-color: #<?= $bgColorDefault ?>;
}

#g-photo-stream .g-block-content ul {
  border-right: 1px solid #<?= $bgColorDefault ?>;
  height: 135px;
  overflow: auto;
  overflow: -moz-scrollbars-horizontal; /* for FF */
  overflow-x: scroll; /* scroll horizontal */
  overflow-y: hidden; /* Hide vertical*/
}

#g-content #g-photo-stream .g-item {
  background-color: #<?= $bgColorDefault ?>;
  border: 1px solid #<?= $borderColorContent ?>;
  border-right-color: #<?= $borderColorHighlight ?>;
  border-bottom-color: #<?= $borderColorHighlight ?>;
  float: left;
  height: 90px;
  overflow: hidden;
  text-align: center;
  width: 90px;
}

#g-content .g-item {
  background-color: #<?= $bgColorDefault ?>;
  border: 1px solid #<?= $borderColorContent ?>;
  border-right-color: #<?= $borderColorHighlight ?>;
  border-bottom-color: #<?= $borderColorHighlight ?>;
  height: 90px;
  padding: 14px 8px;
  text-align: center;
  width: 90px;
}

/* Graphics settings ~~~~~~~~~~~~~~~~~~~~~ */

#g-admin-graphics .g-available .g-block {
  clear: none;
  float: left;
  margin-right: 1em;
  width: 30%;
}

/* Appearance settings ~~~~~~~~~~~~~~~~~~~ */

#g-site-theme,
#g-admin-theme {
  float: left;
  width: 48%;
}

#g-site-theme {
  margin-right: 1em;
}

/* Block admin  ~~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-admin-blocks-list {
  float: left;
  margin: 0 2em 2em 0;
  width: 30%;
}

.g-admin-blocks-list div:last-child {
  border: .1em solid;
  height: 100%;
}

.g-admin-blocks-list ul {
  height: 98%;
  margin: .1em .1em;
  padding: .1em;
}

.g-admin-blocks-list ul li.g-draggable {
  background-color: #<?= $bgColorDefault ?>;
  margin: .5em;
  padding: .3em .8em;
}

/* In-line editing  ~~~~~~~~~~~~~~~~~~~~~~ */
#g-in-place-edit-message {
  background-color: #<?= $bgColorContent ?>;
}

/* Theme options  ~~~~~~~~~~~~~~~~~~~~~~~~ */
#g-theme-options-form {
  border: 1px solid #<?= $borderColorContent ?>;
}
#g-theme-options-form-tabs {
  border: none !important;
}
#g-theme-options-form fieldset {
  border: none;
}

.ui-tabs .ui-tabs-nav li a {
  padding: 0 1em;
}

.ui-tabs .ui-tabs-nav li a.g-error {
  background: none no-repeat scroll 0 0 transparent;
  color: #<?= $fcError ?> !important;
}

/* Footer content ~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-footer #g-credits li a {
  color: #<?= $fcHighlight ?> !important;
}

/* Language options  ~~~~~~~~~~~~~~~~~~~~~~~~ */
#g-share-translations-form fieldset {
  border: 0px;
  margin: 0px;
  padding: 0px;
}

#g-share-translations-form fieldset legend {
  display: none;
}

/** *******************************************************************
 *  5) States and interactions
 **********************************************************************/

.g-draggable:hover {
  border: 1px dashed #<?= $bgColorHighlight ?>;
}

.ui-sortable .g-target,
.ui-state-highlight {
  background-color: #<?= $bgColorHighlight ?>;
  border: 2px dotted #<?= $borderColorHighlight ?>;
}

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
.g-denied,
tr.g-error td.g-error,
#g-add-photos-status .g-error {
  background: #<?= $borderColorError ?> url('../images/ico-error.png') no-repeat .4em 50%;
  color: #<?= $fcError ?>;
}

.g-info {
  background: #<?= $bgColorContent ?> url('../images/ico-info.png') no-repeat .4em 50%;
}

.g-success,
.g-allowed,
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
  background: #<?= $bgColorWarning ?> url('../images/ico-warning.png') no-repeat .4em 50% !important;
  color: #<?= $fcWarning ?> !important;
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

#g-site-admin-menu {
  bottom: 0;
  font-size: 1.2em;
  left: 140px;
  position: absolute;
}

#g-site-admin-menu ul {
  margin-bottom: 0;
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

/* Superfish menu overrides ~~~~~~~~~~~~~~ */
.sf-menu ul {
  width: 12em;
}

ul.sf-menu li li:hover ul,
ul.sf-menu li li.sfHover ul {
  left:	12em;
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

#g-admin-dashboard .ui-state-highlight,
#g-sidebar .ui-state-highlight {
  height: 2em;
  margin-bottom: 1em;
}

.g-buttonset-vertical a {
  width: 8em !important;
}

#g-admin-dashboard .ui-dialog-titlebar,
#g-admin-dashboard-sidebar .ui-dialog-titlebar {
  padding: .2em .4em;
}

/** *******************************************************************
 *  9) Module color overrides
 *********************************************************************/

/* User admin form ~~~~~~~~~~~~~~~~~~~~~~~~~ */
#g-user-admin-list .g-admin {
  color: #<?= $fcDefault ?> !important;
  font-weight: bold;
}

.g-group {
  border: 1px solid #<?= $borderColorContent ?> !important;
}

.g-group h4 {
  background-color: #<?= $bgColorDefault ?> !important;
  border-bottom: 1px dashed #<?= $fcDefault ?> !important;
}

.g-default-group h4,
.g-default-group .g-user {
  color: #<?= $fcDefault ?> !important;
}
