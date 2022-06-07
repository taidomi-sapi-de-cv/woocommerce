<?php
/**
 * @package DomitaiPlugin
 */
namespace Domitai\Base;
class Deactivate{

    public static function deactive(){
        $templateUri = get_template_directory();
        $dirpath =$templateUri."/checkout"; 
        if(is_dir($dirpath)){
            $files = glob($dirpath.'/*',GLOB_MARK);
            foreach($files as $file):
                unlink($file);
            endforeach;
            rmdir($dirpath);
        }

        $templateLanguages = $rootPath."/languages/plugins";
        
        flush_rewrite_rules();
    }
}