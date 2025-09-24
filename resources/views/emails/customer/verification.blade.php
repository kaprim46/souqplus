@component('emails.layouts.general')
    <p>
        # {{ __('Hello dear :user !', ['user' => $user->name]) }}
    </p>

    <div style="text-align: center; margin-top: 20px; font-size: 20px;">
        <!-- Message otp code -->
        {{ __('Your verification code is :code', ['code' => $user->otp_code]) }}
    </div>
@endcomponent
