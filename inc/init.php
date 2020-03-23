<?php 
/**
 * @package WoocommercePlugin
 */
namespace Inc;

class Init{

    public static function get_services(){
        return [
            Base\DomitaiApi::class,
            Base\Activate::class,
            Base\Deactivate::class    
        ];
    }
    public static function register_services(){
        foreach( self::get_services() as $class):
            $service = self::instantiate( $class );
            if ( method_exists( $service,'register')){
                $service->register();
            }
        endforeach;
    }

    private static function instantiate( $class ){
        $services = new $class;
        return $services;
    }
}