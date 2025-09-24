<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\FileServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MediaController extends Controller
{
    CONST MAX_PER_PAGE = 10;

    public function __construct(protected FileServices $fileServices)
    {
        $this->fileServices = $fileServices;
    }

    /**
     * Get media
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $media = File::query()
                ->where('user_id', auth()->id())
                ->paginate(self::MAX_PER_PAGE);

            return response()->json([
                'ok'    => true,
                'media' => $media
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok' => false,
                'message' => 'Something went wrong, please try again later'
            ]);
        }
    }

    /**
     * Upload media
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240',
                'path' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'ok' => false,
                    'message' => $validator->errors()
                ]);
            }

            $response = $this->fileServices->upload($request->file('file'), $request->get('path'), false, true, 350, 350);

            if($response['ok']) {
                $file = File::create($response['data']);

                return response()->json([
                    'ok'        => true,
                    'message'   => __('File uploaded successfully'),
                    'data'      => $file
                ]);
            }

            return response()->json([
                'ok'        => false,
                'message'   => $response['message']
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete media
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $media = File::query()->findOrFail($id);

            if($media->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
                return response()->json([
                    'ok' => false,
                    'message' => __('You are not authorized to delete this file')
                ]);
            }

            if($media->delete()) {
                return response()->json([
                    'ok'        => true,
                    'message'   => __('File deleted successfully',)
                ]);
            }

            return response()->json([
                'ok' => false,
                'message' => __('Unable to delete the file')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later')
            ]);
        }
    }
}
