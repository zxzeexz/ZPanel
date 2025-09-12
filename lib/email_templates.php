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
        <p>If the button above doesnâ€™t work, copy and paste this link into your browser:</p>
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
