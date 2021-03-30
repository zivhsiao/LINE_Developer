<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$hook['post_controller_constructor'] = array(
    'class'    => 'ManageAuth',    
    'function' => 'auth',    
    'filename' =>'ManageAuth.php',    
    'filepath' => 'hooks'
);

// $hook['display_override'][] = array(
// 	'class'  	=> 'Develbar',
//     'function' 	=> 'debug',
//     'filename' 	=> 'Develbar.php',
//     'filepath' 	=> 'third_party/DevelBar/hooks'
// );



/* End of file hooks.php */
/* Location: ./application/config/hooks.php */