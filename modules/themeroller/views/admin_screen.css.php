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
 *  5)  Navigation and menus
 *  6)  jQuery and jQuery UI
 *  7)  Module color overrides
 *  8)  States and interactions
 *  9)  Right-to-left language styles
 *
 * @todo Review g-buttonset-vertical
 */

/** *******************************************************************
 * 1) Basic HTML elements
 **********************************************************************/
html {
  color: #2e6e9e; /* fcDefault; */
}

body, html {
  background-color: #dfeffc; /* bgColorDefault */
  font-family: Lucida Grande, Lucida Sans, Arial, sans-serif; /* ffDefault */
  font-size: 13px/1.231; /* fsDefault/ gallery_line_height */
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
  color: #2e6e9e !important; /* fcDefault; */
  text-decoration: none;
  -moz-outline-style: none;
}

a:hover,
.g-button:hover,
a.ui-state-hover,
input.ui-state-hover,
button.ui-state-hover {
  color: #1d5987 !important; /* fcHover */
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

/* Forms ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

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
  border-bottom: 1px solid #aaaaaa; /* borderColorContent */
  padding: .5em;
  vertical-align: middle;
}

th {
  vertical-align: bottom;
  white-space: nowrap;
}

.g-even {
  background-color: #fcfdfd; /* bgColorContent */
}

.g-odd {
  background-color: #dfeffc;  /* bgColorDefault */
}

/** *******************************************************************
 * 2) Reusable content blocks
 *********************************************************************/

.g-block,
#g-content #g-admin-dashboard .g-block {
  border: 1px solid #aaaaaa; /* borderColorContent */
  padding: 1em;
}

.g-block h2 {
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
  border: 1px solid #aaaaaa; /* borderColorContent */
  padding: .8em;
}

.g-selected img,
.g-available .g-block img {
  float: left;
  margin: 0 1em 1em 0;
}

.g-selected {
  background: #f5f8f9 ; /* bgColorActive */
}

.g-available .g-installed-toolkit:hover {
  cursor: pointer;
  background: #fcfdfd; /* bgColorContent */
}

.g-available .g-button {
  width: 96%;
}

.g-selected .g-button {
  display: none;
}

.g-unavailable {
  border-color: #ffffff; /* fcHeader; */;
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
  background-color: #cd0a0a /* borderColorError */;
  color: #cd0a0a /* fcError */;
  background-image: none;
}

.g-warning td {
  background-color: #fcf9ce;
  background-image: none;
}

.g-module-status.g-info,
#g-log-entries .g-info,
.g-module-status.g-success,
#g-log-entries .g-success {
  background-color: #fcfdfd /* bgColorContent */;
}

/*** ******************************************************************
 * 3) Page layout containers
 *********************************************************************/

/* Header  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

#g-header #g-login-menu {
  margin-top: 1em;
  float: right;
}

/* View container ~~~~~~~~~~~~~~~~~~~~~~~~ */

.g-view {
  background-color: #fcfdfd; /* bgColorContent */
  border: 1px solid #a6c9e2; /* borderColorContent */
  border-bottom: none;
  min-width: 974px !important;
}

/* Layout containers ~~~~~~~~~~~~~~~~~~~~~ */

#g-header {
  background-color: #5c9ccc; /* bgColorHeader */
  border-bottom: 1px solid #4297d7; /* borderColorHeader */
  color: #ffffff; /* fcHeader */
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
  background-color: #fff;
  font-size: .9em;
  padding: 0 20px;
  width: 220px;
}

#g-footer {
  background-color: #5c9ccc; /* bgColorHeader */
  border-top: 1px solid #4297d7; /* borderColorHeader */
  color: #ffffff; /* fcHeader */
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
  color: #ffffff /* fcHeader */ !important;
  display: block;
  height: 65px;
  padding-top: 5px;
  width: 105px;
}

#g-header #g-logo:hover {
  color: #1d5987 !important; /* fcHover */
  text-decoration: none;
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
}

#g-photo-stream .g-block-content ul {
  border-right: 1px solid #e8e8e8;
  height: 135px;
  overflow: auto;
  overflow: -moz-scrollbars-horizontal; /* for FF */
  overflow-x: scroll; /* scroll horizontal */
  overflow-y: hidden; /* Hide vertical*/
}

#g-content #g-photo-stream .g-item {
  background-color: #dfeffc;  /* bgColorDefault */
  border: 1px solid #e8e8e8;
  border-right-color: #ccc;
  border-bottom-color: #ccc;
  float: left;
  height: 90px;
  overflow: hidden;
  text-align: center;
  width: 90px;
}

#g-content .g-item {
  background-color: #dfeffc;  /* bgColorDefault */
  border: 1px solid #e8e8e8;
  border-right-color: #ccc;
  border-bottom-color: #ccc;
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
  background-color: #dfeffc;  /* bgColorDefault */
  margin: .5em;
  padding: .3em .8em;
}

/* In-line editing  ~~~~~~~~~~~~~~~~~~~~~~ */
#g-in-place-edit-message {
  background-color: #fcfdfd; /* bgColorContent */
}

/* Theme options  ~~~~~~~~~~~~~~~~~~~~~~~~ */
#g-theme-options-form {
  border: 1px solid #aaaaaa; /* borderColorContent */
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
  color: #cd0a0a !important; /* fcError */
}

/** *******************************************************************
 * 5) Navigation and menus
 *********************************************************************/

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
 * 6) jQuery and jQuery UI
 *********************************************************************/

/* Superfish menu overrides ~~~~~~~~~~~~~~ */
.sf-menu a {
  border-left:1px solid #a6c9e2; /* borderColorContent */
}

.sf-menu li,
.sf-menu li li,
.sf-menu li li ul li {
  background-color: #dfeffc;  /* bgColorDefault */
}

.sf-menu li:hover {
  background-color: #d0e5f5; /* bgColorHover */
}

.sf-menu li:hover,
.sf-menu li.sfHover,
.sf-menu a:focus,
.sf-menu a:hover,
.sf-menu a:active {
  background-color: #d0e5f5 !important; /* bgColorHover */
}

.sf-sub-indicator {
  background-image: url("themeroller/images/ui-icons_2e83ff_256x240.png");
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
 * 7) Module color overrides
 *********************************************************************/

/* User admin form ~~~~~~~~~~~~~~~~~~~~~~~~~ */
#g-user-admin-list .g-admin {
  color: #2e6e9e !important; /* fcDefault; */
  font-weight: bold;
}

.g-group {
  border: 1px solid #aaaaaa !important; /* borderColorContent */
}

.g-group h4 {
  background-color: #dfeffc !important;  /* bgColorDefault */
  border-bottom: 1px dashed #2e6e9e !important; /* fcDefault; */
}

.g-default-group h4,
.g-default-group .g-user {
  color: #2e6e9e !important; /* fcDefault; */
}

/** *******************************************************************
 * 8) States and interactions
 *********************************************************************/

.g-draggable:hover {
  border: 1px dashed #fbec88; /* bgColorHighlight */
}

.ui-sortable .g-target,
.ui-state-highlight {
  background-color: #fbec88; /* bgColorHighlight */
  border: 2px dotted #fad42e; /* borderColorHighlight */
}

/** *******************************************************************
 * 9) Right to left styles
 *********************************************************************/

.rtl #g-content #g-album-grid .g-item,
.rtl #g-site-theme,
.rtl #g-admin-theme,
.rtl .g-selected img,
.rtl .g-available .g-block img,
.rtl #g-content #g-photo-stream .g-item,
.rtl li.g-group,
.rtl #g-server-add-admin {
  float: right;
}

.rtl #g-admin-graphics .g-available .g-block {
  float: right;
  margin-left: 1em;
  margin-right: 0em;
}

.rtl #g-site-admin-menu {
  left: auto;
  right: 150px;
}

.rtl #g-header #g-login-menu {
  float: left;
}

.rtl #g-header #g-login-menu li {
  margin-left: 0;
  padding-left: 0;
  padding-right: 1.2em;
}

.rtl .g-selected img,
.rtl .g-available .g-block img {
  margin: 0 0 1em 1em;
}

/* RTL Superfish ~~~~~~~~~~~~~~~~~~~~~~~~~ */

.rtl .sf-menu a {
  border-right:1px solid #fff;
}

.rtl .sf-sub-indicator {
  background: url("themeroller/images/ui-icons_2e83ff_256x240.png") no-repeat -96px -16px; /* 8-bit indexed alpha png. IE6 gets solid image only */
}

/*** shadows for all but IE6 ***/
.rtl .sf-shadow ul {
  background: url('../images/superfish-shadow.png') no-repeat bottom left;
  border-top-right-radius: 0;
  border-bottom-left-radius: 0;
  -moz-border-radius-topright: 0;
  -moz-border-radius-bottomleft: 0;
  -webkit-border-top-right-radius: 0;
  -webkit-border-bottom-left-radius: 0;
  -moz-border-radius-topleft: 17px;
  -moz-border-radius-bottomright: 17px;
  -webkit-border-top-left-radius: 17px;
  -webkit-border-bottom-right-radius: 17px;
  border-top-left-radius: 17px;
  border-bottom-right-radius: 17px;
}
