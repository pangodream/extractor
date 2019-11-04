<?php
/**
 * Created by Pangodream.
 * User: Development
 * Date: 04/11/2019
 * Time: 17:10
 */

//Use composer autoload to load class files
require_once __DIR__ . "/../vendor/autoload.php";

use Extractor\Extractor;

$html = '<body><span style="font-weight: bolder;">Teléfono:</span><span>+34 911 876 543</span></body>';

$txt = Extractor::extractText($html);

echo "Texto extraido: ".$txt."\n";

$result = Extractor::analyze($html);

echo "Teléfono extraido: ".$result['telf']."\n";