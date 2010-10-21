function oV(){}
function AV(){return yR}
function EV(){var a;while(tV){a=tV;tV=tV.b;!tV&&(uV=null);Do(a.a)}}
function BV(){wV=true;vV=(yV(),new oV);cA((_z(),$z),2);!!$stats&&$stats(IA(Mvb,Mlb,null,null));vV.Zb();!!$stats&&$stats(IA(Mvb,Jvb,null,null))}
function Do(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Lvb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));vw(a.c,e.encode());return}vw(a.c,a.a)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);vw(a.c,e.encode());return}vw(a.c,a.a)}}
var Nvb='AsyncLoader2',Lvb='beta.canvas',Mvb='runCallbacks2';_=oV.prototype=new pV;_.gC=AV;_.Zb=EV;_.tI=0;var yR=f7(ptb,Nvb);BV();