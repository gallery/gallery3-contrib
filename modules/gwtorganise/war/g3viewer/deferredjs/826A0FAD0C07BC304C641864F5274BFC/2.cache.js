function OT(){}
function $T(){return aQ}
function cU(){var a;while(TT){a=TT;TT=TT.c;!TT&&(UT=null);xo(a.b)}}
function _T(){WT=true;VT=(YT(),new OT);Ny((Ky(),Jy),2);!!$stats&&$stats(rz(zsb,vjb,null,null));VT.cc();!!$stats&&$stats(rz(zsb,wsb,null,null))}
function xo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(ysb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Ov(a.d,e.encode());return}Ov(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Ov(a.d,e.encode());return}Ov(a.d,a.b)}}
var Asb='AsyncLoader2',ysb='beta.canvas',zsb='runCallbacks2';_=OT.prototype=new PT;_.gC=$T;_.cc=cU;_.tI=0;var aQ=T4(hqb,Asb);_T();