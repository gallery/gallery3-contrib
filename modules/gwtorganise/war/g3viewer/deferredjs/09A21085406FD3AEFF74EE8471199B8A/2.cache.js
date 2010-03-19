function qT(){}
function CT(){return HP}
function GT(){var a;while(vT){a=vT;vT=vT.c;!vT&&(wT=null);ro(a.b)}}
function DT(){yT=true;xT=(AT(),new qT);Ky((Hy(),Gy),2);!!$stats&&$stats(oz(Lrb,Oib,null,null));xT.Zb();!!$stats&&$stats(oz(Lrb,Irb,null,null))}
function ro(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(Krb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Kv(a.d,e.encode());return}Kv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Kv(a.d,e.encode());return}Kv(a.d,a.b)}}
var Mrb='AsyncLoader2',Krb='beta.canvas',Lrb='runCallbacks2';_=qT.prototype=new rT;_.gC=CT;_.Zb=GT;_.tI=0;var HP=f4(wpb,Mrb);DT();