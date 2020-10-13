<?php

namespace app\Core;

class Session{

    private static $switch = false;

    public static function turn_on_session(){
        session_start();
        self::$switch = true;
    }

    public static function destroy_session(){
        if(self::$switch == true){
            session_unset();
            echo "obrisano";
        }
    }

    public static function set_id($id){
        if(!self::$switch){
            self::$switch = true;

            self::turn_on_session();
        }
    }
}