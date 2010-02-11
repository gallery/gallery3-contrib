function QS(){}
function aT(){return AP}
function eT(){var a;while(VS){a=VS;VS=VS.c;!VS&&(WS=null);qo(a.b)}}
function bT(){YS=true;XS=($S(),new QS);ry((oy(),ny),1);!!$stats&&$stats(Xy(Xrb,Wib,null,null));XS.cc();!!$stats&&$stats(Xy(Xrb,Yrb,null,null))}
function qo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Wrb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));xv(a.d,e.encode());return}}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);xv(a.d,e.encode());return}}}
var Zrb='AsyncLoader1',Wrb='beta.canvas',Xrb='runCallbacks1';_=QS.prototype=new RS;_.gC=aT;_.cc=eT;_.tI=0;var AP=b4(Jpb,Zrb);bT();