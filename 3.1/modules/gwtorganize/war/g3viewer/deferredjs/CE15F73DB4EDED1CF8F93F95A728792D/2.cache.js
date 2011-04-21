function hV(){}
function tV(){return wR}
function xV(){var a;while(mV){a=mV;mV=mV.c;!mV&&(nV=null);Jo(a.b)}}
function uV(){pV=true;oV=(rV(),new hV);fA((cA(),bA),2);!!$stats&&$stats(LA(rub,elb,null,null));oV.ac();!!$stats&&$stats(LA(rub,oub,null,null))}
function Jo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(qub);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Cw(a.d,e.encode());return}Cw(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Cw(a.d,e.encode());return}Cw(a.d,a.b)}}
var sub='AsyncLoader2',qub='beta.canvas',rub='runCallbacks2';_=hV.prototype=new iV;_.gC=tV;_.ac=xV;_.tI=0;var wR=s6(asb,sub);uV();