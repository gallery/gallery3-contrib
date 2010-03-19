function nT(){}
function zT(){return FP}
function DT(){var a;while(sT){a=sT;sT=sT.c;!sT&&(tT=null);qo(a.b)}}
function AT(){vT=true;uT=(xT(),new nT);Iy((Fy(),Ey),2);!!$stats&&$stats(mz(trb,Aib,null,null));uT.$b();!!$stats&&$stats(mz(trb,qrb,null,null))}
function qo(a){var b,c,d,e,f;e=($wnd.google&&$wnd.google.gears&&$wnd.google.gears.factory).create(srb);e.decode(a.b);d=e.width;c=e.height;f=d/a.c.c;b=c/a.c.b;if(f>b){if(f>1){e.resize(a.c.c,~~Math.max(Math.min(c/f,2147483647),-2147483648));Iv(a.d,e.encode());return}Iv(a.d,a.b)}else{if(b>1){e.resize(~~Math.max(Math.min(d/b,2147483647),-2147483648),a.c.b);Iv(a.d,e.encode());return}Iv(a.d,a.b)}}
var urb='AsyncLoader2',srb='beta.canvas',trb='runCallbacks2';_=nT.prototype=new oT;_.gC=zT;_.$b=DT;_.tI=0;var FP=S3(fpb,urb);AT();