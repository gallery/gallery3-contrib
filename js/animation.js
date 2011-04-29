
//  we need the browser-specific name of the CSS 2D Transform property
window.transformName = (function() {
    var prefixedTransformNames = ["transform", "msTransform", "MozTransform", "WebkitTransform", "OTransform"];

    var tempDiv = document.createElement("div");
    for (var i = 0; i < prefixedTransformNames.length; ++i) {
        if (typeof tempDiv.style[prefixedTransformNames[i]] != 'undefined')
            return prefixedTransformNames[i];
    }

    return "noTransform";
})();

var aniList = new Object();

var transformRegExs = {
    "scale" : /scale\(\s*([\d\.\-]+)\s*\)/,
    "rotate" : /rotate\(\s*([\d\.\-]+)\s*(deg|rad)\s*\)/
};

function AniItem(htmlElement, styleProperty, valueType, startValues, endValues, duration) {

    function DoOneUpdate(now) {
        var elapsed = now - this.startTime;

        //  we're done when we're at the end
        this.done = !(elapsed < duration);

        function linearInterpolate(start, end) {
            if (elapsed < duration) {
                return start  + elapsed * (end - start) / duration;
            } else {
                return end;
            }
        }

        function formatTransform(strValueNow, transformFunction, currentTransform)
        {
            var strValue = transformFunction + "(" + strValueNow + ")";
            if (currentTransform)
            {
                var rxFunction = transformRegExs[transformFunction];
                if (currentTransform.match(rxFunction))
                    strValue = currentTransform.replace(rxFunction, strValue);
                else
                    strValue = currentTransform + " " + strValue;
            }
            return strValue;
        }

        function formatValue(valueNow, valueType) {
            var strValue;
            switch (valueType) {
                case 'pixels':
                    strValue = valueNow.toString() + "px";
                    break;
                case 'scaleTransform':
                    strValue = formatTransform(valueNow.toString(), "scale", htmlElement.style[styleProperty]);
                    break;
                case 'rotateTransform':
                    strValue = formatTransform(valueNow.toString() + "deg", "rotate", htmlElement.style[styleProperty]);
                    break;
                case 'rotateAndScaleTransform':
                    strValue = formatTransform(valueNow[0].toString() + "deg", "rotate", htmlElement.style[styleProperty]);
                    strValue = formatTransform(valueNow[1].toString(), "scale", strValue);
                    break;
                default:
                    strValue = valueNow.toString();
                    break;
            }
            return strValue;
        }

        var valuesNow;
        if (typeof startValues.length != 'undefined') {
            valuesNow = new Array();
            for (var i = 0; i < startValues.length; ++i)
                valuesNow.push(linearInterpolate(startValues[i], endValues[i]));
        }
        else {
            valuesNow = linearInterpolate(startValues, endValues);
        }

        htmlElement.style[styleProperty] = formatValue(valuesNow, valueType);

    }

    //  the object properties
    this.AnimateFrame = DoOneUpdate;
    this.startTime = new Date().getTime();
    this.done = false;
}

function getCurrentTransformFunctionValue(htmlElement, transformFunction, defaultValue)
{
    var currentValue = defaultValue;
    var currentTransform = htmlElement.style[window.transformName];
    if (currentTransform) {
        var m = currentTransform.match(transformRegExs[transformFunction]);
        if (m)
        {
            currentValue = parseFloat(m[1]);

            if (transformFunction == "rotate" && m[2] == "rad")
                currentValue = currentValue * 180 / Math.PI;
        }
    }
    return currentValue;
}

function getCurrentStringValue(htmlElement, styleProperty)
{
    var currentStyleProp = htmlElement.style[styleProperty];
    if (currentStyleProp == null || currentStyleProp == '')
        currentStyleProp = htmlElement.currentStyle ? htmlElement.currentStyle[styleProperty] :
            window.getComputedStyle(htmlElement, null).getPropertyValue(styleProperty);
    return currentStyleProp;
}

function getCurrentNumberValue(htmlElement, styleProperty)
{
    return parseFloat(getCurrentStringValue(htmlElement, styleProperty));
}

function getCurrentTransformScale(htmlElement) {
    return getCurrentTransformFunctionValue(htmlElement, "scale", 1);
}

function getCurrentTransformRotate(htmlElement) {
    return getCurrentTransformFunctionValue(htmlElement, "rotate", 0);
}

function animateTransformScale(htmlElement, endScale, duration) {
    var currentScale = getCurrentTransformScale(htmlElement);
    aniList[htmlElement.id + "st"] = new AniItem(htmlElement, window.transformName, "scaleTransform", currentScale, endScale, duration);
}

function animateTransformRotate(htmlElement, endRotate, duration) {
    var currentRotate = getCurrentTransformRotate(htmlElement);
    aniList[htmlElement.id + "rt"] = new AniItem(htmlElement, window.transformName, "rotateTransform", currentRotate, endRotate, duration);
}

function animateTransformRotateAndScale(htmlElement, endRotate, endScale, duration) {
    animateTransformRotate(htmlElement, endRotate, duration);
    animateTransformScale(htmlElement, endScale, duration);
}

function animateOpacity(htmlElement, endOpacity, duration)
{
    var currentOpacity = getCurrentNumberValue(htmlElement, "opacity");
    aniList[htmlElement.id + "op"] = new AniItem(htmlElement, "opacity", "number", currentOpacity, endOpacity, duration);
}

//var lastAnimate = new Date().getTime();

function _intervalAnimator() {
    var now = new Date().getTime() + 18;    // look ahead to the next 18ms beat;

//    var logged = false;

    for (var name in aniList) {
        if (aniList[name] instanceof AniItem)
        {
            if (!aniList[name].done)
            {
//                if (!logged)
//                {
//                    Debug("Animating at " + (now - lastAnimate) + 'ms');
//                    lastAnimate = now;
//                    logged = true;
//                }
                
                aniList[name].AnimateFrame(now);
            }
        }
    }
}

window.setInterval(_intervalAnimator, Math.floor(1000 / 60));

function _aniListCleaner() {
    var nDeleted = 0;
    var startTime = new Date().getTime();
    for (var name in aniList) {
        if (aniList[name] instanceof AniItem) {
            if (aniList[name].done) {
                delete aniList[name];
                ++nDeleted;
            }
        }
    }
//    Debug(new Date().toTimeString() + ": done cleaning. " + nDeleted + " items deleted from  aniList in " + (new Date().getTime() - startTime).toString() + "ms.");
}

window.setInterval(_aniListCleaner, 5000);
