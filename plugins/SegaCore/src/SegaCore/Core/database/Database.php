<?php

namespace SegaCore\Core\database;

use SegaCore\Core\Main;

class Database {

    public static function getDatabase(){
      return   Main::getInstance()->database;
    }

    public static function getDatabaseByPlugin(){
        return   Main::getInstance()->database2;
    }
}
