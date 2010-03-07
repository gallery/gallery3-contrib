function lT(){}
function xT(){return DP}
function BT(){var a;while(qT){a=qT;qT=qT.c;!qT&&(rT=null);qo(a.b)}}
function yT(){tT=true;sT=(vT(),new lT);Gy((Dy(),Cy),2);!!$stats&&$stats(kz(urb,Bib,null,null));sT.$b();!!$stats&&$stats(kz(urb,rrb,null,null))}
function qo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(trb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Hv(a.d,e.encode());return}Hv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Hv(a.d,e.encode());return}Hv(a.d,a.b)}}
var vrb='AsyncLoader2',trb='beta.canvas',urb='runCallbacks2';_=lT.prototype=new mT;_.gC=xT;_.$b=BT;_.tI=0;var DP=T3(gpb,vrb);yT();