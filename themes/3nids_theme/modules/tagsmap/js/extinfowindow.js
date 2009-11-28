/*
* ExtInfoWindow Class, v1.0 
*  Copyright (c) 2007, Joe Monahan (http://www.seejoecode.com)
* 
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
* 
*       http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*
* This class lets you add an info window to the map which mimics GInfoWindow
* and allows for users to skin it via CSS.  Additionally it has options to
* pull in HTML content from an ajax request, triggered when a user clicks on
* the associated marker.
*/


/**
 * Creates a new ExtInfoWindow that will initialize by reading styles from css
 *
 * @constructor
 * @param {GMarker} marker The marker associated with the info window
 * @param {String} windowId The DOM Id we will use to reference the info window
 * @param {String} html The HTML contents
 * @param {Object} opt_opts A contianer for optional arguments:
 *    {String} ajaxUrl The Url to hit on the server to request some contents 
 *    {Number} paddingX The padding size in pixels that the info window will leave on 
 *                    the left and right sides of the map when panning is involved.
 *    {Number} paddingY The padding size in pixels that the info window will leave on 
 *                    the top and bottom sides of the map when panning is involved.
 *    {Number} beakOffset The repositioning offset for when aligning the beak element. 
 *                    This is used to make sure the beak lines up correcting if the 
 *                    info window styling containers a border.
 */
function ExtInfoWindow(marker, windowId, html, opt_opts) {
  this.html_ = html;
  this.marker_ = marker;
  this.infoWindowId_ = windowId;

  this.options_ = opt_opts == null ? {} : opt_opts;
  this.ajaxUrl_ = this.options_.ajaxUrl == null ? null : this.options_.ajaxUrl;
  this.callback_ = this.options_.ajaxCallback == null ? null : this.options_.ajaxCallback;

  this.borderSize_ = this.options_.beakOffset == null ? 0 : this.options_.beakOffset;
  this.paddingX_ = this.options_.paddingX == null ? 0 + this.borderSize_ : this.options_.paddingX + this.borderSize_;
  this.paddingY_ = this.options_.paddingY == null ? 0 + this.borderSize_ : this.options_.paddingY + this.borderSize_;

  this.map_ = null;

  this.container_ = document.createElement('div');
  this.container_.style.position = 'relative';
  this.container_.style.display = 'none';

  this.contentDiv_ = document.createElement('div');
  this.contentDiv_.id = this.infoWindowId_ + '_contents';
  this.contentDiv_.innerHTML = this.html_;
  this.contentDiv_.style.display = 'block';
  this.contentDiv_.style.visibility = 'hidden';

  this.wrapperDiv_ = document.createElement('div');
};

//use the GOverlay class
ExtInfoWindow.prototype = new GOverlay();

/**
 * Called by GMap2's addOverlay method.  Creates the wrapping div for our info window and adds
 * it to the relevant map pane.  Also binds mousedown event to a private function so that they
 * are not passed to the underlying map.  Finally, performs ajax request if set up to use ajax
 * in the constructor.
 * @param {GMap2} map The map that has had this extInfoWindow is added to it.
 */
ExtInfoWindow.prototype.initialize = function(map) {
  this.map_ = map;

  this.defaultStyles = {
    containerWidth: this.map_.getSize().width / 2,
    borderSize: 1
  };

  this.wrapperParts = {
    tl:{t:0, l:0, w:0, h:0, domElement: null},
    t:{t:0, l:0, w:0, h:0, domElement: null},
    tr:{t:0, l:0, w:0, h:0, domElement: null},
    l:{t:0, l:0, w:0, h:0, domElement: null},
    r:{t:0, l:0, w:0, h:0, domElement: null},
    bl:{t:0, l:0, w:0, h:0, domElement: null},
    b:{t:0, l:0, w:0, h:0, domElement: null},
    br:{t:0, l:0, w:0, h:0, domElement: null},
    beak:{t:0, l:0, w:0, h:0, domElement: null},
    close:{t:0, l:0, w:0, h:0, domElement: null}
  };

  for (var i in this.wrapperParts ) {
    var tempElement = document.createElement('div');
    tempElement.id = this.infoWindowId_ + '_' + i;
    tempElement.style.visibility = 'hidden';
    document.body.appendChild(tempElement);
    tempElement = document.getElementById(this.infoWindowId_ + '_' + i);
    var tempWrapperPart = eval('this.wrapperParts.' + i);    
    tempWrapperPart.w = parseInt(this.getStyle_(tempElement, 'width'));
    tempWrapperPart.h = parseInt(this.getStyle_(tempElement, 'height'));
    document.body.removeChild(tempElement);
  }
  for (var i in this.wrapperParts) {
    if (i == 'close' ) {
      //first append the content so the close button is layered above it
      this.wrapperDiv_.appendChild(this.contentDiv_);
    }
    var wrapperPartsDiv = null;
    if (this.wrapperParts[i].domElement == null) {
      wrapperPartsDiv = document.createElement('div');
      this.wrapperDiv_.appendChild(wrapperPartsDiv);
    } else {
      wrapperPartsDiv = this.wrapperParts[i].domElement;
    }
    wrapperPartsDiv.id = this.infoWindowId_ + '_' + i;
    wrapperPartsDiv.style.position = 'absolute';
    wrapperPartsDiv.style.width = this.wrapperParts[i].w + 'px';
    wrapperPartsDiv.style.height = this.wrapperParts[i].h + 'px';
    wrapperPartsDiv.style.top = this.wrapperParts[i].t + 'px';
    wrapperPartsDiv.style.left = this.wrapperParts[i].l + 'px';
    this.wrapperParts[i].domElement = wrapperPartsDiv;
  }
  
  this.map_.getPane(G_MAP_FLOAT_PANE).appendChild(this.container_);
  this.container_.id = this.infoWindowId_;
  var containerWidth  = this.getStyle_(document.getElementById(this.infoWindowId_), 'width');
  this.container_.style.width = (containerWidth == null ? this.defaultStyles.containerWidth : containerWidth);

  this.map_.getContainer().appendChild(this.contentDiv_);
  this.contentWidth = this.getDimensions_(this.container_).width;
  this.contentDiv_.style.width = this.contentWidth + 'px';
  this.contentDiv_.style.position = 'absolute';

  this.container_.appendChild(this.wrapperDiv_);

  GEvent.bindDom(this.container_, 'mousedown', this,this.onClick_);
  GEvent.bindDom(this.container_, 'dblclick', this,this.onClick_);
  GEvent.bindDom(this.container_, 'DOMMouseScroll', this, this.onClick_);
  

  GEvent.trigger(this.map_, 'extinfowindowopen');
  if (this.ajaxUrl_ != null ) {
    this.ajaxRequest_(this.ajaxUrl_);
  }
};

/**
 * Private function to steal mouse click events to prevent it from returning to the map.
 * Without this links in the ExtInfoWindow would not work, and you could click to zoom or drag 
 * the map behind it.
 * @private
 * @param {MouseEvent} e The mouse event caught by this function
 */
ExtInfoWindow.prototype.onClick_ = function(e) {
  if(navigator.userAgent.toLowerCase().indexOf('msie') != -1 && document.all) {
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  } else {
    //e.preventDefault();
    e.stopPropagation();
  }
};

/**
 * Remove the extInfoWindow container from the map pane. 
 */
ExtInfoWindow.prototype.remove = function() {
  if (this.map_.getExtInfoWindow() != null) {
    GEvent.trigger(this.map_, 'extinfowindowbeforeclose');
    
    GEvent.clearInstanceListeners(this.container_);
    if (this.container_.outerHTML) {
      this.container_.outerHTML = ''; //prevent pseudo-leak in IE
    }
    if (this.container_.parentNode) {
      this.container_.parentNode.removeChild(this.container_);
    }
    this.container_ = null;
    GEvent.trigger(this.map_, 'extinfowindowclose');
    this.map_.setExtInfoWindow_(null);
  }
};

/**
 * Return a copy of this overlay, for the parent Map to duplicate itself in full. This
 * is part of the Overlay interface and is used, for example, to copy everything in the 
 * main view into the mini-map.
 * @return {GOverlay}
 */
ExtInfoWindow.prototype.copy = function() {
  return new ExtInfoWindow(this.marker_, this.infoWindowId_, this.html_, this.options_);
};

/**
 * Draw extInfoWindow and wrapping decorators onto the map.  Resize and reposition
 * the map as necessary. 
 * @param {Boolean} force Will be true when pixel coordinates need to be recomputed.
 */
ExtInfoWindow.prototype.redraw = function(force) {
  if (!force || this.container_ == null) return;

  //set the content section's height, needed so  browser font resizing does not affect the window's dimensions
  var contentHeight = this.contentDiv_.offsetHeight;
  this.contentDiv_.style.height = contentHeight + 'px';

  //reposition contents depending on wrapper parts.
  //this is necessary for content that is pulled in via ajax
  this.contentDiv_.style.left = this.wrapperParts.l.w + 'px';
  this.contentDiv_.style.top = this.wrapperParts.tl.h + 'px';
  this.contentDiv_.style.visibility = 'visible';

  //Finish configuring wrapper parts that were not set in initialization
  this.wrapperParts.tl.t = 0;
  this.wrapperParts.tl.l = 0;
  this.wrapperParts.t.l = this.wrapperParts.tl.w;
  this.wrapperParts.t.w = (this.wrapperParts.l.w + this.contentWidth + this.wrapperParts.r.w) - this.wrapperParts.tl.w - this.wrapperParts.tr.w;
  this.wrapperParts.t.h = this.wrapperParts.tl.h;
  this.wrapperParts.tr.l = this.wrapperParts.t.w + this.wrapperParts.tl.w;
  this.wrapperParts.l.t = this.wrapperParts.tl.h;
  this.wrapperParts.l.h = contentHeight;
  this.wrapperParts.r.l = this.contentWidth + this.wrapperParts.l.w;
  this.wrapperParts.r.t = this.wrapperParts.tr.h;
  this.wrapperParts.r.h = contentHeight;
  this.wrapperParts.bl.t = contentHeight + this.wrapperParts.tl.h;
  this.wrapperParts.b.l = this.wrapperParts.bl.w;
  this.wrapperParts.b.t = contentHeight + this.wrapperParts.tl.h;
  this.wrapperParts.b.w = (this.wrapperParts.l.w + this.contentWidth + this.wrapperParts.r.w) - this.wrapperParts.bl.w - this.wrapperParts.br.w;
  this.wrapperParts.b.h = this.wrapperParts.bl.h;
  this.wrapperParts.br.l = this.wrapperParts.b.w + this.wrapperParts.bl.w;
  this.wrapperParts.br.t = contentHeight + this.wrapperParts.tr.h;
  this.wrapperParts.close.l = this.wrapperParts.tr.l +this.wrapperParts.tr.w - this.wrapperParts.close.w - this.borderSize_;
  this.wrapperParts.close.t = this.borderSize_;
  this.wrapperParts.beak.l = this.borderSize_ + (this.contentWidth / 2) - (this.wrapperParts.beak.w / 2);
  this.wrapperParts.beak.t = this.wrapperParts.bl.t + this.wrapperParts.bl.h - this.borderSize_;

  //create the decoration wrapper DOM objects
  //append the styled info window to the container
  for (var i in this.wrapperParts) {
    if (i == 'close' ) {
      //first append the content so the close button is layered above it
      this.wrapperDiv_.insertBefore(this.contentDiv_, this.wrapperParts[i].domElement);
    }
    var wrapperPartsDiv = null;
    if (this.wrapperParts[i].domElement == null) {
      wrapperPartsDiv = document.createElement('div');
      this.wrapperDiv_.appendChild(wrapperPartsDiv);
    } else {
      wrapperPartsDiv = this.wrapperParts[i].domElement;
    }
    wrapperPartsDiv.id = this.infoWindowId_ + '_' + i;
    wrapperPartsDiv.style.position='absolute';
    wrapperPartsDiv.style.width = this.wrapperParts[i].w + 'px';
    wrapperPartsDiv.style.height = this.wrapperParts[i].h + 'px';
    wrapperPartsDiv.style.top = this.wrapperParts[i].t + 'px';
    wrapperPartsDiv.style.left = this.wrapperParts[i].l + 'px';
    this.wrapperParts[i].domElement = wrapperPartsDiv;
  }

  //add event handler for the close box
  var currentMarker = this.marker_;
  var thisMap = this.map_;
  GEvent.addDomListener(this.wrapperParts.close.domElement, 'click', 
    function() {
      thisMap.closeExtInfoWindow();
    }
  );

  //position the container on the map, over the marker
  var pixelLocation = this.map_.fromLatLngToDivPixel(this.marker_.getPoint());
  this.container_.style.position = 'absolute';
  var markerIcon = this.marker_.getIcon();
  this.container_.style.left = (pixelLocation.x 
    - (this.contentWidth / 2) 
    - markerIcon.iconAnchor.x 
    + markerIcon.infoWindowAnchor.x
  ) + 'px';

  this.container_.style.top = (pixelLocation.y
    - this.wrapperParts.bl.h
    - contentHeight
    - this.wrapperParts.tl.h
    - this.wrapperParts.beak.h
    - markerIcon.iconAnchor.y
    + markerIcon.infoWindowAnchor.y
    + this.borderSize_
  ) + 'px';

  this.container_.style.display = 'block';

  if(this.map_.getExtInfoWindow() != null) {
    this.repositionMap_();
  }
};

/**
 * Determine the dimensions of the contents to recalculate and reposition the 
 * wrapping decorator elements accordingly.
 */
ExtInfoWindow.prototype.resize = function(){
  
  //Create temporary DOM node for new contents to get new height
  //This is done because if you manipulate this.contentDiv_ directly it causes visual errors in IE6
  var tempElement = this.contentDiv_.cloneNode(true);
  tempElement.id = this.infoWindowId_ + '_tempContents';
  tempElement.style.visibility = 'hidden';	
  tempElement.style.height = 'auto';
  document.body.appendChild(tempElement);
  tempElement = document.getElementById(this.infoWindowId_ + '_tempContents');
  var contentHeight = tempElement.offsetHeight;
  document.body.removeChild(tempElement);

  //Set the new height to eliminate visual defects that can be caused by font resizing in browser
  this.contentDiv_.style.height = contentHeight + 'px';

  var contentWidth = this.contentDiv_.offsetWidth;
  var pixelLocation = this.map_.fromLatLngToDivPixel(this.marker_.getPoint());

  var oldWindowHeight = this.wrapperParts.t.domElement.offsetHeight + this.wrapperParts.l.domElement.offsetHeight + this.wrapperParts.b.domElement.offsetHeight;	
  var oldWindowPosTop = this.wrapperParts.t.domElement.offsetTop;

  //resize info window to look correct for new height
  this.wrapperParts.l.domElement.style.height = contentHeight + 'px';
  this.wrapperParts.r.domElement.style.height = contentHeight + 'px';
  var newPosTop = this.wrapperParts.b.domElement.offsetTop - contentHeight;
  this.wrapperParts.l.domElement.style.top = newPosTop + 'px';
  this.wrapperParts.r.domElement.style.top = newPosTop + 'px';
  this.contentDiv_.style.top = newPosTop + 'px';
  windowTHeight = parseInt(this.wrapperParts.t.domElement.style.height);
  newPosTop -= windowTHeight;
  this.wrapperParts.close.domElement.style.top = newPosTop + this.borderSize_ + 'px';
  this.wrapperParts.tl.domElement.style.top = newPosTop + 'px';
  this.wrapperParts.t.domElement.style.top = newPosTop + 'px';
  this.wrapperParts.tr.domElement.style.top = newPosTop + 'px';

  this.repositionMap_();
};

/**
 * Check to see if the displayed extInfoWindow is positioned off the viewable 
 * map region and by how much.  Use that information to pan the map so that 
 * the extInfoWindow is completely displayed.
 * @private
 */
ExtInfoWindow.prototype.repositionMap_ = function(){
  //pan if necessary so it shows on the screen
  var mapNE = this.map_.fromLatLngToDivPixel(
    this.map_.getBounds().getNorthEast()
  );
  var mapSW = this.map_.fromLatLngToDivPixel(
    this.map_.getBounds().getSouthWest()
  );
  var markerPosition = this.map_.fromLatLngToDivPixel(
    this.marker_.getPoint()
  );

  var panX = 0;
  var panY = 0;
  var paddingX = this.paddingX_;
  var paddingY = this.paddingY_;
  var infoWindowAnchor = this.marker_.getIcon().infoWindowAnchor;
  var iconAnchor = this.marker_.getIcon().iconAnchor;

  //test top of screen	
  var windowT = this.wrapperParts.t.domElement;
  var windowL = this.wrapperParts.l.domElement;
  var windowB = this.wrapperParts.b.domElement;
  var windowR = this.wrapperParts.r.domElement;
  var windowBeak = this.wrapperParts.beak.domElement;

  var offsetTop = markerPosition.y - ( -infoWindowAnchor.y + iconAnchor.y +  this.getDimensions_(windowBeak).height + this.getDimensions_(windowB).height + this.getDimensions_(windowL).height + this.getDimensions_(windowT).height + this.paddingY_);
  if (offsetTop < mapNE.y) {
    panY = mapNE.y - offsetTop;
  } else {
    //test bottom of screen
    var offsetBottom = markerPosition.y + this.paddingY_;
    if (offsetBottom >= mapSW.y) {
      panY = -(offsetBottom - mapSW.y);
    }
  }

  //test right of screen
  var offsetRight = Math.round(markerPosition.x + this.getDimensions_(this.container_).width/2 + this.getDimensions_(windowR).width + this.paddingX_ + infoWindowAnchor.x - iconAnchor.x);
  if (offsetRight > mapNE.x) {
    panX = -( offsetRight - mapNE.x);
  } else {
    //test left of screen
    var offsetLeft = - (Math.round( (this.getDimensions_(this.container_).width/2 - this.marker_.getIcon().iconSize.width/2) + this.getDimensions_(windowL).width + this.borderSize_ + this.paddingX_) - markerPosition.x - infoWindowAnchor.x + iconAnchor.x);
    if( offsetLeft < mapSW.x) {
      panX = mapSW.x - offsetLeft;
    }
  }

  if (panX != 0 || panY != 0 && this.map_.getExtInfoWindow() != null ) {
    this.map_.panBy(new GSize(panX,panY));
  }
};

/**
 * Private function that handles performing an ajax request to the server.  The response
 * information is assumed to be HTML and is placed inside this extInfoWindow's contents region.
 * Last, check to see if the height has changed, and resize the extInfoWindow accordingly.
 * @private
 * @param {String} url The Url of where to make the ajax request on the server
 */
ExtInfoWindow.prototype.ajaxRequest_ = function(url){
  var thisMap = this.map_;
  var thisCallback = this.callback_;
  GDownloadUrl(url, function(response, status){
    var infoWindow = document.getElementById(thisMap.getExtInfoWindow().infoWindowId_ + '_contents');
    if (response == null || status == -1 ) {
      infoWindow.innerHTML = '<span class="error">ERROR: The Ajax request failed to get HTML content from "' + url + '"</span>';
    } else {
      infoWindow.innerHTML = response;
    }
    if (thisCallback != null ) {
      thisCallback();
    }
    thisMap.getExtInfoWindow().resize();
    GEvent.trigger(thisMap, 'extinfowindowupdate');
  });
};

/**
 * Private function derived from Prototype.js to get a given element's
 * height and width
 * @private
 * @param {Object} element The DOM element that will have height and 
 *                    width will be calculated for it.
 * @return {Object} Object with keys: width, height
 */
ExtInfoWindow.prototype.getDimensions_ = function(element) {
  var display = this.getStyle_(element, 'display');
  if (display != 'none' && display != null) { // Safari bug
    return {width: element.offsetWidth, height: element.offsetHeight};
  }

  // All *Width and *Height properties give 0 on elements with display none,
  // so enable the element temporarily
  var els = element.style;
  var originalVisibility = els.visibility;
  var originalPosition = els.position;
  var originalDisplay = els.display;
  els.visibility = 'hidden';
  els.position = 'absolute';
  els.display = 'block';
  var originalWidth = element.clientWidth;
  var originalHeight = element.clientHeight;
  els.display = originalDisplay;
  els.position = originalPosition;
  els.visibility = originalVisibility;
  return {width: originalWidth, height: originalHeight};
};

/**
 * Private function derived from Prototype.js to get a given element's
 * value that is associated with the passed style
 * @private
 * @param {Object} element The DOM element that will be checked.
 * @param {String} style The style name that will be have it's value returned.
 * @return {Object}
 */
ExtInfoWindow.prototype.getStyle_ = function(element, style) {
  var found = false;
  style = this.camelize_(style);
  var value = element.style[style];
  if (!value) {
    if (document.defaultView && document.defaultView.getComputedStyle) {
      var css = document.defaultView.getComputedStyle(element, null);
      value = css ? css[style] : null;
    } else if (element.currentStyle) {
      value = element.currentStyle[style];
    }
  }
  if((value == 'auto') && (style == 'width' || style == 'height') && (this.getStyle_(element, 'display') != 'none')) {
    if( style == 'width' ) {
      value = element.offsetWidth;
    }else {
      value = element.offsetHeight;
    }
  }
  return (value == 'auto') ? null : value;
};

/**
 * Private function pulled from Prototype.js that will change a hyphened
 * style name into camel case.
 * @private
 * @param {String} element The string that will be parsed and made into camel case
 * @return {String}
 */
ExtInfoWindow.prototype.camelize_ = function(element) {
  var parts = element.split('-'), len = parts.length;
  if (len == 1) return parts[0];
  var camelized = element.charAt(0) == '-'
    ? parts[0].charAt(0).toUpperCase() + parts[0].substring(1)
    : parts[0];

  for (var i = 1; i < len; i++) {
    camelized += parts[i].charAt(0).toUpperCase() + parts[i].substring(1);
  }
  return camelized;
};

GMap.prototype.ExtInfoWindowInstance_ = null;
GMap.prototype.ClickListener_ = null;
GMap.prototype.InfoWindowListener_ = null;

/**
 * Creates a new instance of ExtInfoWindow for the GMarker.  Register the newly created 
 * instance with the map, ensuring only one window is open at a time. If this is the first
 * ExtInfoWindow ever opened, add event listeners to the map to close the ExtInfoWindow on 
 * zoom and click, to mimic the default GInfoWindow behavior.
 *
 * @param {GMap} map The GMap2 object where the ExtInfoWindow will open
 * @param {String} cssId The id we will use to reference the info window
 * @param {String} html The HTML contents
 * @param {Object} opt_opts A contianer for optional arguments:
 *    {String} ajaxUrl The Url to hit on the server to request some contents 
 *    {Number} paddingX The padding size in pixels that the info window will leave on 
 *                    the left and right sides of the map when panning is involved.
 *    {Number} paddingX The padding size in pixels that the info window will leave on 
 *                    the top and bottom sides of the map when panning is involved.
 *    {Number} beakOffset The repositioning offset for when aligning the beak element. 
 *                    This is used to make sure the beak lines up correcting if the 
 *                    info window styling containers a border.
 */
GMarker.prototype.openExtInfoWindow = function(map, cssId, html, opt_opts) {
  if (map == null) {
    throw 'Error in GMarker.openExtInfoWindow: map cannot be null';
    return false;
  }
  if (cssId == null || cssId == '') {
    throw 'Error in GMarker.openExtInfoWindow: must specify a cssId';
    return false;
  }
  
  map.closeInfoWindow();
  if (map.getExtInfoWindow() != null) {
    map.closeExtInfoWindow();
  }
  if (map.getExtInfoWindow() == null) {
    map.setExtInfoWindow_( new ExtInfoWindow(
      this,
      cssId,
      html,
      opt_opts
    ) );
    if (map.ClickListener_ == null) {
      //listen for map click, close ExtInfoWindow if open
      map.ClickListener_ = GEvent.addListener(map, 'click',
      function(event) {
          if( !event && map.getExtInfoWindow() != null ){
            map.closeExtInfoWindow();
          }
        }
      );
    }
    if (map.InfoWindowListener_ == null) {
      //listen for default info window open, close ExtInfoWindow if open
      map.InfoWindowListener_ = GEvent.addListener(map, 'infowindowopen', 
      function(event) {
          if (map.getExtInfoWindow() != null) {
            map.closeExtInfoWindow();
          }
        }
      );
    }
    map.addOverlay(map.getExtInfoWindow());
  }
};

/**
 * Remove the ExtInfoWindow instance
 * @param {GMap2} map The map where the GMarker and ExtInfoWindow exist
 */
GMarker.prototype.closeExtInfoWindow = function(map) {
  if( map.getExtInfWindow() != null ){
    map.closeExtInfoWindow();
  }
};

/**
 * Get the ExtInfoWindow instance from the map
 */
GMap2.prototype.getExtInfoWindow = function(){
  return this.ExtInfoWindowInstance_;
};
/**
 * Set the ExtInfoWindow instance for the map
 * @private
 */
GMap2.prototype.setExtInfoWindow_ = function( extInfoWindow ){
  this.ExtInfoWindowInstance_ = extInfoWindow;
}
/**
 * Remove the ExtInfoWindow from the map
 */
GMap2.prototype.closeExtInfoWindow = function(){
  if( this.getExtInfoWindow() != null ){
    this.ExtInfoWindowInstance_.remove();
  }
};
