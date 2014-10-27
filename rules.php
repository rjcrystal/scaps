<?php
$ign = array
(
	'T_BAD_CHARACTER',
	'T_DOC_COMMENT',
	'T_COMMENT',
	'T_WHITESPACE',
	'T_OPEN_TAG',
	'T_CLOSE_TAG'
); //which elements to ignore  
$sqli= array (
'mysql_query',
'mysqli_query'
);// exploitable functions which can be
$xss=array 
(
'echo',
'print'
);
$temp =array 
(
	
);
$cmdexec= array  
(
	'exec',
	'passthru',
	'system',
	'shell_exec',
	'`',
	'popen',
	'proc_open',
	'pcntl_exec',
);//these functions are used to create processes execute commands of system
$codeexec=array
(
	'eval',
	'assert'
);//functions which can execute php code supplied in arguments
$taintable=array 
(
	'$_POST',
	'$_GET',
	'$_SESSION',
	'T_FUNCTION'
);//defines which elements to look for in a code
$ctrlr= array (
	'T_IF', 
	'T_SWITCH',
	'T_ELSEIF',
);// used to define flow or loop controlling elements  
$lrfi= array (
		'T_INCLUDE',
		'T_INCLUDE_ONCE',
		'T_REQUIRE',
		'T_REQUIRE_ONCE'
);// used to detect if files are connected /included and these functions are responsible for lfi and rfi
$clbkfunc=array 
(
'ob_start'                   ,
'array_diff_uassoc'          ,
'array_diff_ukey'            ,
'array_filter'               ,
'array_intersect_uassoc'     ,
'array_intersect_ukey'       ,
'array_map'                  ,
'array_reduce'               ,
'array_udiff_assoc'          ,
'array_udiff_uassoc'         ,
'array_udiff'                ,
'array_uintersect_assoc'     ,
'array_uintersect_uassoc'    ,
'array_uintersect'           ,
'array_walk_recursive'       ,
'array_walk'                 ,
'assert_options'             ,
'uasort'                     ,
'uksort'                     ,
'usort'                      ,
'preg_replace_callback'      ,
'spl_autoload_register'      ,
'iterator_apply'             ,
'call_user_func'             ,
'call_user_func_array'       ,
'register_shutdown_function' ,
'register_tick_function'     ,
'set_error_handler'          ,
'set_exception_handler'      ,
'session_set_save_handler'   ,
'sqlite_create_aggregate'    ,
'sqlite_create_function'
);

$funcstats= array (

);
// stats about functions that are analysed by the tna function 
$list = array ();
$warnfunc = array();
?>