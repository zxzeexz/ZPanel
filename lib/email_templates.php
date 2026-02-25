<?php
/**
 * lib/email_templates.php
 * ZPanel Centralized HTML templates for emails
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */

/**
 * Template 1 (registration verification email)
 * @param string $username        The username of the account
 * @param string $activationLink  The full verification URL
 * @param array  $config          Global configuration (for site name, url, etc.)
 * @return string HTML content for email
 */
function getVerifyEmailTemplate(string $username, string $activationLink, array $config): string
{
    $siteName = $config['site']['name'] ?? 'ZPanel';
    $siteUrl  = $config['site']['url'] ?? 'http://localhost/';

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification - {$siteName}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #f0f0f0;
            padding: 20px;
        }
        .container {
            background-color: #1e1e1e;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
			color: #bbbbbb;
        }
        h2 {
            color: #4caf50;
        }
        a.button {
            display: inline-block;
            background-color: #4caf50;
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        a.button:hover {
            background-color: #43a047;
        }
        .footer {
            font-size: 12px;
            margin-top: 20px;
            color: #bbbbbb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to {$siteName}, {$username}!</h2>
        <p>Thank you for registering an account. Before you can log in, you need to verify your email address.</p>
        <p style="text-align: center;">
            <a href="{$activationLink}" class="button">Verify My Account</a>
        </p>
        <p>If the button above does not work, copy and paste this link into your browser:</p>
        <p><a href="{$activationLink}" style="color: #4caf50;">{$activationLink}</a></p>
        <div class="footer">
            <p>This email was sent automatically by {$siteName}.<br>
            If you did not sign up for an account, please ignore this message.</p>
            <p><a href="{$siteUrl}" style="color: #4caf50;">Visit {$siteName}</a></p>
        </div>
    </div>
</body>
</html>
HTML;
}
/**
 * Template 2 (password reset link email)
 * Generates the HTML body for password reset email.
 * Styled similarly to getVerifyEmailTemplate (responsive, inline CSS, Bootstrap-inspired look).
 *
 * @param string $username   The user's username
 * @param string $resetLink  The full password reset URL
 * @param array  $config     Site config array (for site name, URL, colors, etc.)
 * @return string            HTML email body
 */
function getResetEmailTemplate(string $username, string $resetLink, array $config): string
{
	$siteName = $config['site']['name'] ?? 'ZPanel';
    $siteUrl  = $config['site']['url'] ?? 'http://localhost/';
	
	return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset - {$siteName}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #f0f0f0;
            padding: 20px;
        }
        .container {
            background-color: #1e1e1e;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
			color: #bbbbbb;
        }
        h2 {
            color: #4caf50;
        }
        a.button {
            display: inline-block;
            background-color: #4caf50;
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        a.button:hover {
            background-color: #43a047;
        }
        .footer {
            font-size: 12px;
            margin-top: 20px;
            color: #bbbbbb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {$username},</h2>
        <p>We received a request to reset your password. Click the button below to set a new one:</p>
        <p style="text-align: center;">
			<a href="{$resetLink}" class="button">Reset My Password</a>
        </p>
        <p>If the button above does not work, copy and paste this link into your browser:</p>
        <p><a href="{$resetLink}" style="color: #4caf50;">{$resetLink}</a></p>
        <div class="footer">
            <p>This email was sent automatically by {$siteName}.<br>
            If you did not request to reset your password, please ignore this message.</p>
            <p><a href="{$siteUrl}" style="color: #4caf50;">Visit {$siteName}</a></p>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Template 3 (email change verification email)
 * @param string $username        The username
 * @param string $new_email       The new email
 * @param string $verifyLink      The verification URL
 * @param array  $config          Global config
 * @return string HTML email body
 */
function getChangeEmailTemplate(string $username, string $new_email, string $verifyLink, array $config): string
{
    $siteName = $config['site']['name'] ?? 'ZPanel';
    $siteUrl  = $config['site']['url'] ?? 'http://localhost/';
    $expiryHours = round(($config['email_change']['expiry'] ?? 3600) / 3600);

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Change Verification - {$siteName}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: #f0f0f0; padding: 20px; }
        .container { background-color: #1e1e1e; border-radius: 8px; padding: 20px; max-width: 600px; margin: auto; color: #bbbbbb; }
        h2 { color: #4caf50; }
        a.button { display: inline-block; background-color: #4caf50; color: #ffffff !important; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; }
        a.button:hover { background-color: #43a047; }
        .footer { font-size: 12px; margin-top: 20px; color: #bbbbbb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {$username},</h2>
        <p>You requested to change your email to {$new_email}.</p>
        <p>Click the button below to verify. This link expires in {$expiryHours} hours.</p>
        <p style="text-align: center;"><a href="{$verifyLink}" class="button">Verify Email Change</a></p>
        <p>If the button doesn't work: <a href="{$verifyLink}" style="color: #4caf50;">{$verifyLink}</a></p>
        <div class="footer">
            <p>Sent by {$siteName}. Ignore if not requested.</p>
            <p><a href="{$siteUrl}" style="color: #4caf50;">Visit {$siteName}</a></p>
        </div>
    </div>
</body>
</html>
HTML;
}