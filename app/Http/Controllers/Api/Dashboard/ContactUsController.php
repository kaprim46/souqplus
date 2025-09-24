<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Requests\Dashboard\ContactUsRequest;
use App\Models\Category;
use App\Models\ContactUs;
use App\Notifications\ContactUsReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactUsController
{

    CONST MAX_PER_PAGE = 15;

    /**
     * Get all messages
     * @param ContactUsRequest $request
     * @return JsonResponse
     */
    public function index(ContactUsRequest $request): JsonResponse
    {
        try {
            $messages = ContactUs::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->paginate(self::MAX_PER_PAGE);

            return response()->json([
                'ok'            => true,
                'messages'     => $messages
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Show category details
     * @param ContactUs $contactUs
     * @return JsonResponse
     * @throws Throwable
     */
    public function show(ContactUs $contactUs): JsonResponse
    {
        if (!$contactUs->is_read)
        {
            $contactUs->update([
                'is_read' => true
            ]);
        }

        try {
            return response()->json([
                'ok'            => true,
                'contactUs'      => $contactUs
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }


    /*
     * Reply to message
     * @param ContactUs $contactUs
     * @param ContactUsRequest $request
     * @return JsonResponse
     */
    public function reply(ContactUs $contactUs, ContactUsRequest $request): JsonResponse
    {
        try {
            $contactUs->update([
                'is_replied' => true,
                'reply' => $request->get('reply')
            ]);


            /**
             * Refresh the model
             */
            $contactUs->refresh();

            /**
             * Send email notification
             */
            $contactUsNotification  = new ContactUsReply($contactUs);

            $contactUs->notify($contactUsNotification);

            return response()->json([
                'ok'        => true,
                'contactUs' => $contactUs->refresh(),
                'message'   => __('Message replied successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'error'     => $e->getMessage()
            ]);
        }
    }


    /**
     * Delete message
     * @param ContactUs $contactUs
     * @return JsonResponse
     */
    public function destroy(ContactUs $contactUs): JsonResponse
    {
        try {
            $contactUs->delete();

            return response()->json([
                'ok'        => true,
                'message'   => 'Message deleted successfully'
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok' => false,
                'message' => 'Something went wrong, please try again later'
            ]);
        }
    }
}
