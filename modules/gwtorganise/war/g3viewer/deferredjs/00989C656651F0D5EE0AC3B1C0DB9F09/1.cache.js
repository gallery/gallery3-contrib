function JS(){}
function VS(){return uP}
function ZS(){var a;while(OS){a=OS;OS=OS.c;!OS&&(PS=null);ro(a.b)}}
function WS(){RS=true;QS=(TS(),new JS);ty((qy(),py),1);!!$stats&&$stats(Zy(Yrb,Zib,null,null));QS.ac();!!$stats&&$stats(Zy(Yrb,Zrb,null,null))}
function ro(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Xrb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));zv(a.d,e.encode());return}}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);zv(a.d,e.encode());return}}}
var $rb='AsyncLoader1',Xrb='beta.canvas',Yrb='runCallbacks1';_=JS.prototype=new KS;_.gC=VS;_.ac=ZS;_.tI=0;var uP=_3(Lpb,$rb);WS();