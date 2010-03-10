function oT(){}
function AT(){return FP}
function ET(){var a;while(tT){a=tT;tT=tT.c;!tT&&(uT=null);ro(a.b)}}
function BT(){wT=true;vT=(yT(),new oT);Iy((Fy(),Ey),2);!!$stats&&$stats(mz(Mrb,Pib,null,null));vT.Zb();!!$stats&&$stats(mz(Mrb,Jrb,null,null))}
function ro(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Lrb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Jv(a.d,e.encode());return}Jv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Jv(a.d,e.encode());return}Jv(a.d,a.b)}}
var Nrb='AsyncLoader2',Lrb='beta.canvas',Mrb='runCallbacks2';_=oT.prototype=new pT;_.gC=AT;_.Zb=ET;_.tI=0;var FP=g4(xpb,Nrb);BT();