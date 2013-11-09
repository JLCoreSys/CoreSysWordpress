<?php
require( 'wp-config.php' );

'$s_headers = apache_request_headers();',
'$s_header = isset( $s_headers[ 'From-Symfony'] ) ? $s_headers[ 'From-Symfony' ] : null;',
'if( !empty( $s_header ) ) {',
'    $s_hash_match = false;',
'    for( $s_i = 0; $s_i <= 10; $s_i++ ) {',
'        $s_time = time() - $s_i;',
'        $s_hash = md5( htmlentities( LOGGED_IN_KEY . ':' . LOGGED_IN_SALT . ':' . $table_prefix . ':' . $s_time ) );',
'        if( $s_hash == $s_header ) { $s_hash_match = true; break; }',
'    }',
'    if( $s_hash_match ) {',
'        defined( 'SYMFONY_WP' ) || define( 'SYMFONY_WP', $s_hash );',
'    }',
'}'
