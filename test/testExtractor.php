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

$html = '/* <![CDATA[ */
var wc_add_to_cart_params = {"ajax_url":"\/wp-admin\/admin-ajax.php","wc_ajax_url":"\/contacto\/?wc-ajax=%%endpoint%%","i18n_view_cart":"Ver carraito","cart_url":"https:\/\/farmaciabeatrizcastellanos.es\/?page_id=13625","is_cart":"","cart_redirect_after_add":"no"};
/* ]]> */<body><span style="font-weight: bolder;">Teléfono:</span><span>+34 911 876 543</span><p>Ir a mi Carrito</p></body>';

$txt = Extractor::extractText($html);

echo "Texto extraido: ".$txt."\n";

$result = Extractor::analyze($html);

echo "Teléfono extraido: ".$result['telf']."\n";
echo "Carrito: ".$result['carrito']."\n";