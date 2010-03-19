function QT(){}
function aU(){return cQ}
function eU(){var a;while(VT){a=VT;VT=VT.c;!VT&&(WT=null);xo(a.b)}}
function bU(){YT=true;XT=($T(),new QT);Py((My(),Ly),2);!!$stats&&$stats(tz(ysb,ujb,null,null));XT.cc();!!$stats&&$stats(tz(ysb,vsb,null,null))}
function xo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(xsb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Pv(a.d,e.encode());return}Pv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Pv(a.d,e.encode());return}Pv(a.d,a.b)}}
var zsb='AsyncLoader2',xsb='beta.canvas',ysb='runCallbacks2';_=QT.prototype=new RT;_.gC=aU;_.cc=eU;_.tI=0;var cQ=S4(gqb,zsb);bU();