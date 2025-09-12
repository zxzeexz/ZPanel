<?php
/**
 * config.php
 * ZPanel global configuration
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
$siteUrl = 'http://localhost';					//Specify [protocol (http or https)]://[hostname]:[port] under where ZPanel runs.
return [
    // -----------------------------
    // Site Settings
    // -----------------------------
    'site' => [
        'name'       => 'ZPanel',					//Page title accross entire site
        'url'        => $siteUrl,					//Recommended not to touch this (unless you know what youre doing.)
        'root_path'  => '/cp',						//Base web root on which your application lies
    ],
    // -----------------------------
    // Debug Mode
    // -----------------------------
    'debug' => false,								//Toggle error reporting

    // -----------------------------
    // Database Settings - supports only MySQL/MariaDB.
    // -----------------------------
    'db' => [
        'host'     => 'localhost',
        'port'     => '3306',
        'name'     => 'zro_main',
        'user'     => 'root',
        'pass'     => '123456',
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
];
