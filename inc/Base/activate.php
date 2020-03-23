<?php
/**
 * @package DomitaiPlugin
 */
namespace Inc\Base;
 class Activate{

    public static function activate(){
        $carpetaRaiz = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        
        $templateUri = get_template_directory()."/checkout";
        $fileName = $templateUri."/thankyou.php";
        if(!is_dir($templateUri)) mkdir($templateUri,0777,true);
        copy(dirname(__FILE__)."/thankyou.php",$fileName);
        $templateUriImages = $carpetaRaiz."/imgs";
        if(!is_dir($templateUriImages)) mkdir($templateUriImages,0777,true);
        
        $files = glob(dirname(__FILE__)."/icons/*",GLOB_MARK);
        foreach($files as $file):
            $archivo = explode("/",$file);
            $totalArray = count($archivo);
            copy($file,$templateUriImages.'/'.$archivo[$totalArray-1]);
        endforeach;
        flush_rewrite_rules();
    }
 }