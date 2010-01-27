<!--

// Copyright (c) 1996-1997 Athenia Associates.
// http://www.webreference.com/js/
// License is granted if and only if this entire
// copyright notice is included. By Tomer Shiran.

function setCookie (name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) + (expires ? "; expires=" + expires : "") + (path ? "; path=" + path : "") + (domain ? "; domain=" + domain : "") + (secure ? "secure" : "");
  document.cookie = curCookie;
}

function getCookie (name) {
  var prefix = name + '=';
  var c = document.cookie;
  var nullstring = '';
  var cookieStartIndex = c.indexOf(prefix);
  if (cookieStartIndex == -1)
      return nullstring;
  var cookieEndIndex = c.indexOf(";", cookieStartIndex + prefix.length);
  if (cookieEndIndex == -1)
      cookieEndIndex = c.length;
  return unescape(c.substring(cookieStartIndex + prefix.length, cookieEndIndex));
}

function deleteCookie (name, path, domain) {
  if (getCookie(name))
    document.cookie = name + "=" + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
}

function fixDate (date) {
  var base = new Date(0);
  var skew = base.getTime();
  if (skew > 0)
    date.setTime(date.getTime() - skew);
}

function rememberMe (f) {
  var now = new Date();
  fixDate(now);
  now.setTime(now.getTime() + 365 * 24 * 60 * 60 * 1000);
  now = now.toGMTString();
  if (f.author != undefined)
    setCookie('mtcmtauth', f.author.value, now, '/', '', '');
  if (f.email != undefined)
    setCookie('mtcmtmail', f.email.value, now, '/', '', '');
  if (f.url != undefined)
    setCookie('mtcmthome', f.url.value, now, '/', '', '');
}

function forgetMe (f) {
  deleteCookie('mtcmtmail', '/', '');
  deleteCookie('mtcmthome', '/', '');
  deleteCookie('mtcmtauth', '/', '');
  f.email.value = '';
  f.author.value = '';
  f.url.value = '';
}

//-->
