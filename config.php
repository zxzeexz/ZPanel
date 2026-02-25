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
        'user'     => 'user',
        'pass'     => 'pass',
        'charset'  => 'utf8mb4',
    ],

    // -----------------------------
    // Security Settings
    // -----------------------------
    'security' => [
        'hash_method'        => 'plain',   		//Toggle password hashing [md5 | plain | bcrypt]
        'csrf_protection'    => true,			//Toggle use of CSRF protection.
	'max_logintime'      => 3600,			//Login session time (in seconds)
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
		'allowed_email_domains' => [            // List of allowed email domains for registration
            'gmail.com',
            'yahoo.com',
            'hotmail.com',
            'outlook.com',
            'icloud.com',
            'protonmail.com',
            'aol.com',
            'yandex.com',
            'mail.com',
            'inbox.com',
            'zoho.com',
            'gmx.com',
            // Add more as needed
        ],
    ],

    // -----------------------------
    // Email / PHPMailer Settings - Only if you enable email_verification
    // -----------------------------
    'mail' => [
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'username'   => 'user',
        'password'   => 'pass',
        'from_email' => 'email@gmail.com',
        'from_name'  => 'Project Baldur',
        'reply_email'=> 'email@gmail.com',	//Fallback for replies (probably avoids the email being marked as spam)
        'reply_name' => 'Project Baldur',
        'hostname'   => 'example.com',		//Must match your domain DNS
        'debug'      => 0,						//Toggle emailer debug 0=off, 2=verbose debug
		//email subjects section
		'verification_email' => 'YourRO - Activate your account',
		'passwrdreset_email' => 'YourRO - Password Reset',
		'account_change_email' => 'YourRO - Verify Email Change',
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
	
	// -----------------------------
	// Password Reset Options
	// -----------------------------
	'pwreset' => [
		'enabled'    => true,    // Toggle feature on/off
		'expiry'     => 36000,    // Reset link expiry time (in seconds, e.g., 10 hours)
	],
	
	// -----------------------------
    // Email Change Options
    // -----------------------------
    'email_change' => [
        'enabled' => true,    // Toggle feature on/off (default false)
        'expiry'  => 86400,     // Link expiry time (in seconds)
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
		'login_seexpired' => 'Logged out due to session expired.',
		//confirmations
		'login_loggedout' => 'Logged out successfully.',
		//charview module
		//errors
		'chview_invchid' => 'Invalid character ID.',
		'chview_xauthid' => 'Error accessing this page.',
		//dashboard module
		'dashbo_nochars' => 'You do not have any characters yet.',
		//register module
		//errors
		'regist_nullfld' => 'All fields are required.',
		'regist_verpass' => 'Passwords do not match.',
		'regist_invasex' => 'Invalid sex selection.',
		'regist_invemai' => 'Invalid email address.',
		'regist_bdformt' => 'Invalid birthdate format. Use YYYY-MM-DD.',
		'regist_xdomain' => 'Email domain not allowed. Please use a supported email provider.',
		'regist_dupuser' => 'Username already taken.',
		'regist_dupmail' => 'Email already registered.',
		'regist_aclimit' => 'Maximum number of allowed registrations reached.',
		'regist_dberror' => 'Failed to create account. Please try again later.',
		//confirmations
		'regist_success' => 'Account created! Please check your email for the verification link.',
		'regist_succes2' => 'Account created successfully. You may now log in.',
		'regist_sucxeml' => 'Account created, but failed to send verification email. Contact admin.',
		//register module -- verification
		'regis2_donever' => 'This account has already been verified. You may log in.',
		'regis2_verfied' => 'Your account has been verified! You can now log in.',
		'regis2_dberror' => 'Failed to verify account due to a server error. Please contact admin.',
		'regis2_vrerror' => 'Verification failed. The link is invalid or the account does not exist.',
		'regis2_badlink' => 'Invalid verification link.',
		//resend verification link module
		//errors
		'reseve_disable' => 'Email verification is currently disabled. This page is not available.',
		'reseve_nullchk' => 'Please enter your username or email.',
		'reseve_noaccnt' => 'No pending verification found under this username or email.',
		'reseve_nopendi' => 'This account is already verified. You can log in.',
		'reseve_novcode' => 'No activation code under this account. Please contact admin.',
		'reseve_notsent' => 'Failed to send verification email. Please contact admin.',
		//confirmations
		'reseve_success' => 'Verification email has been re-sent. Please make sure to check your spam/junk mail.',
		//settings module
		//errors
		'settng_inpmism' => 'New passwords do not match.', 
		'settng_inpulen' => 'New password must be at least 6 characters.',
		'settng_xcurpas' => 'Current password is incorrect.',
		//confirmations
		'settng_success' => 'Password updated successfully.',
		// pwreset module
		// errors
		'pwres_disable' => 'Password reset is currently disabled.',
		'pwres_nullinp' => 'Please enter your username or email.',
		'pwres_noaccnt' => 'No account found with that username or email.',
		'pwres_notsent' => 'Failed to send reset email. Please contact admin.',
		'pwres_badlink' => 'Invalid or expired reset link.',
		'pwres_inpmism' => 'New passwords do not match.',
		'pwres_inpulen' => 'New password must be at least 6 characters.',
		'pwres_dberror' => 'Failed to fetch or update account data. Please contact admin',
		// confirmations
		'pwres_reqsent' => 'Password reset link sent to your email. Please make sure to check your spam/junk mail.',
		'pwres_success' => 'Password reset successfully. You can now log in.',
		//settings module
		//email change feature
        //errors
        'settng_email_invalid' => 'Invalid email address.', 
        'settng_email_same' => 'New email must be different from current.', 
        'settng_email_exists' => 'This email is already in use.', 
        'settng_email_db_error' => 'Failed to process email change request. Try again later.', 
        'settng_email_disabled' => 'Email change feature is disabled.', 
        'settng_email_badlink' => 'Invalid or expired email change link.', 
        //confirmations
        'settng_email_sent' => 'A verification email has been sent to your new email address. Please check your inbox and spam/junk folder.', 
        'settng_email_changed' => 'Your email has been changed successfully!',
		
		//actions
		//unstuck
		'action_unotfnd' => 'Character not found.',
		'action_unisonl' => 'Character is online. Please logout first and try again.',
		'action_unsucce' => 'Character unstuck successfull.',
		'action_unerror' => 'Failed to update character.',
		'action_undisab' => 'Unstuck feature is disabled.',
	],

];