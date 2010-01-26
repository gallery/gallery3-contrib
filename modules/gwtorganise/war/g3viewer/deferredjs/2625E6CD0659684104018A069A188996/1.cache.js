function _R(){}
function lS(){return OO}
function pS(){var a;while(eS){a=eS;eS=eS.c;!eS&&(fS=null);jo(a.b)}}
function mS(){hS=true;gS=(jS(),new _R);cy((_x(),$x),1);!!$stats&&$stats(Iy(uqb,Bhb,null,null));gS.Zb();!!$stats&&$stats(Iy(uqb,vqb,null,null))}
function jo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(tqb);e.decode(a.b);d=e.width;c=e.height;f=~~(d/a.c.c);b=~~(c/a.c.b);if(f>b){if(f>1){e.resize(a.c.c,~~(c/f));$u(a.d,e.encode())}}else{if(b>1){e.resize(~~(d/b),a.c.b);$u(a.d,e.encode())}}}
var wqb='AsyncLoader1',tqb='beta.canvas',uqb='runCallbacks1';_=_R.prototype=new aS;_.gC=lS;_.Zb=pS;_.tI=0;var OO=e3(job,wqb);mS();