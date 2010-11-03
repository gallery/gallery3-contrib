<?php
/**
 *   FLVMetaData - This tiny class is provided to parse FLV file header and
 *   get the most common metadata like the width, height, framerate, duration and etc.
 *   for more information see http://www.adobe.com/devnet/flv/
 *
 *   Note: Metadata fileds might be dependent upon the software used to create the FLV.
 *
 * -----------------------------------------------------------------------------
 * LICENSE:
 *
 *   FLVMetaData is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   FLVMetaData is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with FLVMetaData.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Amin Saeedi, <amin.w3dev@gmail.com>
 * @copyright Copyright (c) 2009, Amin Saeedi
 * @version 1.0
 *
 */
class FLVMetaData {
    private $buffer;
    private $metaData;
    private $fileName;
    private $typeFlagsAudio;
    private $typeFlagsVideo;

    public $VCidMap = array(
      2=>"Sorenson H.263",
      3=>"Screen Video",
      4=>"VP6",
      5=>"VP6 with Alpha channel",
    );      //Video Codec ID(s)

    public $ACidMap = array(
      "Linear PCM, platform endian",
      "ADPCM",
      "MP3",
      "Linear PCM, little endian",
      "Nellymoser 16-kHz Mono",
      "Nellymoser 8-kHz Mono",
      "Nellymoser",
      "G.711 A-law logarithmic PCM",
      "G.711 mu-law logarithmic PCM",
      "reserved",
      "AAC",
      "Speex",
      14=>"MP3 8-Khz",
      15=>"Device-specific sound"
    );      //Audio Codec ID(s)

/**
 *  CONSTRUCTOR : initialize class members
 *
 * @param string $flv : flv file path
 */
    public function  __construct($flv) {
        $this->fileName = $flv;
        $this->metaData = array(
        "duration"=>null,
        "size"=>null,
        "framerate"=>null,
        "width"=>null,
        "height"=>null,
        "videodatarate"=>null,
        "audiodatarate"=>null,
        "audiodelay"=>null,
        "audiosamplesize"=>null,
        "audiosamplerate"=>null,
        "audiocodecid"=>null,
        "videocodecid"=>null,
        "version"=>null,
        "headersize"=>0
        );
    }

/**
 * Gets metadata of FLV file
 *
 * @return array $this->metaData : matadata of FLV
 */
    public function getMetaData(){
        if(!file_exists($this->fileName)){
            echo "Error! {$this->fileName} does not exist.<br />";
            return false;
        }
        if(!is_readable($this->fileName)){
            echo "Error! Could not read the file. Check the file permissions.<br />";
            return false;
        }
        $f = @fopen($this->fileName,"rb");
        if(!$f){
            echo "Unknown Error! Could not read the file.<br />";
            return;
        }
        $signature = fread($f,3);
        if($signature != "FLV"){
            echo "Error! Wrong file format.";
            return false;
        }
        $this->metaData["version"] = ord(fread($f,1));
        $this->metaData["size"] = filesize($this->fileName);

        $flags = ord(fread($f,1));
        $flags = sprintf("%'04b", $flags);
        $this->typeFlagsAudio = substr($flags, 1, 1);
        $this->typeFlagsVideo = substr($flags, 3, 1);

        for ($i=0; $i < 4; $i++) {
            $this->metaData["headersize"] += ord(fread($f,1)) ;
        }

        $this->buffer = fread($f, 400);
        fclose($f);
        if(strpos($this->buffer, "onMetaData") === false){
            echo "Error! No MetaData Exists.";
            return false;
        }

        foreach($this->metaData as $k=>$v){
            $this->parseBuffer($k);
        }
        return $this->metaData;
    }

/**
 * Takes a field name of metadata, retrieve it's value and set it in $this->metaData
 *
 * @param string $fieldName : matadata field name
 */
    private function parseBuffer($fieldName){
        $fieldPos = strpos($this->buffer, $fieldName);  //get the field position
        if($fieldPos !== false){
            $pos = $fieldPos + strlen($fieldName) + 1;
            $buffer = substr($this->buffer,$pos);

            $d = "";
            for($i=0; $i < 8;$i++){
                $d .= sprintf("%08b", ord(substr($buffer,$i,1)));
            }

            $total = self::bin2Double($d);
            $this->metaData[$fieldName] = $total;
        }
    }

/**
 * Calculates double-precision value of given binary string
 * (IEEE Standard 754 - Floating Point Numbers)
 *
 * @param string binary data $strBin
 * @return Float calculated double-precision number
 */
    public static function bin2Double($strBin){
            $sb = substr($strBin, 0, 1);    // first bit is sign bit
            $exponent = substr($strBin, 1, 11); // 11 bits exponent
            $fraction = "1".substr($strBin, 12, 52);    //52 bits fraction (1.F)

            $s = pow(-1, bindec($sb));
            $dec = pow(2, (bindec($exponent) - 1023));  //Decode exponent

            if($dec == 2047){
                if($fraction == 0){
                    if($s==0){
                        echo "Infinity";
                    }else{
                        echo "-Infinity";
                    }
                }else{
                    echo "NaN";
                }
            }

            if($dec > 0 && $dec < 2047){
                $t = 1;
                for($i=1 ; $i <= 53; $i++){
                    $t += ((int)substr($fraction, $i, 1)) * pow(2, -$i);    //decode significand
                }
                $total = $s * $t * $dec ;
                return  $total;
            }
            return false;
    }
}
?>
