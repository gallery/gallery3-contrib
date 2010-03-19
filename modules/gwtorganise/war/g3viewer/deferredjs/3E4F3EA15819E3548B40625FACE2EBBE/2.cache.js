function JT(){}
function VT(){return YP}
function ZT(){var a;while(OT){a=OT;OT=OT.c;!OT&&(PT=null);yo(a.b)}}
function WT(){RT=true;QT=(TT(),new JT);Ry((Oy(),Ny),2);!!$stats&&$stats(vz(zsb,xjb,null,null));QT.ac();!!$stats&&$stats(vz(zsb,wsb,null,null))}
function yo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(ysb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Rv(a.d,e.encode());return}Rv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Rv(a.d,e.encode());return}Rv(a.d,a.b)}}
var Asb='AsyncLoader2',ysb='beta.canvas',zsb='runCallbacks2';_=JT.prototype=new KT;_.gC=VT;_.ac=ZT;_.tI=0;var YP=Q4(iqb,Asb);WT();