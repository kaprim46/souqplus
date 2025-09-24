<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message'   => __('Invalid input'),
                'errors'    => $validation->errors()
            ], 422);
        }

        $data           = $request->only(['name', 'email', 'phone', 'subject', 'message']);
        $data['ip']     = $request->ip();

        $contactUs      = ContactUs::create($data);

        if(!$contactUs) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Failed to save contact us data')
            ], 201);
        }

        return response()->json([
            'ok'        => true,
            'message'   => __('Contact us data saved successfully'),
        ]);
    }
}
