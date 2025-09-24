@component('emails.layouts.ad')
    # {{ __('Hello dear :user', ['user' => $ad->user->name]) }} {{ __('Your ad status has changed.') }} <br>

    <p style="font-size: 16px; margin-bottom: 20px;">
        <!-- Write message for approved ad -->
        {{ __('Your ad has been approved and is now visible to everyone.') }} <br>
        <p>
            {{ __('You can view your ad by clicking the link below.') }} <br>
            <a href='{{ url("advertisement/{$ad->id}") }}' style="
                color: #fff;
                background-color: #007bff;
                display: inline-block;
                font-weight: 400;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                border: 1px solid transparent;
                padding: 0.375rem 0.75rem;
                font-size: 1rem;
                line-height: 1.5;
                border-radius: 0.25rem;
                text-decoration: none;
                margin-top: 15px;
             ">
                {{ __('View Ad') }}
            </a>
        </p>
    </p>
@endcomponent
