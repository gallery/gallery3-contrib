function wT(){}
function IT(){return IP}
function MT(){var a;while(BT){a=BT;BT=BT.b;!BT&&(CT=null);qo(a.a)}}
function JT(){ET=true;DT=(GT(),new wT);Jy((Gy(),Fy),2);!!$stats&&$stats(nz(wsb,mjb,null,null));DT.Zb();!!$stats&&$stats(nz(wsb,tsb,null,null))}
function qo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(vsb);e.decode(a.a);d=e.width;c=e.height;f=d/a.b.b;b=c/a.b.a;if(f>b){if(f>1){e.resize(a.b.b,~~Math.max(Math.min(c/f,2147483647),-2147483648));Hv(a.c,e.encode());return}Hv(a.c,a.a)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.b.a);Hv(a.c,e.encode());return}Hv(a.c,a.a)}}
var xsb='AsyncLoader2',vsb='beta.canvas',wsb='runCallbacks2';_=wT.prototype=new xT;_.gC=IT;_.Zb=MT;_.tI=0;var IP=M4(bqb,xsb);JT();