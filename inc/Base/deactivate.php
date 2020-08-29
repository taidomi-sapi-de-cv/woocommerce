<?php
/**
 * @package DomitaiPlugin
 */
namespace Inc\Base;
class Deactivate{

    public static function deactive(){
        $carpetaRaiz = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        
        $templateUri = get_template_directory();
        $dirpath = $templateUri."/checkout"; 
        if(is_dir($dirpath)){
            $files = glob($dirpath.'/*',GLOB_MARK);
            $images = glob($carpetaRaiz.'/imgs/*',GLOB_MARK);
            foreach($images as $image):
                unlink($image);
            endforeach;
            foreach($files as $file):
                unlink($file);
            endforeach;
            rmdir($carpetaRaiz.'/imgs');
            rmdir($dirpath);
        }
        /*$templateLanguages = $carpetaRaiz."/wp-content/languages/plugins";
        $filesLanguages = glob(dirname(dirname(dirname(__FILE__)))."/languages/*",GLOB_MARK);
        foreach($filesLanguages as $file):
            $archivo = explode("/",$file);
            $totalArray = count($archivo);
            if($archivo[$totalArray-1]!="woocommerce-pay-plugin.pot") unlink($templateLanguages.'/'.$archivo[$totalArray-1]);
        endforeach;*/
        flush_rewrite_rules();
    }
}