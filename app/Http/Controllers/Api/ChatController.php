<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Jobs\FirebaseMessagesProcess;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    const MAX_PER_PAGE = 10;

    /**
     * Open a conversation with a usre
     * @param Request $request
     * @return JsonResponse
     */
    public function openConversation(Request $request): JsonResponse
    {
        $request->validate([
            'owner'             => 'required|integer|exists:users,id',
            'advertisement_id'  => 'nullable|integer|exists:advertisements,id',
            'type'              => 'nullable|string|in:business'
        ]);

        if ($request->user()->id === intval($request->input('owner'))) {
            return response()->json([
                'ok'        => false,
                'message'   => 'You cannot open a conversation with yourself.'
            ], 400);
        }

        /**
         * Check
         */
        $conversation = Conversation::query()
            ->where(function ($q) use ($request) {
                $q->where('owner_id', $request->user()->id)
                    ->where('user_id', $request->input('owner'));
            })
            ->orWhere(function ($q) use ($request) {
                $q->where('owner_id', $request->input('owner'))
                    ->where('user_id', $request->user()->id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'owner_id'          => $request->user()->id,
                'user_id'           => $request->input('owner'),
                'advertisement_id'  => $request->input('advertisement_id')
            ]);
        }

        $conversation->load(['owner', 'user', 'advertisement' => function ($q) {
            $q->select('id', 'name', 'slug')->with('mainMedia');
        }]);


        return response()->json([
            'ok'            => true,
            'conversation'  => [
                'id'                        => $conversation->id,
                'owner'                     => $request->user()->id !== $conversation->owner->id ? [
                    'id'                    => $conversation->owner->id,
                    'name'                  => $conversation->owner->role === 'business' ? (is_array($conversation->owner->business_info) && isset($conversation->owner->business_info['business_name']) ? $conversation->owner->business_info['business_name'] : '--') : $conversation->owner->name,
                    'avatar_url'            => $conversation->owner->role === 'business' ? (is_array($conversation->owner->business_info) && isset($conversation->owner->business_info['business_logo']) ? asset($conversation->owner->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$conversation->owner->name}&color=7F9CF5&background=EBF4FF") : $conversation->owner->avatar_url,
                    'is_business'           => $conversation->owner->role === 'business',
                    'slug'                  => $conversation->owner->role === 'business' ? Str::slug($conversation->owner->business_info['business_name']) : Str::slug($conversation->owner->name),

                ] : [
                    'id'                    => $conversation->user->id,
                    'name'                  => $conversation->user->role === 'business' ? (is_array($conversation->user->business_info) && isset($conversation->user->business_info['business_name']) ? $conversation->user->business_info['business_name'] : '--') : $conversation->user->name,
                    'avatar_url'            => $conversation->user->role === 'business' ? (is_array($conversation->user->business_info) && isset($conversation->user->business_info['business_logo']) ? asset($conversation->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$conversation->user->name}&color=7F9CF5&background=EBF4FF") : $conversation->user->avatar_url,
                    'is_business'           => $conversation->owner->role === 'business',
                    'slug'                  => $conversation->owner->role === 'business' ? Str::slug($conversation->owner->business_info['business_name']) : Str::slug($conversation->owner->name),
                ],
                'advertisement'              => $conversation->advertisement ? [
                    'id'            => $conversation->advertisement->id,
                    'name'          => $conversation->advertisement->name,
                    'main_media'    => $conversation->advertisement->mainMedia ? $conversation->advertisement->mainMedia?->url_thumb : null,
                    'slug'          => $conversation->advertisement->slug,
                ] : null
            ]
        ]);
    }

    /**
     * Get messages of a conversation
     * @param Request $request
     * @return JsonResponse
     */
    public function getMessages(Request $request): JsonResponse
    {
        $request->validate([
            'conversationId' => 'required|integer|exists:conversations,id',
        ]);

        $messages = Message::where('conversation_id', $request->input('conversationId'))
            ->latest()
            ->paginate(self::MAX_PER_PAGE);

        return response()->json([
            'ok'        => true,
            'messages'  => (new MessageResource($messages))->toArray($request)
        ]);
    }

    /**
     * Send a message to a conversation
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message'           => 'required|string',
            'conversationId'    => 'required|integer|exists:conversations,id',
        ]);

        /**
         * Check if the user is part of the conversation
         */
        $conversation = Conversation::find($request->input('conversationId'));

        if ($conversation->owner_id !== $request->user()->id && $conversation->user_id !== $request->user()->id) {
            return response()->json([
                'ok'        => false,
                'message'   => 'You are not part of this conversation.'
            ], 400);
        }

        $message = Message::create([
            'conversation_id'   => $request->input('conversationId'),
            'sender_id'         => $request->user()->id,
            'body'              => $request->input('message')
        ]);

        if (!$message) {
            return response()->json([
                'ok'        => false,
                'message'   => 'Failed to send message.'
            ], 500);
        }

        FirebaseMessagesProcess::dispatch([
            'open' => [
                'id'            => $message->id,
                'body'          => $message->body,
                'sender_id'     => $request->user()->id,
                'created_at'    => $message->created_at->fromNow()
            ],
            'conversation'      => [
                'id'                        => $conversation->id,
                'owner'                     => [
                    'id'                => $conversation->owner->id,
                    'name'              => $conversation->owner->role === 'business' ? (is_array($conversation->owner->business_info) && isset($conversation->owner->business_info['business_name']) ? $conversation->owner->business_info['business_name'] : '--') : $conversation->owner->name,
                    'avatar_url'        => $conversation->owner->role === 'business' ? (is_array($conversation->owner->business_info) && isset($conversation->owner->business_info['business_logo']) ? asset($conversation->owner->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$conversation->owner->name}&color=7F9CF5&background=EBF4FF") : $conversation->owner->avatar_url,
                ],
                'user'                      => [
                    'id'                => $conversation->user->id,
                    'name'              => $conversation->user->role === 'business' ? (is_array($conversation->user->business_info) && isset($conversation->user->business_info['business_name']) ? $conversation->user->business_info['business_name'] : '--') : $conversation->user->name,
                    'avatar_url'        => $conversation->user->role === 'business' ? (is_array($conversation->user->business_info) && isset($conversation->user->business_info['business_logo']) ? asset($conversation->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$conversation->user->name}&color=7F9CF5&background=EBF4FF") : $conversation->user->avatar_url,
                ],
                'total_unread'              => $conversation->messages->where('status', 1)->where('sender_id', '!=', $request->user()->id)->count() + 1,
                'has_messages'              => $conversation->messages->count() > 0,
                'last_message'              => $message->body,
                'last_message_created_at'   => Carbon::parse($message->created_at)->fromNow(),
                'last_message_date_time'    => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            ]
        ])->onQueue('firebase')->afterResponse();

        return response()->json([
            'ok'        => true,
            'message'   => __('messages.message_sent'),
        ]);
    }

    /**
     * Get conversations of a user
     * @param Request $request
     * @return JsonResponse
     */
    public function getConversations(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|string|in:business'
        ]);

        $query = Conversation::with(['owner', 'user', 'advertisement', 'messages'])
            ->where(function ($q) use ($request) {
                $q->where('owner_id', $request->user()->id)
                    ->orWhere('user_id', $request->user()->id);
            });

        $conversations = $query->paginate(self::MAX_PER_PAGE);

        $mappedConversations = $conversations->getCollection()->map(function ($conversation) use ($request) {
            $lastMessage = DB::table('messages')
                ->where('conversation_id', $conversation->id)
                ->latest()
                ->first();

            return [
                'id'                    => $conversation->id,
                'contact'               => $conversation->user->id === $request->user()->id ? [
                    'id'                => $conversation->owner->id,
                    'name'              => $conversation->owner->role === 'business' ? (is_array($conversation->owner->business_info) && isset($conversation->owner->business_info['business_name']) ? $conversation->owner->business_info['business_name'] : '--') : $conversation->owner->name,
                    'avatar_url'        => $conversation->owner->role === 'business' ? (is_array($conversation->owner->business_info) && isset($conversation->owner->business_info['business_logo']) ? asset($conversation->owner->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$conversation->owner->name}&color=7F9CF5&background=EBF4FF") : $conversation->owner->avatar_url,
                ] : [
                    'id'                => $conversation->user->id,
                    'name'              => $conversation->user->role === 'business' ? (is_array($conversation->user->business_info) && isset($conversation->user->business_info['business_name']) ? $conversation->user->business_info['business_name'] : '--') : $conversation->user->name,
                    'avatar_url'        => $conversation->user->role === 'business' ? (is_array($conversation->user->business_info) && isset($conversation->user->business_info['business_logo']) ? asset($conversation->user->business_info['business_logo']) : "https://ui-avatars.com/api/?name={$conversation->user->name}&color=7F9CF5&background=EBF4FF") : $conversation->user->avatar_url,
                ],
                'is_read'                   => $lastMessage?->status === 2,
                'total_unread'              => $conversation->messages->where('status', 1)->where('sender_id', '!=', $request->user()->id)->count(),
                'has_messages'              => $conversation->messages->count() > 0,
                'last_message'              => Str::limit($lastMessage?->body, 30),
                'last_message_created_at'   => $lastMessage ? Carbon::parse($lastMessage->created_at)->fromNow() : null,
                'last_message_date_time'    => $lastMessage ? Carbon::parse($lastMessage->created_at)->format('Y-m-d H:i:s') : null,
            ];
        })->sortByDesc('last_message_date_time')->values();

        $conversations->setCollection($mappedConversations);

        $userId = $request->user()->id;

        $totalUnreadConversations = $query->clone()->whereHas('messages', function ($q) use ($userId) {
            $q->where('status', 1)->where('sender_id', '!=', $userId);
        })->count();

        $totalUnreadMessages = $query->clone()->withCount(['messages' => function ($q) use ($userId) {
            $q->where('status', 1)->where('sender_id', '!=', $userId);
        }])->get()->sum('messages_count');

        return response()->json([
            'total_unread_conversations'    => $totalUnreadConversations,
            'total_unread_messages'         => $totalUnreadMessages,
            'conversations'                 => $conversations,
            'ok'                            => true,
        ]);
    }

    /**
     * Mark a conversation as read
     * @param Request $request
     * @return JsonResponse
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $conversations = Conversation::where(function ($query) use ($request) {
                $query->where('owner_id', $request->user()->id)
                    ->orWhere('user_id', $request->user()->id);
            })->pluck('id');

            Message::whereIn('conversation_id', $conversations)
                ->where('status', 1)
                ->where('sender_id', '!=', $request->user()->id)
                ->update(['status' => 2]);

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => __('Successfully marked conversation as read.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => __('Error marking conversation as read.'),
            ], 500);
        }
    }
}
