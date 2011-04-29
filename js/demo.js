//  some configuration "constants"
var DEFAULT_FLICKR_RESULTS_PER_PAGE = 24;
var DEFAULT_SEARCH_TERM = "sunset";
var DEBUG = false;

function Debug(s) {
    if (DEBUG) {
        try {
            console.log("DEBUG: " + s);
        } catch (e) {
            // console does not exist...do nothing
        }
    }
}

function encodeHTML(s) {
    return s.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;");
}

var nJSONRequests = 0;

function SendJSONRequest(url) {
    var head = document.getElementsByTagName("head").item(0);
    var script = document.createElement("script");
    script.setAttribute("type", "text/javascript");
    script.setAttribute("src", url);
    script.setAttribute("id", "dynamicScript" + ++nJSONRequests);
    head.appendChild(script);
}

var currentSearchTermEncoded = "";

//  api documented at http://www.flickr.com/services/api/flickr.photos.search.html
function ExecuteFlickrSearch(term) {
    currentSearchTermEncoded = encodeHTML(term);
    document.getElementById("gallery").innerHTML = "<p class='loading'>Searching for photos of &ldquo;" + currentSearchTermEncoded + "&rdquo;...</p>";
    document.getElementById("displayingN").innerHTML = "";

    var requestUrl = "http://api.flickr.com/services/rest/?" +
            [
                "jsoncallback=" + "HandleFlickrSearchResult",
                "method=" + "flickr.photos.search",
                "api_key=" + "afaf235416e5cb2fb406b7cae3caa43c",
                "text=" + term,
                "sort=" + "relevance", // "interestingness-desc", //
                "safe_search=" + "1",
                "content_type=" + "1",
                "license=" + "4,6,7",
                "per_page=" + DEFAULT_FLICKR_RESULTS_PER_PAGE,
                "page=" + 1,
                "extras=" + "owner_name,tags,url_sq,url_t,url_s,url_m,url_o",
                "format=" + "json"
            ].join("&");

    Debug("sending request: " + requestUrl);
    SendJSONRequest(requestUrl);
}

function formatTemplate() {
    var template =
            '<div id="pw{3}" class="photoWrapper" onmouseover="overPhoto(this,event)" onmouseout="outPhoto(this,event)" ' +
                    'onfocus="overPhoto(this,event)" onblur="outPhoto(this,event)" onclick="clickPhoto(this,event)" ' +
                    'onkeydown="keyDownPhoto(this,event)" ' +
                    'flickrPageUrl="http://www.flickr.com/photos/{5}/{3}" title="Ctrl-click to view photo on Flickr.com" tabindex="{6}" >' +
                    '<div class="innerWrapper">' +
                    '<img alt="{2}" src="{0}" onload="animateOpacity(document.getElementById(\'pw{3}\'), 1, 1000)" />' +
                    '<span class="creditWrapper">' +
                    '<span class="photoCredit">from {1}</span>' +
                    '</span>' +
                    '</div>' +
                    '<p class="photoCaption">{2}</p>' +
                    '<p class="photoTags">{4}</p>' +
                    '</div>';

    for (var i = 0; i < arguments.length; ++i) {
        template = template.replace(new RegExp('\\{' + i + '\\}', 'gm'), encodeHTML(arguments[i].toString()));
    }

    return template;
}

function HandleFlickrSearchResult(data) {
    if (data.stat != "ok") {
        var msg = "Flickr returned error " + data.code + ": " + data.message + ".";
        Debug(msg);
        document.getElementById("gallery").innerHTML = "<p class=\"xhrerror\">" + encodeHTML(msg) + "</p>";
        return;
    }

    var nPhotos = data.photos.photo.length;
    Debug("Got an \"ok\" response with " + nPhotos.toString() + " photos");

    if (nPhotos == 0) {
        document.getElementById("gallery").innerHTML = "<p class=\"nophotos\">Flickr returned no photos for &ldquo;" + currentSearchTermEncoded + "&rdquo;.</p>";
        return;
    }

    var gallery = "";
    for (var i = 0; i < nPhotos; i++) {
        var photo = data.photos.photo[i];
        gallery += formatTemplate(photo.url_m, photo.ownername, photo.title, photo.id, photo.tags, photo.owner, 20 + i);
    }

    document.getElementById("gallery").innerHTML = gallery;
    document.getElementById("displayingN").innerHTML = "Displaying " + nPhotos + " of " + data.photos.total.replace(/^(\d{1,3})(\d{3})/, "$1,$2") + " photos";

    //  restore messiness if enabled
    if (document.getElementById("cbMessyLayout").checked)
        messyLayout(true);
}

function colorGalleryBackgroundClicked() {
    if (document.getElementById("cbColorGallery").checked) {
        document.getElementById("gallery").style.backgroundImage = "";
    }
    else {
        document.getElementById("gallery").style.backgroundImage = "none";
    }
}

function rotateImagesTo(nDeg, duration) {
    if (typeof duration == 'undefined')
        duration = 100;

    var photos = document.getElementsByClassName("photoWrapper");
    for (var i = 0; i < photos.length; i++) {
        animateTransformRotate(photos[i], nDeg, duration);
    }
}

function rotateImagesBy(nDeg, duration) {
    if (typeof duration == 'undefined')
        duration = 100;

    var photos = document.getElementsByClassName("photoWrapper");
    for (var i = 0; i < photos.length; i++) {
        animateTransformRotate(photos[i], getCurrentTransformRotate(photos[i]) + nDeg, duration);
    }
}

function messyLayout(messy) {
    var photos = document.getElementsByClassName("photoWrapper");
    for (var i = 0; i < photos.length; i++) {
        if (messy) {
            photos[i].style.zIndex = 1 + Math.floor(Math.random() * photos.length);
            animateTransformRotateAndScale(photos[i], -30 + Math.floor(Math.random() * 61), 1.2, 0);
        }
        else {
            photos[i].style.zIndex = "auto";
            animateTransformRotateAndScale(photos[i], 0, 1, 0);
        }
    }
}

HTMLElement.prototype.isOrIsADescendantOf = function(ancestor) {
    for (var n = this; n != null; n = n.parentNode)
        if (n == ancestor)
            return true;

    return false;
};

function entering(eventObj, targetElement) {
    var fromElement = eventObj.relatedTarget;
    return fromElement == null || !(fromElement instanceof HTMLElement) || !fromElement.isOrIsADescendantOf(targetElement);
}

function leaving(eventObj, targetElement) {
    var toElement = eventObj.relatedTarget;
    return toElement == null || !(toElement instanceof HTMLElement) || !toElement.isOrIsADescendantOf(targetElement);
}

var zoomFactors = [ 3, 2.5, 2 ];

function overPhoto(wrapperElement, eventObj) {
    if (entering(eventObj, wrapperElement)) {
        wrapperElement.setAttribute("oldzIndex", getCurrentStringValue(wrapperElement, "zIndex"));
        wrapperElement.setAttribute("oldrotate", getCurrentTransformRotate(wrapperElement).toString());
        wrapperElement.setAttribute("oldscale", getCurrentTransformScale(wrapperElement).toString());
        wrapperElement.style.zIndex = 25;
        var zoomFactor = zoomFactors[document.getElementById("thumbnails").selectedIndex];

        animateTransformRotateAndScale(wrapperElement, -6, zoomFactor, 200);
    }
}

function clickPhoto(wrapperElement, eventObj) {
    var url = wrapperElement.getAttribute("flickrPageUrl");
    if (url && eventObj.ctrlKey) {
        location.href = url;
    }
    return false;
}

function keyDownPhoto(wrapperElement, eventObj) {
    var url = wrapperElement.getAttribute("flickrPageUrl");
    if (url && eventObj.keyCode == KEY_ENTER && eventObj.ctrlKey) {
        location.href = url;
    }
    return false;
}

function outPhoto(wrapperElement, eventObj) {
    if (leaving(eventObj, wrapperElement)) {
        if (document.getElementById("cbMessyLayout").checked) {
            wrapperElement.style.zIndex = wrapperElement.getAttribute("oldzIndex");
            animateTransformRotateAndScale(wrapperElement,
                    parseFloat(wrapperElement.getAttribute("oldrotate")),
                    parseFloat(wrapperElement.getAttribute("oldscale")), 200);
        }
        else {
            wrapperElement.style.zIndex = "auto";
            animateTransformRotateAndScale(wrapperElement, 0, 1, 200);
        }
    }
}

function doSearch() {
    var searchText = document.getElementById("searchText").value;
    if (searchText != "") {
        ExecuteFlickrSearch(searchText);
    }
}

function makeThumbnails(size) {
    var gal = document.getElementById('gallery');
    gal.className = gal.className.replace(/\b(small|medium|large)\b/, size);
}

function showTags(show) {
    var gal = document.getElementById('gallery');
    gal.className = gal.className.replace(/\s*showtags\b/, '') + ((show == "true") ? ' showtags' : '');
}

function init() {

    //  this depends on initialization that has already been done  in animation.js
    //  we just set the class on a div to the script element property name for CSS3 2D Transforms
    document.getElementById("TransformNote").className = window.transformName;

    //  kill text selection in IE
    document.getElementById("gallery").onselectstart = function() {return false;};

    //  re-init the background color or image
    colorGalleryBackgroundClicked();

    //  start with a default search (or the old one if this is a refresh)
    var searchTerm = document.getElementById("searchText").value;
    ExecuteFlickrSearch(searchTerm != "" ? searchTerm : DEFAULT_SEARCH_TERM);
}
