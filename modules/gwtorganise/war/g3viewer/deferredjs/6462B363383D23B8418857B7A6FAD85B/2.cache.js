function YU(){}
function iV(){return iR}
function mV(){var a;while(bV){a=bV;bV=bV.b;!bV&&(cV=null);Bo(a.a)}}
function jV(){eV=true;dV=(gV(),new YU);_z((Yz(),Xz),2);!!$stats&&$stats(FA(rub,Ykb,null,null));dV.Zb();!!$stats&&$stats(FA(rub,oub,null,null))}
function Bo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(qub);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));tw(a.c,e.encode());return}tw(a.c,a.a)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);tw(a.c,e.encode());return}tw(a.c,a.a)}}
var sub='AsyncLoader2',qub='beta.canvas',rub='runCallbacks2';_=YU.prototype=new ZU;_.gC=iV;_.Zb=mV;_.tI=0;var iR=q6(Yrb,sub);jV();