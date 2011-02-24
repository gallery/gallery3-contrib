function NU(){}
function ZU(){return dR}
function bV(){var a;while(SU){a=SU;SU=SU.c;!SU&&(TU=null);Bo(a.b)}}
function $U(){VU=true;UU=(XU(),new NU);Yz((Vz(),Uz),2);!!$stats&&$stats(CA(ltb,hkb,null,null));UU.$b();!!$stats&&$stats(CA(ltb,itb,null,null))}
function Bo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(ktb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));tw(a.d,e.encode());return}tw(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);tw(a.d,e.encode());return}tw(a.d,a.b)}}
var mtb='AsyncLoader2',ktb='beta.canvas',ltb='runCallbacks2';_=NU.prototype=new OU;_.gC=ZU;_.$b=bV;_.tI=0;var dR=u5(Zqb,mtb);$U();