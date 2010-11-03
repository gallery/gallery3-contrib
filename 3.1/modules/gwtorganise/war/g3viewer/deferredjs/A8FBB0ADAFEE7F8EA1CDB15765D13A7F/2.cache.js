function oV(){}
function AV(){return CR}
function EV(){var a;while(tV){a=tV;tV=tV.c;!tV&&(uV=null);Io(a.b)}}
function BV(){wV=true;vV=(yV(),new oV);dA((aA(),_z),2);!!$stats&&$stats(JA(qub,blb,null,null));vV.cc();!!$stats&&$stats(JA(qub,nub,null,null))}
function Io(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(pub);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Aw(a.d,e.encode());return}Aw(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Aw(a.d,e.encode());return}Aw(a.d,a.b)}}
var rub='AsyncLoader2',pub='beta.canvas',qub='runCallbacks2';_=oV.prototype=new pV;_.gC=AV;_.cc=EV;_.tI=0;var CR=u6($rb,rub);BV();