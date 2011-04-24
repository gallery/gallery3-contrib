function QU(){}
function aV(){return fR}
function eV(){var a;while(VU){a=VU;VU=VU.c;!VU&&(WU=null);Co(a.b)}}
function bV(){YU=true;XU=($U(),new QU);$z((Xz(),Wz),2);!!$stats&&$stats(EA(Dtb,vkb,null,null));XU.Zb();!!$stats&&$stats(EA(Dtb,Atb,null,null))}
function Co(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Ctb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));vw(a.d,e.encode());return}vw(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);vw(a.d,e.encode());return}vw(a.d,a.b)}}
var Etb='AsyncLoader2',Ctb='beta.canvas',Dtb='runCallbacks2';_=QU.prototype=new RU;_.gC=aV;_.Zb=eV;_.tI=0;var fR=J5(orb,Etb);bV();