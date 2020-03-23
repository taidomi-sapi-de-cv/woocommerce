<?php
/**
 * @package DomitaiPlugin
 */
namespace Inc\Base;
class Deactivate{

    public static function deactive(){
        $carpetaRaiz = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        
        $templateUri = get_template_directory();
        $dirpath =$templateUri."/checkout"; 
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
        flush_rewrite_rules();
    }
}