function QS(){}
function aT(){return wP}
function eT(){var a;while(VS){a=VS;VS=VS.b;!VS&&(WS=null);lo(a.a)}}
function bT(){YS=true;XS=($S(),new QS);qy((ny(),my),1);!!$stats&&$stats(Wy(ntb,Bjb,null,null));XS.Zb();!!$stats&&$stats(Wy(ntb,otb,null,null))}
function lo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(mtb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));sv(a.c,e.encode());return}}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);sv(a.c,e.encode());return}}}
var ptb='AsyncLoader1',mtb='beta.canvas',ntb='runCallbacks1';_=QS.prototype=new RS;_.gC=aT;_.Zb=eT;_.tI=0;var wP=L4(Wqb,ptb);bT();