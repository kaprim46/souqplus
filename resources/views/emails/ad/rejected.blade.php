@component('emails.layouts.ad')
    # {{ __('Hello dear :user', ['user' => $ad->user->name]) }} {{ __('Your ad status has changed.') }} <br>

    <p style="font-size: 16px; margin-bottom: 20px;">
        <!-- Write message for rejected ad -->
        {{ __('Your ad has been rejected and is not visible to everyone.') }} <br>

        <!-- Reason for rejection -->
        <p style="font-size: 16px; margin: 20px 0;">
            {{ __('Reason for rejection: ') }} {{ $ad->reject_reason }} <br>
        </p>
    </p>
@endcomponent
