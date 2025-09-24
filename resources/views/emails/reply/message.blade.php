@component('emails.layouts.message')
    # {{ __('Hello dear :user', ['user' => $msg->name]) }} <br>

    <p style="font-size: 16px; margin-bottom: 20px;">
        <p>
           {!! $msg->reply  !!}
        </p>
    </p>
@endcomponent
