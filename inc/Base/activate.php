<?php
/**
 * @package DomitaiPlugin
 */
namespace Domitai\Base;
 class Activate{

    public static function activate(){
        $rootPath = dirname(dirname(get_template_directory()));
        $templateUri = get_template_directory()."/checkout";
        $fileName = $templateUri."/thankyou.php";
        if(!is_dir($templateUri)) mkdir($templateUri,0777,true);
        copy(dirname(__FILE__)."/thankyou.php",$fileName);
        $templateLanguages = $rootPath."/languages/plugins";
        $filesLanguages = glob(dirname(plugin_dir_path(__DIR__))."/languages/*",GLOB_MARK);
        foreach($filesLanguages as $file):
            $archivo = explode("/",$file);
            $totalArray = count($archivo);
            if($archivo[$totalArray-1]!="woocommerce-pay-plugin.pot")copy($file,$templateLanguages.'/'.$archivo[$totalArray-1]);
        endforeach;
        flush_rewrite_rules();
    }
 }