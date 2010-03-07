function OT(){}
function $T(){return YP}
function cU(){var a;while(TT){a=TT;TT=TT.b;!TT&&(UT=null);so(a.a)}}
function _T(){WT=true;VT=(YT(),new OT);My((Jy(),Iy),2);!!$stats&&$stats(qz(Rtb,akb,null,null));VT.Zb();!!$stats&&$stats(qz(Rtb,Otb,null,null))}
function so(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Qtb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));Jv(a.c,e.encode());return}Jv(a.c,a.a)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);Jv(a.c,e.encode());return}Jv(a.c,a.a)}}
var Stb='AsyncLoader2',Qtb='beta.canvas',Rtb='runCallbacks2';_=OT.prototype=new PT;_.gC=$T;_.Zb=cU;_.tI=0;var YP=B5(urb,Stb);_T();