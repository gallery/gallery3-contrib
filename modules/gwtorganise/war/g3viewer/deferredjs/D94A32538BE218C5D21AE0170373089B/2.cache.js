function yT(){}
function KT(){return KP}
function OT(){var a;while(DT){a=DT;DT=DT.b;!DT&&(ET=null);qo(a.a)}}
function LT(){GT=true;FT=(IT(),new yT);Ly((Iy(),Hy),2);!!$stats&&$stats(pz(zsb,pjb,null,null));FT.Zb();!!$stats&&$stats(pz(zsb,wsb,null,null))}
function qo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(ysb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));Iv(a.c,e.encode());return}Iv(a.c,a.a)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);Iv(a.c,e.encode());return}Iv(a.c,a.a)}}
var Asb='AsyncLoader2',ysb='beta.canvas',zsb='runCallbacks2';_=yT.prototype=new zT;_.gC=KT;_.Zb=OT;_.tI=0;var KP=O4(eqb,Asb);LT();