@component('emails.layouts.general')
    <p>
        # {{ __('Hello dear :user the registration is successful', ['user' => $user->name]) }}
    </p>

    <div style="text-align: center; margin-top: 20px; font-size: 20px;">
        {{ __('You have successfully registered on our site. Now you can use all the features of the site.') }}
    </div>

    <a href="{{ url('/') }}" style="display: block; text-align: center; margin-top: 20px; font-size: 20px;">
        {{ __('Go to the site') }}
    </a>
@endcomponent
