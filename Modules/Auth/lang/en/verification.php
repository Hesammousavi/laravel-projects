<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Verification Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default messages used by
    | the verification controller for the Auth module.
    |
    */

    'code_sent_successfully' => 'Code sent successfully',
    'code_sending_failed' => 'Code sending failed',
    'contact_not_belongs_to_authenticated_user' => 'The selected contact does not belong to the authenticated user.',
    'user_not_found' => 'User not found.',
    'contact_verified_successfully' => 'Contact verified successfully.',

    // Action-specific messages
    'register' => [
        'title' => 'Complete Your Registration',
        'message' => 'Please enter the verification code sent to your contact to complete your registration.',
        'success' => 'Registration completed successfully! You can now log in to your account.',
    ],
    'login' => [
        'title' => 'Login Verification',
        'message' => 'Please enter the verification code sent to your contact to complete your login.',
        'success' => 'Login successful! Welcome back.',
    ],

    // Email template messages
    'email' => [
        'subject' => 'Your Verification Code',
        'greeting' => 'Hello!',
        'intro' => 'You have requested a verification code for your account.',
        'code_label' => 'Your verification code is:',
        'closing' => 'Best regards,',
        'team' => ':app_name Team',
        'footer_notice' => 'This is an automated message, please do not reply to this email.',
    ],
];
