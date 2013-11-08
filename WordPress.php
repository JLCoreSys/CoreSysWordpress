<?php

namespace wordpress\WordPress;

class WordPress
{
    private $folder;

    public function __construct()
    {
        $this->folder = dirname( __FILE__ );
        echo $this->folder;
    }

    public function getFolder( $sub_folder = null )
    {
        if( !empty( $sub_folder ) ) {
            if( substr( $sub_folder, 0, 1 ) == DIRECTORY_SEPARATOR ) {
                return $this->folder . $sub_folder;
            }
            return $this->folder . DIRECTORY_SEPARATOR . $sub_folder;
        }
        return $this->folder;
    }
}