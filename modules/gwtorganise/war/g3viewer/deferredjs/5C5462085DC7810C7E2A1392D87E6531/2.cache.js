function QT(){}
function aU(){return $P}
function eU(){var a;while(VT){a=VT;VT=VT.b;!VT&&(WT=null);so(a.a)}}
function bU(){YT=true;XT=($T(),new QT);Oy((Ly(),Ky),2);!!$stats&&$stats(sz(Utb,dkb,null,null));XT.Zb();!!$stats&&$stats(sz(Utb,Rtb,null,null))}
function so(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Ttb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));Kv(a.c,e.encode());return}Kv(a.c,a.a)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);Kv(a.c,e.encode());return}Kv(a.c,a.a)}}
var Vtb='AsyncLoader2',Ttb='beta.canvas',Utb='runCallbacks2';_=QT.prototype=new RT;_.gC=aU;_.Zb=eU;_.tI=0;var $P=D5(xrb,Vtb);bU();