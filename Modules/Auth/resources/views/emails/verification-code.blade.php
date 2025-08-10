<x-mail::message>
# {{ __('auth::verification.email.subject') }}

{{ __('auth::verification.email.greeting') }}

{{ __('auth::verification.email.intro') }}

**{{ __('auth::verification.email.code_label') }}**

<div style="text-align: center; padding: 20px; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #495057;">
{{ $code }}
</div>

{{ __('auth::verification.email.closing') }}<br>
{{ __('auth::verification.email.team', ['app_name' => config('app.name')]) }}

---

{{ __('auth::verification.email.footer_notice') }}
</x-mail::message>
