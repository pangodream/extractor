<?php
/**
 * Created by Pangodream.
 * Date: 20/10/2019
 * Time: 6:51
 */

namespace Extractor;
use Html2Text\Html2Text;

class Extractor
{
    public static function extractText($html){
        $extract = new Html2Text($html);
        $txt = $extract->getText();
        return $txt;
    }
    public static function analyze($html){
        $result = [
            'facebook'=>false,
            'twitter'=>false,
            'instagram'=>false,
            'youtube'=>false,
            'pinterest'=>false,
            'ecommerce'=>'No',
            'analytics'=>false,
            'responsive'=>false,
            'email'=>[],
            'telf'=>'',
            'keywords'=>[],
            'linkedin'=>false,
            'googleplus'=>false,
            'carrito'=>false,
            'parking'=>''
        ];
        $txt = self::extractText($html);
        $html = strtolower($html);
        if(strpos($html, 'facebook.com/') !== false){
            $result['facebook'] = true;
        }
        if(strpos($html, 'twitter.com/') !== false){
            $result['twitter'] = true;
        }
        if(strpos($html, 'instagram.com/') !== false){
            $result['instagram'] = true;
        }
        if(strpos($html, 'youtube.com/') !== false){
            $result['youtube'] = true;
        }
        if(strpos($html, 'pinterest.com/') !== false){
            $result['pinterest'] = true;
        }
        if(strpos($html, 'linkedin.com/') !== false){
            $result['linkedin'] = true;
        }
        if(strpos($html, 'plus.google.com/') !== false){
            $result['googleplus'] = true;
        }
        if(strpos($html, 'analytics') !== false){
            $result['analytics'] = true;
        }

        $result['keywords'] = self::getWords($txt);
        $result['email'] = self::getEmails($txt);
        $result['telf'] = self::getTelf($txt);
        $result['ecommerce'] = self::isShop($html);
        $result['carrito'] = self::hasCarrito($html, $txt);
        $result['parking'] = self::extractParking($html);
        $result['responsive'] = self::calculateResponsive($html);
        return $result;
    }
    private static function getTelf($txt){
        $res = self::getTelfB($txt, "9");
        if($res == ""){
            $res = self::getTelfB($txt, "6");
        }
        return $res;
    }
    private static function getTelfB($txt, $primer){
        $continuar = true;
        $i = -1;
        while($continuar){
            $candidato = "";
            $i=strpos($txt, $primer, $i+1);
            if($i !== false){
                $buscar = true;
                $fallidos = 0;
                while($buscar){
                    $let = substr($txt, $i, 1);
                    if($let >= "0" && $let <= "9"){
                        $candidato .= $let;
                    }else{
                        $fallidos++;
                    }
                    if($fallidos == 3 || $i >= strlen($txt)){
                        $buscar = false;
                    }
                    $i++;
                }
                if($i >= strlen($txt) || strlen($candidato)==9){
                    $continuar = false;
                }
            }else{
                $continuar = false;
            }
        }
        if(strlen($candidato)!=9){
            $candidato = "";
        }
        return $candidato;
    }
    private static function isShop($html){
        $shop = "No";
        $lHtml = strtolower($html);
        if(substr_count($lHtml, "prestashop")>0){
            $shop = "Prestashop";
        }elseif(substr_count($lHtml, "zenshop")>0){
            $shop = "Zenshop";
        }elseif(substr_count($lHtml, "magento")>0){
            $shop = "Magento";
        }elseif(substr_count($lHtml, "woocommerce")>0){
            $shop = "WooCommerce";
        }elseif(substr_count($lHtml, "opencart")>0){
            $shop = "Opencart";
        }elseif(substr_count($lHtml, "shopify")>0){
            $shop = "Shopify";
        }elseif(substr_count($lHtml, "virtuemart")>0){
            $shop = "Virtuemart";
        }elseif(substr_count($lHtml, "seotoaster")>0){
            $shop = "SeoToaster";
        }elseif(substr_count($lHtml, "epages")>0){
            $shop = "ePages";
        }
        return $shop;
    }
    private static function hasCarrito($html, $txt){
        $hasCarrito = 0;
        $lHtml = strtolower($html);
        $lTxt = strtolower($txt);
        if(substr_count($lHtml, "carrito")>0){
            $hasCarrito = 1;
        }
        if(substr_count($lTxt, "carrito")>0){
            $hasCarrito = 2;
        }
        return $hasCarrito;
    }
    private static function calculateResponsive($html){
        $responsiveLevel = 0;
        $lHtml = strtolower($html);
        if(strpos($lHtml, "viewport") !== false){
            $responsiveLevel = 1;
            if(strpos($lHtml, "device-width") !== false){
                $responsiveLevel = 2;
                if(strpos($lHtml, "initial-scale") !== false){
                    $responsiveLevel = 3;
                }
            }
        }
        return $responsiveLevel;
    }
    private static function extractParking($html){
        $parking = '';

        return $parking;
    }
    private static function getEmails($txt){
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        preg_match_all($pattern, $txt, $matches);
        return $matches[0];
    }
    public static function getWords($txt){
        $allWords = explode(' ', $txt);
        $uWords = [];
        $words = [];
        foreach($allWords as $word){
            $cWord = self::cleanWord($word);
            if($cWord !== '' && self::isStopWord($cWord) == false && strlen($cWord)<15  && strlen($cWord) > 4){
                if(isset($uWords[$cWord])){
                    $uWords[$cWord] = $uWords[$cWord] + 1;
                }else{
                    $uWords[$cWord] = 1;
                }
            }
        }
        arsort($uWords);
        $cnt = 0;
        foreach($uWords as $word=>$count){
            $words[]=$word;
            $cnt++;
            if($cnt == 5){
                break;
            }
        }
        return $words;
    }
    private static function cleanWord($word){
        $clean = "";
        $allowed = "abcdefghijklmnñopqrstuvwxyzáéíóúü";
        $word = strtolower($word);
        for($i=0;$i<strlen($word);$i++){
            $let = substr($word, $i, 1);
            if(strpos($allowed, $let) !== false){
                $clean .= $let;
            }
        }
        //$clean = utf8_decode($clean);
        try{
            $clean = iconv(mb_detect_encoding($clean, mb_detect_order(), true), "UTF-8", $clean);
        }catch(\Exception $e) {
            $clean = "";
        }
        return $clean;
    }

    private static function isStopWord($word){
        $stopWords = ['a','acá','aca','ahí','ahi','al','algo','algún','algun',
            'alguna','alguno','algunas','algunos','allá','alla','allí','alli',
            'ambos','ante','antes','aquel','aquella','aquello','aquellas','aquellos',
            'aquí','aqui','arriba','así','asi','atrás','atras','aun',
            'aunque','bien','cada','casi','como','con','cual','cuales',
            'cualquier','cualquiera','cuan','cuando','cuanto','cuantos','cuanta',
            'cuantas','de','del','demás','demas','desde','donde','dónde',
            'dos','el','él','ella','ellas','ello','ellos','en',
            'eres','esa','esas','ese','esos','esta','estás','estos',
            'este','etc','ha','hasta','la','las','lo','los',
            'me','mi','mis','mía','mías','mia','mias','mientras',
            'muy','ni','nosotras','nosotros','nuestra','nuestro','nuestras','nuestros',
            'os','otra','otras','otro','otros','para','por','pero',
            'pues','que','qué','si','sí','siempre','siendo','sin',
            'sino','so','sobre','sr','sra','sres','sta','su',
            'sus','te','tu','tus','un','una','uno','unas',
            'unos','usted','ustedes','vosotras','vosotros','vuestra','vuestro','vuestras',
            'vuestros','y','ya','yo',
            'don','es','no','todos','se','más','cookie','cookies'];
        return in_array($word, $stopWords);
    }
}