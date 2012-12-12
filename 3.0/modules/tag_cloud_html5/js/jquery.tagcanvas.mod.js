/**
 * Copyright (C) 2010-2012 Graham Breach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * jQuery.tagcanvas 1.17.1
 * For more information, please contact <graham@goat1000.com>
 */
/**
 * Modified by Shad Laws 2012/06/08 -- all modified lines have "mod Shad Laws" comments
 * - built a physics-based model for motion, which is activated with the physModel flag
 * - included initialDecel, deadZone, maxInputZone, and physModel as options
 * - set defaults of new options to mimic behavior without them (false, 0, 0, and false)
 * - removed two unnecessary variable declarations caught by YUI Compressor
 * - fixed initialization of a few variables (minSpeed, decel, yaw/pitch)
 * - fixed problem with html margin-top changing coordinates in IE (but not Chrome or Firefox)
 */
(function($) {
// var i, j, abs = Math.abs, sin = Math.sin, cos = Math.cos, max = Math.max, min = Math.min, // mod Shad Laws
var i, j, abs = Math.abs, sin = Math.sin, cos = Math.cos, max = Math.max, min = Math.min, sqrt = Math.sqrt, // mod Shad Laws
  hexlookup3 = {}, hexlookup2 = {}, hexlookup1 = {
  0:"0,",   1:"17,",  2:"34,",  3:"51,",  4:"68,",  5:"85,",
  6:"102,", 7:"119,", 8:"136,", 9:"153,", a:"170,", A:"170,",
  b:"187,", B:"187,", c:"204,", C:"204,", d:"221,", D:"221,",
  e:"238,", E:"238,", f:"255,", F:"255,"  
}, Oproto, Tproto, TCproto, doc = document, ocanvas, handlers = {};
for(i = 0; i < 256; ++i) {
  j = i.toString(16);
  if(i < 16)
    j = '0' + j;
  hexlookup2[j] = hexlookup2[j.toUpperCase()] = i.toString() + ',';
}
function Defined(d) {
  return typeof(d) != 'undefined';
}
function Shuffle(a) {
  var i = a.length-1, t, p;
  while(i) {
    p = ~~(Math.random()*i);
    t = a[i];
    a[i] = a[p];
    a[p] = t;
    --i;
  }
}
function PointsOnSphere(n,xr,yr,zr) {
  var i, y, r, phi, pts = [], inc = Math.PI * (3-Math.sqrt(5)), off = 2/n;
  for(i = 0; i < n; ++i) {
    y = i * off - 1 + (off / 2);
    r = Math.sqrt(1 - y*y);
    phi = i * inc;
    pts.push([cos(phi) * r * xr, y * yr, sin(phi) * r * zr]);
  }
  return pts;
}
function Cylinder(n,o,xr,yr,zr,i,j,k,l) {
  var phi, pts = [], inc = Math.PI * (3-Math.sqrt(5)), off = 2/n;
  for(i = 0; i < n; ++i) {
    j = i * off - 1 + (off / 2);
    phi = i * inc;
    k = cos(phi);
    l = sin(phi);
    pts.push(o ? [j * xr, k * yr, l * zr] : [k * xr, j * yr, l * zr]);
  }
  return pts;
}
function PointsOnCylinderV(n,xr,yr,zr) { return Cylinder(n, 0, xr, yr, zr) }
function PointsOnCylinderH(n,xr,yr,zr) { return Cylinder(n, 1, xr, yr, zr) }
function SetAlpha(c,a) {
  var d = c, p1, p2, ae = (a*1).toPrecision(3) + ')';
  if(c[0] === '#') {
    if(!hexlookup3[c])
      if(c.length === 4)
        hexlookup3[c] = 'rgba(' + hexlookup1[c[1]] + hexlookup1[c[2]] + hexlookup1[c[3]];
      else
        hexlookup3[c] = 'rgba(' + hexlookup2[c.substr(1,2)] + hexlookup2[c.substr(3,2)] + hexlookup2[c.substr(5,2)];
    d = hexlookup3[c] + ae;
  } else if(c.substr(0,4) === 'rgb(' || c.substr(0,4) === 'hsl(') {
    d = (c.replace('(','a(').replace(')', ',' + ae));
  } else if(c.substr(0,5) === 'rgba(' || c.substr(0,5) === 'hsla(') {
    p1 = c.lastIndexOf(',') + 1, p2 = c.indexOf(')');
    a *= parseFloat(c.substring(p1,p2));
    d = c.substr(0,p1) + a.toPrecision(3) + ')';
  }
  return d;
}
function NewCanvas(w,h) {
  // if using excanvas, give up now
  if(window.G_vmlCanvasManager)
    return null;
  var c = doc.createElement('canvas');
  c.width = w;
  c.height = h;
  return c;
}
// I think all browsers pass this test now...
function ShadowAlphaBroken() {
  var cv = NewCanvas(3,3), c, i;
  if(!cv)
    return false;
  c = cv.getContext('2d');
  c.strokeStyle = '#000';
  c.shadowColor = '#fff';
  c.shadowBlur = 3;
  c.globalAlpha = 0;
  c.strokeRect(2,2,2,2);
  c.globalAlpha = 1;
  i = c.getImageData(2,2,1,1);
  cv = null;
  return (i.data[0] > 0);
}
function FindGradientColour(t,p) {
  var l = 1024, g = t.weightGradient, cv, c, i, gd, d;
  if(t.gCanvas) {
    c = t.gCanvas.getContext('2d');
  } else {
    t.gCanvas = cv = NewCanvas(l,1);
    if(!cv)
      return null;
    c = cv.getContext('2d');
    gd = c.createLinearGradient(0,0,l,0);
    for(i in g)
      gd.addColorStop(1-i, g[i]);
    c.fillStyle = gd;
    c.fillRect(0,0,l,1);
  }
  d = c.getImageData(~~((l-1)*p),0,1,1).data;
  return 'rgba(' + d[0] + ',' + d[1] + ',' + d[2] + ',' + (d[3]/255) + ')';
}
function TextSet(c,f,l,s,sc,sb,so) {
  var xo = (sb || 0) + (so && so[0] < 0 ? abs(so[0]) : 0),
    yo = (sb || 0) + (so && so[1] < 0 ? abs(so[1]) : 0);
  c.font = f;
  c.textBaseline = 'top';
  c.fillStyle = l;
  sc && (c.shadowColor = sc);
  sb && (c.shadowBlur = sb);
  so && (c.shadowOffsetX = so[0], c.shadowOffsetY = so[1]);
  c.fillText(s, xo, yo);
}
function TextToCanvas(s,f,ht,w,h,l,sc,sb,so,padx,pady) {
  var cw = w + abs(so[0]) + sb + sb, ch = h + abs(so[1]) + sb + sb, cv, c;
  cv = NewCanvas(cw+padx,ch+pady);
  if(!cv)
    return null;
  c = cv.getContext('2d');
  TextSet(c,f,l,s,sc,sb,so);
  return cv;
}
function AddShadowToImage(i,sc,sb,so) {
  var cw = i.width + abs(so[0]) + sb + sb, ch = i.height + abs(so[1]) + sb + sb, cv, c,
    xo = (sb || 0) + (so && so[0] < 0 ? abs(so[0]) : 0),
    yo = (sb || 0) + (so && so[1] < 0 ? abs(so[1]) : 0);
  cv = NewCanvas(cw,ch);
  if(!cv)
    return null;
  c = cv.getContext('2d');
  sc && (c.shadowColor = sc);
  sb && (c.shadowBlur = sb);
  so && (c.shadowOffsetX = so[0], c.shadowOffsetY = so[1]);
  c.drawImage(i, xo, yo);
  return cv;
}
function FindTextBoundingBox(s,f,ht) {
  var w = parseInt(s.length * ht), h = parseInt(ht * 2), cv = NewCanvas(w,h), c, idata, w1, h1, x, y, i, ex;
  if(!cv)
    return null;
  c = cv.getContext('2d');
  c.fillStyle = '#000';
  c.fillRect(0,0,w,h);
  TextSet(c,ht + 'px ' + f,'#fff',s)

  idata = c.getImageData(0,0,w,h);
  w1 = idata.width; h1 = idata.height;
  ex = {
    min: { x: w1, y: h1 },
    max: { x: -1, y: -1 }
  };
  for(y = 0; y < h1; ++y) {
    for(x = 0; x < w1; ++x) {
      i = (y * w1 + x) * 4;
      if(idata.data[i+1] > 0) {
        if(x < ex.min.x) ex.min.x = x;
        if(x > ex.max.x) ex.max.x = x;
        if(y < ex.min.y) ex.min.y = y;
        if(y > ex.max.y) ex.max.y = y;
      }
    }
  }
  // device pixels might not be css pixels
  if(w1 != w) {
    ex.min.x *= (w / w1);
    ex.max.x *= (w / w1);
  }
  if(h1 != h) {
    ex.min.y *= (w / h1);
    ex.max.y *= (w / h1);
  }

  cv = null;
  return ex;
}
function FixFont(f) {
  return "'" + f.replace(/(\'|\")/g,'').replace(/\s*,\s*/g, "', '") + "'";
}
function AddHandler(h,f,e) {
  e = e || doc;
  if(e.addEventListener)
    e.addEventListener(h,f,false);
  else
    e.attachEvent('on' + h, f);
}
function AddImage(i,o,t,tc) {
  var tl = tc.taglist, s = tc.imageScale;
  if(s && !(o.width && o.height)) {
    // images are not yet rendered, wait for window onload
    AddHandler('load', function() { AddImage(i,o,t,tc); }, window);
    return;
  }
  if(!i.complete) {
    // image not loaded, wait for image onload
    AddHandler('load',function() { AddImage(i,o,t,tc); }, i);
    return;
  }

  // Yes, this does look like nonsense, but it makes sure that both the
  // width and height are actually set and not just calculated. This is
  // required to keep proportional sizes when the images are hidden, so
  // the images can be used again for another cloud.
  o.width = o.width;
  o.height = o.height;

  if(s) {
    i.width = o.width * s;
    i.height = o.height * s;
  }
  t.w = i.width;
  t.h = i.height;
  tl.push(t);
}
function GetProperty(e,p) {
  var dv = doc.defaultView, pc = p.replace(/\-([a-z])/g,function(a){return a.charAt(1).toUpperCase()});
  return (dv && dv.getComputedStyle && dv.getComputedStyle(e,null).getPropertyValue(p)) ||
    (e.currentStyle && e.currentStyle[pc]);
}
function FindWeight(t,a) {
  var w = 1, p;
  if(t.weightFrom) {
    w = 1 * (a.getAttribute(t.weightFrom) || t.textHeight);
  } else if(p = GetProperty(a,'font-size')) {
    w = (p.indexOf('px') > -1 && p.replace('px','') * 1) ||
      (p.indexOf('pt') > -1 && p.replace('pt','') * 1.25) ||
      p * 3.3;
  } else {
    t.weight = false;
  }
  return w;
}
function MouseOut(e) {
  MouseMove(e);
  var cv = e.target || e.fromElement.parentNode, tc = TagCanvas.tc[cv.id];
  tc && (tc.mx = tc.my = -1);
}
function MouseMove(e) {
  var i, tc, dd = doc.documentElement, o;
  for(i in TagCanvas.tc) {
    tc = TagCanvas.tc[i];
    if(tc.tttimer) {
      clearTimeout(tc.tttimer);
      tc.tttimer = null;
    }
    o = $(tc.canvas).offset();
    // if(e.pageX) { // mod Shad Laws
    if(e.offsetX) { // mod Shad Laws
      // this works for IE
      tc.mx = e.offsetX; // mod Shad Laws
      tc.my = e.offsetY; // mod Shad Laws
    } else if(e.pageX) { // mod Shad Laws
      // this doesn't work for IE --> e.pageY = -parseInt($("html").css("margin-left"), 10) for the top row!
      tc.mx = e.pageX - o.left;
      tc.my = e.pageY - o.top;
    } else {
      tc.mx = e.clientX + (dd.scrollLeft || doc.body.scrollLeft) - o.left;
      tc.my = e.clientY + (dd.scrollTop || doc.body.scrollTop) - o.top;
    }
  }
}
function MouseClick(e) {
  var t = TagCanvas, cb = doc.addEventListener ? 0 : 1,
    tg = e.target && Defined(e.target.id) ? e.target.id : e.srcElement.parentNode.id;
  if(tg && e.button == cb && t.tc[tg]) {
    MouseMove(e);
    t.tc[tg].Clicked(e);
  }
}
function MouseWheel(e) {
  var t = TagCanvas,
    tg = e.target && Defined(e.target.id) ? e.target.id : e.srcElement.parentNode.id;
  if(tg && t.tc[tg]) {
    e.cancelBubble = true;
    e.returnValue = false;
    e.preventDefault && e.preventDefault();
    t.tc[tg].Wheel((e.wheelDelta || e.detail) > 0);
  }
}
function DrawCanvas() {
  var t = TagCanvas.tc, i;
  for(i in t)
    t[i].Draw();
}
function RotX(p1,t) {
  var s = sin(t), c = cos(t); 
  return {x:p1.x, y:(p1.y * c) + (p1.z * s), z:(p1.y * -s) + (p1.z * c)};
}
function RotY(p1,t) {
  var s = sin(t), c = cos(t); 
  return {x:(p1.x * c) + (p1.z * -s), y:p1.y, z:(p1.x * s) + (p1.z * c)};
}
function Project(tc,p1,w,h,sx,sy) {
  var yn, xn, zn, m = tc.z1 / (tc.z1 + tc.z2 + p1.z);
  yn = p1.y * m * sy;
  xn = p1.x * m * sx;
  zn = tc.z2 + p1.z;
  return {x:xn, y:yn, z:zn};
}
/**
 * @constructor
 */
function Outline(tc) {
  this.ts = new Date().valueOf();
  this.tc = tc;
  this.x = this.y = this.w = this.h = this.sc = 1;
  this.z = 0;
  this.Draw = tc.pulsateTo < 1 && tc.outlineMethod != 'colour' ? this.DrawPulsate : this.DrawSimple;
  this.SetMethod(tc.outlineMethod);
}
Oproto = Outline.prototype;
Oproto.SetMethod = function(om) {
  var methods = {
    block: ['PreDraw','DrawBlock'],
    colour: ['PreDraw','DrawColour'],
    outline: ['PostDraw','DrawOutline'],
    classic: ['LastDraw','DrawOutline'],
    none: ['LastDraw']
  }, funcs = methods[om] || methods.outline;
  if(om == 'none') {
    this.Draw = function() { return 1; }
  } else {
    this.drawFunc = this[funcs[1]];
  }
  this[funcs[0]] = this.Draw;
};
Oproto.Update = function(x,y,w,h,sc,p,xo,yo) {
  var o = this.tc.outlineOffset, o2 = 2 * o;
  this.x = sc * x + xo - o;
  this.y = sc * y + yo - o;
  this.w = sc * w + o2;
  this.h = sc * h + o2;
  this.sc = sc; // used to determine frontmost
  this.z = p.z;
};
Oproto.DrawOutline = function(c,x,y,w,h,colour) {
  c.strokeStyle = colour;
  c.strokeRect(x,y,w,h);
};
Oproto.DrawColour = function(c,x,y,w,h,colour,tag,x1,y1) {
  return this[tag.image ? 'DrawColourImage' : 'DrawColourText'](c,x,y,w,h,colour,tag,x1,y1);
};
Oproto.DrawColourText = function(c,x,y,w,h,colour,tag,x1,y1) {
  var normal = tag.colour;
  tag.colour = colour;
  tag.Draw(c,x1,y1);
  tag.colour = normal;
  return 1;
};
Oproto.DrawColourImage = function(c,x,y,w,h,colour,tag,x1,y1) {
  var ccanvas = c.canvas, fx = ~~max(x,0), fy = ~~max(y,0), 
    fw = min(ccanvas.width - fx, w) + .5|0, fh = min(ccanvas.height - fy,h) + .5|0, cc;
  if(ocanvas)
    ocanvas.width = fw, ocanvas.height = fh;
  else
    ocanvas = NewCanvas(fw, fh);
  if(!ocanvas)
    return this.SetMethod('outline'); // if using IE and images, give up!
  cc = ocanvas.getContext('2d');

  cc.drawImage(ccanvas,fx,fy,fw,fh,0,0,fw,fh);
  c.clearRect(fx,fy,fw,fh);
  tag.Draw(c,x1,y1);
  c.setTransform(1,0,0,1,0,0);
  c.save();
  c.beginPath();
  c.rect(fx,fy,fw,fh);
  c.clip();
  c.globalCompositeOperation = 'source-in';
  c.fillStyle = colour;
  c.fillRect(fx,fy,fw,fh);
  c.restore();
  c.globalCompositeOperation = 'destination-over';
  c.drawImage(ocanvas,0,0,fw,fh,fx,fy,fw,fh);
  c.globalCompositeOperation = 'source-over';
  return 1;
};
Oproto.DrawBlock = function(c,x,y,w,h,colour) {
  c.fillStyle = colour;
  c.fillRect(x,y,w,h);
};
Oproto.DrawSimple = function(c, tag, x1, y1) {
  var t = this.tc;
  c.setTransform(1,0,0,1,0,0);
  c.strokeStyle = t.outlineColour;
  c.lineWidth = t.outlineThickness;
  c.shadowBlur = c.shadowOffsetX = c.shadowOffsetY = 0;
  c.globalAlpha = 1;
  return this.drawFunc(c,this.x,this.y,this.w,this.h,t.outlineColour,tag,x1,y1);
};
Oproto.DrawPulsate = function(c, tag, x1, y1) {
  var diff = new Date().valueOf() - this.ts, t = this.tc;
  c.setTransform(1,0,0,1,0,0);
  c.strokeStyle = t.outlineColour;
  c.lineWidth = t.outlineThickness;
  c.shadowBlur = c.shadowOffsetX = c.shadowOffsetY = 0;
  c.globalAlpha = t.pulsateTo + ((1 - t.pulsateTo) * 
    (0.5 + (cos(2 * Math.PI * diff / (1000 * t.pulsateTime)) / 2)));
  return this.drawFunc(c,this.x,this.y,this.w,this.h,t.outlineColour,tag,x1,y1);
};
Oproto.Active = function(c,x,y) {
  return (x >= this.x && y >= this.y &&
    x <= this.x + this.w && y <= this.y + this.h);
};
Oproto.PreDraw = Oproto.PostDraw = Oproto.LastDraw = function() {};
/**
 * @constructor
 */
function Tag(tc,name,a,v,w,h,col,font) {
  var c = tc.ctxt, i;
  this.tc = tc;
  this.image = name.src ? name : null;
  this.name = name.src ? '' : name;
  this.title = a.title || null;
  this.a = a;
  this.p3d = { x: v[0] * tc.radius * 1.1, y: v[1] * tc.radius * 1.1, z: v[2] * tc.radius * 1.1};
  this.x = this.y = 0;
  this.w = w;
  this.h = h;
  this.colour = col || tc.textColour;
  this.textFont = font || tc.textFont;
  this.weight = this.sc = this.alpha = 1;
  this.weighted = !tc.weight;
  this.outline = new Outline(tc);
  if(this.image) {
    if(tc.txtOpt && tc.shadow) {
      i = AddShadowToImage(this.image,tc.shadow,tc.shadowBlur,tc.shadowOffset);
      if(i) {
        this.image = i;
        this.w = i.width;
        this.h = i.height;
      }
    }
  } else {
    this.textHeight = tc.textHeight;
    this.extents = FindTextBoundingBox(this.name, this.textFont, this.textHeight);
    this.Measure(c,tc);
  }
  this.SetShadowColour = tc.shadowAlpha ? this.SetShadowColourAlpha : this.SetShadowColourFixed;
  this.SetDraw(tc);
}
Tproto = Tag.prototype;
Tproto.SetDraw = function(t) {
  this.Draw = this.image ? (t.ie > 7 ? this.DrawImageIE : this.DrawImage) : this.DrawText;
  t.noSelect && (this.CheckActive = function() {});
};
Tproto.Measure = function(c,t) {
  this.h = this.extents ? this.extents.max.y + this.extents.min.y : this.textHeight;
  c.font = this.font = this.textHeight + 'px ' + this.textFont;
  this.w = c.measureText(this.name).width;
  if(t.txtOpt) {
    var s = t.txtScale, th = s * this.textHeight, f = th + 'px ' + this.textFont,
      soff = [s*t.shadowOffset[0],s*t.shadowOffset[1]], cw;
    c.font = f;
    cw = c.measureText(this.name).width;
    this.image = TextToCanvas(this.name, f, th, cw, s * this.h, this.colour,
      t.shadow, s * t.shadowBlur, soff, s, s);
    if(this.image) {
      this.w = this.image.width / s;
      this.h = this.image.height / s;
    }
    this.SetDraw(t);
    t.txtOpt = this.image;
  }
};
Tproto.SetWeight = function(w) {
  if(!this.name.length)
    return;
  this.weight = w;
  this.Weight(this.tc.ctxt, this.tc);
  this.Measure(this.tc.ctxt, this.tc);
};
Tproto.Weight = function(c,t) {
  var w = this.weight, m = t.weightMode;
  this.weighted = true;
  if(m == 'colour' || m == 'both')
    this.colour = FindGradientColour(t, (w - t.min_weight) / (t.max_weight-t.min_weight));
  if(m == 'size' || m == 'both')
    this.textHeight = w * t.weightSize;
  this.extents = FindTextBoundingBox(this.name, this.textFont, this.textHeight);
};
Tproto.SetShadowColourFixed = function(c,s,a) {
  c.shadowColor = s;
};
Tproto.SetShadowColourAlpha = function(c,s,a) {
  c.shadowColor = SetAlpha(s, a);
};
Tproto.DrawText = function(c,xoff,yoff) {
  var t = this.tc, x = this.x, y = this.y, w, h, s = this.sc;
  c.globalAlpha = this.alpha;
  c.setTransform(s,0,0,s,0,0);
  c.fillStyle = this.colour;
  t.shadow && this.SetShadowColour(c,t.shadow,this.alpha);
  c.font = this.font;
  w = this.w;
  h = this.h;
  x += (xoff / s) - (w / 2);
  y += (yoff / s) - (h / 2);
  c.fillText(this.name, x, y);
};
Tproto.DrawImage = function(c,xoff,yoff) {
  //var t = this.tc, x = this.x, y = this.y, s = this.sc, // mod Shad Laws
  var x = this.x, y = this.y, s = this.sc, // mod Shad Laws
    i = this.image, w = this.w, h = this.h, a = this.alpha,
    shadow = this.shadow;
  c.globalAlpha = a;
  c.setTransform(s,0,0,s,0,0);
  c.fillStyle = this.colour;
  shadow && this.SetShadowColour(c,shadow,a);
  x += (xoff / s) - (w / 2);
  y += (yoff / s) - (h / 2);
  c.drawImage(i, x, y, w, h);
};
Tproto.DrawImageIE = function(c,xoff,yoff) {
  var i = this.image, s = this.sc,
    w = i.width = this.w*s, h = i.height = this.h * s,
    x = (this.x*s) + xoff - (w/2), y = (this.y*s) + yoff - (h/2);
  c.setTransform(1,0,0,1,0,0);
  c.globalAlpha = this.alpha;
  c.drawImage(i, x, y);
};
Tproto.Calc = function(yaw,pitch) {
  var pp = RotY(this.p3d,yaw), t = this.tc, mb = t.minBrightness, r = t.radius;
  this.p3d = RotX(pp,pitch);
  pp = Project(t, this.p3d, this.w, this.h, t.stretchX, t.stretchY);
  this.x = pp.x;
  this.y = pp.y;
  this.sc = (t.z1 + t.z2 - pp.z) / t.z2;
  this.alpha = max(mb,min(1,mb + 1 - ((pp.z - t.z2 + r) / (2 * r))));
};
Tproto.CheckActive = function(c,xoff,yoff) {
  var t = this.tc, o = this.outline,
    w = this.w, h = this.h,
    x = this.x - w/2, y = this.y - h/2;
  o.Update(x, y, w, h, this.sc, this.p3d, xoff, yoff);
  return o.Active(c, t.mx, t.my) ? o : null;
};
Tproto.Clicked = function(e) {
  var a = this.a, t = a.target, h = a.href, evt;
  if(t != '' && t != '_self') {
    if(self.frames[t]) {
      self.frames[t] = h;
    } else{
      try {
        if(top.frames[t]) {
          top.frames[t] = h;
          return;
        }
      } catch(err) {
        // different domain/port/protocol?
      }
      window.open(h, t);
    }
    return;
  }
  if(doc.createEvent) {
    evt = doc.createEvent('MouseEvents');
    evt.initMouseEvent('click', 1, 1, window, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, null);
    if(!a.dispatchEvent(evt))
      return;
  } else if(a.fireEvent) {
    if(!a.fireEvent('onclick'))
      return;
  }
  doc.location = h;
};
/**
 * @constructor
 */
function TagCanvas() { 
  var i, opts = {
  mx: -1, my: -1,
  z1: 20000, z2: 20000, z0: 0.0002,
  freezeActive: false,
  activeCursor: 'pointer',
  pulsateTo: 1,
  pulsateTime: 3,
  reverse: false,
  depth: 0.5,
  maxSpeed: 0.05,
  minSpeed: 0,
  decel: 0.95,
  interval: 20,
  initial: null,
  initialDecel: false, // mod Shad Laws
  deadZone: 0, // mod Shad Laws
  physModel: false, // mod Shad Laws
  maxInputZone: 0, // mod Shad Laws
  hideTags: true,
  minBrightness: 0.1,
  outlineColour: '#ffff99',
  outlineThickness: 2,
  outlineOffset: 5,
  outlineMethod: 'outline',
  textColour: '#ff99ff',
  textHeight: 15,
  textFont: 'Helvetica, Arial, sans-serif',
  shadow: '#000',
  shadowBlur: 0,
  shadowOffset: [0,0],
  zoom: 1,
  weight: false,
  weightMode: 'size',
  weightFrom: null,
  weightSize: 1,
  weightGradient: {0:'#f00', 0.33:'#ff0', 0.66:'#0f0', 1:'#00f'},
  txtOpt: true,
  txtScale: 2,
  frontSelect: false,
  wheelZoom: true,
  zoomMin: 0.3,
  zoomMax: 3,
  zoomStep: 0.05,
  shape: 'sphere',
  lock: null,
  tooltip: null,
  tooltipDelay: 300,
  tooltipClass: 'tctooltip',
  radiusX: 1,
  radiusY: 1,
  radiusZ: 1,
  stretchX: 1,
  stretchY: 1,
  shuffleTags: false,
  noSelect: false,
  noMouse: false,
  imageScale: 1
  };
  for(i in opts)
    this[i] = opts[i];
  this.max_weight = 0;
  this.min_weight = 200;
}
TCproto = TagCanvas.prototype;
TCproto.Draw = function() {
  var cv = this.canvas, cw = cv.width, ch = cv.height, max_sc = 0, yaw = this.yaw, pitch = this.pitch,
    x1 = cw / 2, y1 = ch / 2, c = this.ctxt, active, a, i, aindex = -1, tl = this.taglist, l = tl.length,
    frontsel = this.frontSelect;
  //if(yaw == 0 && pitch == 0 && this.drawn) // mod Shad Laws
  if(yaw == 0 && pitch == 0 && this.drawn && !this.zoneActive) // mod Shad Laws
    return  this.Animate(cw,ch);
  c.setTransform(1,0,0,1,0,0);
  this.active = null;
  for(i = 0; i < l; ++i)
    tl[i].Calc(yaw, pitch);
  tl = tl.sort(function(a,b) {return a.sc-b.sc});
  
  for(i = 0; i < l; ++i) {
    a = tl[i].CheckActive(c, x1, y1);
    a = this.mx >= 0 && this.my >= 0 && tl[i].CheckActive(c, x1, y1);
    if(a && a.sc > max_sc && (!frontsel || a.z <= 0)) {
      active = a;
      active.index = aindex = i;
      max_sc = a.sc;
    }
  }
  this.active = active;

  if(!this.txtOpt && this.shadow) {
    c.shadowBlur = this.shadowBlur;
    c.shadowOffsetX = this.shadowOffset[0];
    c.shadowOffsetY = this.shadowOffset[1];
  }
  c.clearRect(0,0,cw,ch);
  for(i = 0; i < l; ++i) {
    if(!(aindex == i && active.PreDraw(c, tl[i], x1, y1)))
      tl[i].Draw(c, x1, y1);
    aindex == i && active.PostDraw(c);
  }
  if(this.freezeActive && active) {
    this.yaw = this.pitch = this.drawn = 0;
  } else {
    this.Animate(cw, ch);
    this.drawn = (l == this.listLength);
  }
  active && active.LastDraw(c);
  cv.style.cursor = active ? this.activeCursor : '';
  this.Tooltip(active,tl[aindex]);
};
TCproto.TooltipNone = function() { };
TCproto.TooltipNative = function(active,tag) {
  this.canvas.title = active && tag.title ? tag.title : '';
};
TCproto.TooltipDiv = function(active,tag) {
  //var tc = this, s = tc.ttdiv.style, cid = tc.canvas.id; // mod Shad Laws
  var tc = this, s = tc.ttdiv.style; // mod Shad Laws
  if(active && tag.title) {
    tc.ttdiv.innerHTML = tag.title;
    if(s.display == 'none' && ! tc.tttimer) {
      tc.tttimer = setTimeout(function() {
        var p = $(tc.canvas).offset();
        s.display = 'block';
        s.left = p.left + tc.mx + 'px';
        s.top = p.top + tc.my + 24 + 'px';
        tc.tttimer = null;
      }, tc.tooltipDelay);
    }
  } else {
    s.display = 'none';
  }
};
TCproto.Animate = function(w,h) { // mod Shad Laws - original function afterward
  var tc = this; // mod Shad Laws
  if(tc.physModel)
  /**
   * Physics-based model -- mod Shad Laws
   * State update equation
   * (normalized inertia J=1 and time-step dt=1, forward Euler discretization)
   *   w1 = w - b*w + m*(u-f) + n*v
   * where w1,w,u,f,v are vectors, b,m,n are scalars:
   *    w = rotational speed at t=k
   *        components wx, wy, magnitude w
   *   w1 = rotational speed at t=k+1
   *        components wx1, wy1, magnitude w1
   *  u-f = actual input from mouse position (-1 <= u-f <= 1)
   *        components ux, uy, magnitude u
   *        coulomb friction f (models "deadZone" effect)
   *        reversed/locked/saturated as defined (models "reverse", "lock", and "maxInputZone" effects)
   *    v = fictitious input (-1 <= v <= 1)
   *        components vx, vy, magnitude v
   *        directed as needed (models "minSpeed" effect)
   *    b = damping (models "decel" effect)
   *    m = maximum actual input (models "maxSpeed" effect)
   *    n = maximum fictitious input (models "minSpeed" effect)
   */
  {
    var x = tc.mx, y = tc.my, xmax = w-1, ymax = h-1, xstr = tc.stretchX, ystr = tc.stretchY;
    var r = tc.reverse ? -1 : 1, l = tc.lock, usat = tc.maxInputZone, zt = tc.z0;
    // calculate physical parameters (b,f,m,n)
    var b = 1-tc.decel, f = tc.deadZone, m = tc.maxSpeed*b, n = tc.minSpeed*b;
    // set rotational velocities, following previous convention where x-rotation is "pitch" and y-rotation is "yaw"
    var wx = tc.pitch, wy = tc.yaw, w = sqrt(wx*wx + wy*wy), wx1, wy1, w1;
    // calculate inputs
    var ux, uy, u, vx, vy, v;
    this.zoneActive = false;
    if(x >= 0 && y >= 0 && x <= xmax && y <= ymax)
    {
      // the "max(xmax/ymax,1)" parts are needed to deal with non-square tag clouds correctly
      ux = -r * (2 * y/ymax - 1) * max(ymax/xmax,1) / ystr;
      uy =  r * (2 * x/xmax - 1) * max(xmax/ymax,1) / xstr;
      if(abs(ux) <= 1+usat && abs(uy) <= 1+usat)
      {
        ux = (l != 'y') ? ux : 0;  
        uy = (l != 'x') ? uy : 0;  
        u = sqrt(ux*ux + uy*uy);
        if(u <= f || f >= 1-zt) // dead zone, zero input
        {
          this.initial = null;
          this.zoneActive = true;
          ux = 0, uy = 0, u = 0;
          vx = 0, vy = 0, v = 0;
        }
        else if(u <= 1) // inside cloud, normal input
        {
          this.initial = null;
          this.zoneActive = true;
          ux = ux/u*(u-f)/(1-f), uy = uy/u*(u-f)/(1-f), u = (u-f)/(1-f);
          vx = 0, vy = 0, v = 0;
        }
        else if(u <= 1+usat) // just outside cloud, saturated input
        {
          this.initial = null;
          this.zoneActive = true;
          ux = ux/u, uy = uy/u, u = 1;
          vx = 0, vy = 0, v = 0;
        }
      }
    }
    if(!this.zoneActive) // outside cloud, fictitious input
    {
      ux = 0, uy = 0, u = 0;
      if(w >= zt)
        vx = wx/w, vy = wy/w, v = 1;
      else
      {
        v = 2*Math.PI*Math.random();
        if(l == 'x')
          vx = 0, vy = v<Math.PI ? 1 : -1, v = 1;
        else if(l == 'y')
          vx = v<Math.PI ? 1 : -1, vy = 0, v = 1;
        else
          vx = cos(v), vy = sin(v), v = 1;
      }
    }
    // update state, if we aren't doing initial perpetual motion
    if(!tc.initial || tc.initialDecel)
    {
      // main state update equation
      wx1 = wx - b*wx + m*ux + n*vx;
      wy1 = wy - b*wy + m*uy + n*vy;
      w1 = sqrt(wx1*wx1 + wy1*wy1);
      // account for zt
      if(w1 < zt)
        wx1 = 0, wy1 = 0, w1 = 0;
      this.pitch = wx1;
      this.yaw = wy1;
    }
  }
  else // without physics-based model
  {
    var x = tc.mx, y = tc.my, l = tc.lock, s, ay, ap, r; // mod Shad Laws
    tc.zoneActive = false; // mod Shad Laws
    if(x >= 0 && y >= 0 && x < w && y < h)
    {
      tc.zoneActive = true; // mod Shad Laws
      //s = tc.maxSpeed, r = tc.reverse ? -1 : 1; // mod Shad Laws
      s = tc.maxSpeed, r = tc.reverse ? -1 : 1, dz = tc.deadZone; // mod Shad Laws
      if(l != 'x')
        //this.yaw = r * ((s * 2 * x / w) - s); // mod Shad Laws
        this.yaw = r * s / max(1-dz,0.000001) * ( max(2*x/w-1-dz,0) + min(2*x/w-1+dz,0) ); // mod Shad Laws
      if(l != 'y')
        //this.pitch = r * -((s * 2 * y / h) - s); // mod Shad Laws
        this.pitch = -r * s / max(1-dz,0.000001) * ( max(2*y/h-1-dz,0) + min(2*y/h-1+dz,0) ); // mod Shad Laws
      this.initial = null;
    }
    //else if(!tc.initial) // mod Shad Laws
    else if(!tc.initial || tc.initialDecel) // mod Shad Laws
    {
      s = tc.minSpeed, ay = abs(tc.yaw), ap = abs(tc.pitch);
      if(l != 'x' && ay > s)
        this.yaw = ay > tc.z0 ? tc.yaw * tc.decel : 0;
      if(l != 'y' && ap > s)
        this.pitch = ap > tc.z0 ? tc.pitch * tc.decel : 0;
    }
  }
};
TCproto.Zoom = function(r) {
  this.z2 = this.z1 * (1/r);
  this.drawn = 0;
};
TCproto.Clicked = function(e) {
  var t = this.taglist, a = this.active;
  try {
    if(a && t[a.index]) 
      t[a.index].Clicked(e);
  } catch(ex) {
  }
};
TCproto.Wheel = function(i) {
  var z = this.zoom + this.zoomStep * (i ? 1 : -1);
  this.zoom = min(this.zoomMax,max(this.zoomMin,z));
  this.Zoom(this.zoom);
};

TagCanvas.tc = {};

jQuery.fn.tagcanvas = function(options,lctr) {
  var links, ctr = lctr ? jQuery('#'+lctr) : this;
  if(doc.all && !lctr) return false; // IE must have external list
  links = ctr.find('a');
  if(Defined(window.G_vmlCanvasManager)) {
    this.each(function() { $(this)[0] = window.G_vmlCanvasManager.initElement($(this)[0]); });
    options.ie = parseFloat(navigator.appVersion.split('MSIE')[1]);
  }

  if(!links.length || !this[0].getContext || !this[0].getContext('2d').fillText)
    return false;

  this.each(function() {
    var i, vl, im, ii, tag, jqt, w, weights = [],
      pfuncs = {
        sphere:PointsOnSphere,
        vcylinder:PointsOnCylinderV,
        hcylinder:PointsOnCylinderH
      };

    // if using internal links, get only the links for this canvas
    lctr || (links = $(this).find('a'));
      
    jqt = new TagCanvas;
    for(i in options)
      jqt[i] = options[i];

    jqt.z1 = (19800 / (Math.exp(jqt.depth) * (1-1/Math.E))) +
      20000 - 19800 / (1-(1/Math.E));
    jqt.z2 = jqt.z1 * (1/jqt.zoom);

    jqt.radius = (this.height > this.width ? this.width : this.height)
      * 0.33 * (jqt.z2 + jqt.z1) / (jqt.z1);
    //jqt.yaw = jqt.initial ? jqt.initial[0] * jqt.maxSpeed : 0; // mod Shad Laws
    //jqt.pitch = jqt.initial ? jqt.initial[1] * jqt.maxSpeed : 0; // mod Shad Laws
    jqt.yaw = jqt.initial && (jqt.lock != 'x') ? jqt.initial[0] * jqt.maxSpeed : 0;
    jqt.pitch = jqt.initial && (jqt.lock != 'y') ? jqt.initial[1] * jqt.maxSpeed : 0;
    jqt.canvas = $(this)[0];
    jqt.ctxt = jqt.canvas.getContext('2d');
    jqt.textFont = jqt.textFont && FixFont(jqt.textFont);
    jqt.deadZone *= 1; // mod Shad Laws
    jqt.minSpeed *= 1; // mod Shad Laws
    jqt.decel *= 1; // mod Shad Laws
    jqt.maxInputZone *= 1; // mod Shad Laws
    jqt.pulsateTo *= 1;
    jqt.textHeight *= 1;
    jqt.minBrightness *= 1;
    jqt.ctxt.textBaseline = 'top';
    if(jqt.shadowBlur || jqt.shadowOffset[0] || jqt.shadowOffset[1]) {
      // let the browser translate "red" into "#ff0000"
      jqt.ctxt.shadowColor = jqt.shadow;
      jqt.shadow = jqt.ctxt.shadowColor;
      jqt.shadowAlpha = ShadowAlphaBroken();
    } else {
      delete jqt.shadow;
    }
    jqt.taglist = [];

    jqt.shape = pfuncs[jqt.shape] || pfuncs.sphere;
    vl = jqt.shape(links.length, jqt.radiusX, jqt.radiusY, jqt.radiusZ);
    jqt.shuffleTags && Shuffle(vl);
    jqt.listLength = links.length;
    for(i = 0; i < links.length; ++i) {
      im = links[i].getElementsByTagName('img');
      if(im.length) {
        ii = new Image;
        ii.src = im[0].src;
        tag = new Tag(jqt,ii, links[i], vl[i], 1, 1);
        AddImage(ii,im[0],tag,jqt);
      } else {
        jqt.taglist.push(new Tag(jqt,links[i].innerText || links[i].textContent, links[i],
          vl[i], 2, jqt.textHeight + 2, jqt.textColour || GetProperty(links[i],'color'),
          jqt.textFont || FixFont(GetProperty(links[i],'font-family'))));
      }
      if(jqt.weight) {
        w = FindWeight(jqt,links[i]);
        if(w > jqt.max_weight) jqt.max_weight = w;
        if(w < jqt.min_weight) jqt.min_weight = w;
        weights.push(w);
      }
    }
    if(jqt.weight = (jqt.max_weight > jqt.min_weight)) {
      for(i = 0; i < jqt.taglist.length; ++i) {
        jqt.taglist[i].SetWeight(weights[i]);
      }
    }

    TagCanvas.tc[$(this)[0].id] = jqt;
    jqt.Tooltip = (jqt.tooltip == 'native' ? jqt.TooltipNative : (jqt.tooltip ? jqt.TooltipDiv : jqt.TooltipNone));
    if(jqt.tooltip) {
      if(jqt.tooltip == 'native') {
        jqt.Tooltip = jqt.TooltipNative;
      } else {
        jqt.Tooltip = jqt.TooltipDiv;
        if(!jqt.ttdiv) {
          jqt.ttdiv = doc.createElement('div');
          jqt.ttdiv.className = jqt.tooltipClass;
          jqt.ttdiv.style.position = 'absolute';
          jqt.ttdiv.style.zIndex = jqt.canvas.style.zIndex + 1;
          AddHandler('mouseover',function(e){e.target.style.display='none';},jqt.ttdiv);
          doc.body.appendChild(jqt.ttdiv);
        }
      }
    } else {
      jqt.Tooltip = jqt.TooltipNone;
    }
    if(!jqt.noMouse && !handlers[$(this)[0].id]) {
      // for some reason, using bind with mouseup isn't working in IE
      AddHandler('mousemove', MouseMove, this);
      AddHandler('mouseout', MouseOut, this);
      AddHandler('mouseup', MouseClick, this);
      if(jqt.wheelZoom) {
        AddHandler('mousewheel', MouseWheel, this);
        AddHandler('DOMMouseScroll', MouseWheel, this);
      }
      handlers[$(this)[0].id] = 1;
    }
    if(lctr && jqt.hideTags) {
      if(TagCanvas.loaded)
        $(ctr).hide();
      else
        AddHandler('load', function() { $(ctr).hide() }, window);
    }
    options.interval = options.interval || jqt.interval;
  });
  return !!(TagCanvas.started || (TagCanvas.started = setInterval(DrawCanvas, options.interval)));
};
// set a flag for when the window has loaded
AddHandler('load',function(){TagCanvas.loaded=1;},window);
})(jQuery);
