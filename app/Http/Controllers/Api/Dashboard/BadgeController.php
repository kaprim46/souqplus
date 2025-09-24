<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BadgeController extends Controller
{
    private const MAX_PER_PAGE = 10;

    /**
     * List of badges
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $badges = Badge::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
                ->paginate(self::MAX_PER_PAGE);

            return response()->json([
                'ok'        => true,
                'badges'    => $badges
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
            ]);
        }
    }


    /**
     * Store a badge
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'condition_type'    => 'required|in:advertisement_count,membership_duration,role',
            'condition'         => 'required|json',
            'condition.*'       => 'required',
            'icon'              => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];

        $validated = Validator::make($request->only([
            'name', 'description', 'condition_type', 'condition', 'icon'
        ]), $rules);

        /**
         * Validation by condition json
         */
        $validated->after(function ($validator) use ($request) {
            $conditionType = $request->input('condition_type');
            $condition = json_decode($request->input('condition'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $validator->errors()->add('condition', 'The condition field must be a valid JSON.');
                return;
            }

            switch ($conditionType) {
                case 'role':
                    if (!isset($condition['role']) || $condition['role'] === '') {
                        $validator->errors()->add('condition', 'The condition must contain a valid "role" key.');
                    }
                    break;
                case 'advertisement_count':
                    if (!isset($condition['advertisement_count']) || !is_numeric($condition['advertisement_count'])) {
                        $validator->errors()->add('condition', 'The condition must contain a valid "advertisement_count" key with a numeric value.');
                    }
                    break;
                case 'membership_duration':
                    if (!isset($condition['membership_duration']) || !is_numeric($condition['membership_duration'])) {
                        $validator->errors()->add('condition', 'The condition must contain a valid "membership_duration" key with a numeric value.');
                    }
                    break;
                default:
                    $validator->errors()->add('condition', 'Invalid condition type.');
                    break;
            }
        });

        if ($validated->fails())
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid data'),
                'errors'    => $validated->errors()
            ], 422);
        }

        $data = $validated->validate();

        /**
         * Upload icon
         */
        $icon = $request->file('icon');

        if($icon)
        {
            $path           = $icon->store('public/badges', [
                'filename' => $icon->getClientOriginalName() . '.' . $icon->getClientOriginalExtension() . '.' . time() . '.' . $icon->getClientOriginalExtension()
            ]);
            $data['icon']   = basename($path);
        }

        $badge = Badge::query()->create($data);

        return response()->json([
            'ok'            => true,
            'badge'         => $badge,
            'message'       => __('Badge created successfully')
        ]);
    }

    /**
     * Update a badge
     * @param Badge $badge
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Badge $badge, Request $request): JsonResponse
    {
        $rules = [
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'condition_type'    => 'required|in:advertisement_count,membership_duration,role',
            'condition'         => 'required|json',
            'condition.*'       => 'required',
        ];

        if($request->hasFile('icon'))
        {
            $rules['icon'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $validated = Validator::make($request->only([
            'name', 'description', 'condition_type', 'condition', 'icon'
        ]), $rules);

        /**
         * Validation by condition json
         */
        $validated->after(function ($validator) use ($request) {
            $conditionType = $request->input('condition_type');
            $condition = json_decode($request->input('condition'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $validator->errors()->add('condition', 'The condition field must be a valid JSON.');
                return;
            }

            switch ($conditionType) {
                case 'role':
                    if (!isset($condition['role']) || $condition['role'] === '') {
                        $validator->errors()->add('condition', 'The condition must contain a valid "role" key.');
                    }
                    break;
                case 'advertisement_count':
                    if (!isset($condition['advertisement_count']) || !is_numeric($condition['advertisement_count'])) {
                        $validator->errors()->add('condition', 'The condition must contain a valid "advertisement_count" key with a numeric value.');
                    }
                    break;
                case 'membership_duration':
                    if (!isset($condition['membership_duration']) || !is_numeric($condition['membership_duration'])) {
                        $validator->errors()->add('condition', 'The condition must contain a valid "membership_duration" key with a numeric value.');
                    }
                    break;
                default:
                    $validator->errors()->add('condition', 'Invalid condition type.');
                    break;
            }
        });

        if ($validated->fails())
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid data'),
                'errors'    => $validated->errors()
            ], 422);
        }

        $data = $validated->validate();

        /**
         * Upload icon check type is file
         */
        $icon = $request->file('icon');

        if($icon)
        {
            $path           = $icon->store('public/badges', [
                'filename' => $icon->getClientOriginalName() . '.' . $icon->getClientOriginalExtension() . '.' . time() . '.' . $icon->getClientOriginalExtension()
            ]);
            $data['icon']   = basename($path);
        }

        $badge->update($data);

        return response()->json([
            'ok'            => true,
            'badge'         => $badge,
            'message'       => __('Badge updated successfully')
        ]);
    }

    /**
     * destroy a badge
     * @param Badge $badge
     * @return JsonResponse
     */
    public function destroy(Badge $badge): JsonResponse
    {
        $badge->delete();

        return response()->json([
            'ok'        => true,
            'message'   => __('Badge deleted successfully')
        ]);
    }
}
