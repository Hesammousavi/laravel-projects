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

    'code_sent_successfully' => 'کد با موفقیت ارسال شد',
    'code_sending_failed' => 'ارسال کد ناموفق بود',
    'contact_not_belongs_to_authenticated_user' => 'اطلاعات تماس انتخاب شده متعلق به کاربر احراز هویت شده نیست.',
    'user_not_found' => 'کاربر یافت نشد.',
    'contact_verified_successfully' => 'اطلاعات تماس با موفقیت تایید شد.',

    // Action-specific messages
    'register' => [
        'title' => 'تکمیل ثبت نام',
        'message' => 'لطفاً کد تایید ارسال شده به اطلاعات تماس شما را وارد کنید تا ثبت نام شما تکمیل شود.',
        'success' => 'ثبت نام با موفقیت تکمیل شد! اکنون می‌توانید وارد حساب کاربری خود شوید.',
    ],
    'login' => [
        'title' => 'تایید ورود',
        'message' => 'لطفاً کد تایید ارسال شده به اطلاعات تماس شما را وارد کنید تا ورود شما تکمیل شود.',
        'success' => 'ورود موفقیت‌آمیز بود! خوش آمدید.',
    ],

    // Email template messages
    'email' => [
        'subject' => 'کد تایید شما',
        'greeting' => 'سلام!',
        'intro' => 'شما درخواست کد تایید برای حساب کاربری خود کرده‌اید.',
        'code_label' => 'کد تایید شما:',
        'closing' => 'با احترام،',
        'team' => 'تیم :app_name',
        'footer_notice' => 'این یک پیام خودکار است، لطفاً به این ایمیل پاسخ ندهید.',
    ],
];
