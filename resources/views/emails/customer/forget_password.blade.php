@component('emails.layouts.general')
    <p>
        # {{ __('Hello dear :user !', ['user' => $user->name]) }}
    </p>

    <div style="text-align: center; margin-top: 20px; font-size: 20px;">
        <!-- Message otp code -->
        {{ __('Enter the following code to reset your password') }}:

        <div style="margin-top: 20px; font-size: 30px; font-weight: bold;">
            {{ $otp }}
        </div>
    </div>
@endcomponent
