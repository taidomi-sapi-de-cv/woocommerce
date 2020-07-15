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

        $templateLanguages = $carpetaRaiz."/wp-content/languages/plugins";
        if(!is_dir($templateLanguages)) mkdir($templateUriImages,0777,true);
        $filesLanguages = glob(dirname(dirname(dirname(__FILE__)))."/languages/*",GLOB_MARK);
        
        foreach($filesLanguages as $file):
            $archivo = explode("/",$file);
            $totalArray = count($archivo);
            if($archivo[$totalArray-1]!="woocommerce-pay-plugin.pot")copy($file,$templateLanguages.'/'.$archivo[$totalArray-1]);
        endforeach;
        flush_rewrite_rules();
    }
 }