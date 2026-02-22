<?php
/**
 * config.php
 * ZPanel global configuration
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
$siteUrl = 'http://47.129.100.170';					//Specify [protocol (http or https)]://[hostname]:[port] under where ZPanel runs.
return [
    // -----------------------------
    // Site Settings
    // -----------------------------
    'site' => [
        'name'       => 'ZPanel',					//Page title accross entire site
        'url'        => $siteUrl,					//Recommended not to touch this (unless you know what youre doing.)
        'root_path'  => '/cpanel',						//Base web root on which your application lies
    ],
    // -----------------------------
    // Debug Mode
    // -----------------------------
    'debug' => true,								//Toggle error reporting

    // -----------------------------
    // Database Settings - supports only MySQL/MariaDB.
    // -----------------------------
    'db' => [
        'host'     => 'localhost',
        'port'     => '3306',
        'name'     => 'c2ro_main',
        'user'     => 'root',
        'pass'     => '837829318',
        'charset'  => 'utf8mb4',
    ],

    // -----------------------------
    // Security Settings
    // -----------------------------
    'security' => [
        'hash_method'        => 'plain',   		//Toggle password hashing [md5 | plain | bcrypt]
        'csrf_protection'    => true,			//Toggle use of CSRF protection.
    ],

    // -----------------------------
    // Theme settings
    // -----------------------------
    'theme' => [
        'default' => 'default',					//Create a new folder inside themes and change it like so : 'default' => 'mynewtheme'
    ],

    // -----------------------------
    // Registration Options
    // -----------------------------
    'registration' => [
        'max_accounts_per_ip' => 3, 			//Set to 0 to allow unlimited account creation under same IP
		'max_accounts_per_device' => 3, 		//New: Set to 0 to allow unlimited per device fingerprint
        'email_verification'  => false,			//Set to false to skip email verification
    ],

    // -----------------------------
    // Email / PHPMailer Settings - Only if you enable email_verification
    // -----------------------------
    'mail' => [
        'host'       => 'smtp.example.com',
        'port'       => 587,
        'username'   => 'user',
        'password'   => 'pass',
        'from_email' => 'noreply@example.com',
        'from_name'  => 'ZPanel No-reply',
        'reply_email'=> 'support@example.com',	//Fallback for replies (probably avoids the email being marked as spam)
        'reply_name' => 'ZPanel Support',
        'hostname'   => 'example.com',			//Must match your domain DNS
        'debug'      => 0,						//Toggle emailer debug 0=off, 2=verbose debug
		//email subjects section
		'verification_email' => 'YourRO - Activate your account',
    ],
	// -----------------------------
	// Unstuck Feature (Account and player dashboard)
	// -----------------------------
	'unstuck' => [
		'enabled' => true,						//Toggle feature on/off
		'town'    => 'prontera',				//Map ID
		'x'       => 150,
		'y'       => 150,
	],

	//messages
	'msg' => [
		//misc
		'form_csrferror' => 'CSRF validation failed. Please reload the page and try again.',
		//login module
		//errors
		'login_nullusrpw' => 'Please enter both username and password.',
		'login_wrongpass' => 'Invalid password.',
		'login_noaccount' => 'Account not found.',
		//confirmations
		'login_loggedout' => 'Logged out successfully.',
		//charview module
		//errors
		'chview_invchid' => 'Invalid character ID.',
		'chview_xauthid' => 'Error accessing this page.',
		
		//actions
		//unstuck
		'action_unotfnd' => 'Character not found.',
		'action_unisonl' => 'Character is online. Please logout first and try again.',
		'action_unsucce' => 'Character unstuck successfull.',
		'action_unerror' => 'Failed to update character.',
		'action_undisab' => 'Unstuck feature is disabled.',
	],

];