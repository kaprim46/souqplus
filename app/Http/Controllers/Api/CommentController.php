<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Jobs\NewCommentNotificationJob;
use App\Services\NotificationServices;
use Illuminate\Http\JsonResponse;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CommentController
{
    CONST MAX_PER_PAGE = 5;

    public function __construct(protected NotificationServices $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Index comments on advertisement
     * @param int $advertisement_id
     * @param Request $request
     * @return JsonResponse
     */
    public function indexComments(int $advertisement_id, Request $request): JsonResponse
    {
        $advertisement = Advertisement::query()
            ->when($request->user() && $request->user()->isAdmin(), function($q) {
                $q->withTrashed();
            })
            ->findOrFail($advertisement_id);

        try {
            $validator = Validator::make($request->all(), [
                'status'    => 'nullable|string|in:all,approved,pending,rejected'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'ok'        => false,
                    'message'   => __('Validation error'),
                    'errors'    => $validator->errors()
                ])->setStatusCode(422);
            }

            $comments = $advertisement->comments()
                ->when(!$request->user() || !$request->user()->isAdmin(), function($query){
                    $query->whereStatus('approved');
                })
                ->when($request->user() && $request->user()->isAdmin() && $request->get('status') && $request->get('status') !== 'all', function($query) use ($request){
                    $query->whereStatus($request->get('status'));
                })
                ->latest()
                ->paginate(self::MAX_PER_PAGE);

            /**
             * Map it
             */
            $comments->getCollection()->transform(function($item){
                return [
                    'id'        => $item->id,
                    'content'   => $item->content,
                    'name'      => $item->name,
                    'user'      => $item->user ? [
                    'id'            => $item->user->id,
                    'name'          => $item->user->role === 'business' ? (is_array($item->user->business_info) && isset($item->user->business_info['business_name']) ? $item->user->business_info['business_name'] : '--') : $item->user->name,
                    'avatar_url'    => $item->user->role === 'business' ? (is_array($item->user->business_info) && isset($item->user->business_info['business_logo']) ? asset($item->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$item->user->name}&color=7F9CF5&background=EBF4FF") : $item->user->avatar_url,
                    'country_code'  => $item->user->country_code,
                    'phone_number'  => $item->user->phone_number,
                    'is_business'   => $item->user->role === 'business',
                    'is_verified'   => $item->user->is_verified,
                    'slug'          => Str::slug($item->user->role === 'business' ? (is_array($item->user->business_info) && isset($item->user->business_info['business_name']) ? $item->user->business_info['business_name'] : '--') : $item->user->name),
                ] : [
                        'name' => $item->name ?? 'Anonymous',
                    ],
                    'created_at'=> $item->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'ok'        => true,
                'comments'  => $comments
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Store comment on advertisement
     * @param Advertisement $advertisement
     *  @param Request $request
     * @return JsonResponse
     */
    public function storeComment(Advertisement $advertisement, Request $request): JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'content'   => 'required|string',
            ]);

            if($validation->fails()) {
                return response()->json([
                    'ok'        => false,
                    'message'   => __('Validation error'),
                    'errors'    => $validation->errors()
                ])->setStatusCode(422);
            }

            $comment = $advertisement->comments()->create([
                'user_id'   => $request->user()?->id ?? null,
                'name'      => $request->user()?->name ?? $request->get('name'),
                'content'   => $request->get('content'),
                'status'    => 'approved'
            ]);

            if($request->user() && $request->user()?->id !== $advertisement->user_id) {
                NewCommentNotificationJob::dispatch($advertisement, $request->user())
                    ->onQueue('notifications')->afterResponse();
            }

            /**
             * Load user relation
             */
            $comment->load('user:id,name,is_verified,avatar');

            return response()->json([
                'ok'        => true,
                'message'   => __('Comment added successfully'),
                'comment'   => [
                    'id'        => $comment->id,
                    'content'   => $comment->content,
                    'name'      => $comment->name,
                    'user'      => $comment->user ? [
                        'id'        => $comment->user->id,
                        'name'      => $comment->user->name,
                        'is_verified'=> $comment->user->is_verified,
                        'avatar_url'    => $comment->user->avatar_url
                    ] : [
                        'name' => $comment->name ?? 'Anonymous',
                    ],
                    'created_at'=> $comment->created_at->diffForHumans()
                ]
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete comment on advertisement
     * @param Advertisement $advertisement
     * @param int $comment_id
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyComment(Advertisement $advertisement, int $comment_id, Request $request): JsonResponse
    {
        try {
            $comment = $advertisement->comments()->findOrFail($comment_id);

            if(
                !$request->user()->isAdmin() &&
                $comment->user_id !== $request->user()->id
            ){
                return response()->json([
                    'ok'        => false,
                    'message'   => __('You are not allowed to delete this comment')
                ]);
            }

            $comment->delete();

            return response()->json([
                'ok'        => true,
                'message'   => __('Comment deleted successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Update comment on advertisement
     * @param Advertisement $advertisement
     * @param int $comment_id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateComment(Advertisement $advertisement, int $comment_id, Request $request): JsonResponse
    {
        try {
            $comment = $advertisement->comments()->findOrFail($comment_id);

            if(
                !$request->user()->isAdmin() &&
                $comment->user_id !== $request->user()->id
            ){
                return response()->json([
                    'ok'        => false,
                    'message'   => __('You are not allowed to update this comment')
                ]);
            }

            $validation = Validator::make($request->all(), [
                'status'            => 'string|in:approved,pending,rejected',
                'rejection_reason'  => 'nullable|string',
            ]);

            if($validation->fails()) {
                return response()->json([
                    'ok'        => false,
                    'message'   => __('Validation error'),
                    'errors'    => $validation->errors()
                ])->setStatusCode(422);
            }

            $comment->update([
                'status'            => $request->get('status') ?? $comment->status,
                'rejection_reason'  => $request->get('rejection_reason') ?? $comment->rejection_reason
            ]);

            return response()->json([
                'ok'        => true,
                'message'   => __('Comment updated successfully'),
                'comment'   => $comment
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later'),
                'response'  => $e->getMessage()
            ]);
        }
    }
}
