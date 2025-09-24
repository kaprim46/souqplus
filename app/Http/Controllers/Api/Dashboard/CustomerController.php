<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CustomerRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CustomerController extends Controller
{

    CONST MAX_PER_PAGE = 15;

    /**
     * Get customers
     * @param CustomerRequest $request
     * @return JsonResponse
     */
    public function index(CustomerRequest $request): JsonResponse
    {
        try {
            $customers = User::query()->where('id', '!=', auth()->id())
                ->when($request->get('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhere('phone_number', 'like', '%' . $search . '%');
                })
                ->withCount('advertisements')
                ->withTrashed()
                ->paginate(self::MAX_PER_PAGE);

            return response()->json([
                'ok'            => true,
                'customers'     => $customers
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Show customer details
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $customer = User::query()
            ->withTrashed()
            ->find($id);

        try {
            return response()->json([
                'ok'            => true,
                'customer'      => $customer
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
     * Update customer
     * @param int $id
     * @param CustomerRequest $request
     * @return JsonResponse
     */
    public function update(int $id, CustomerRequest $request): JsonResponse
    {

        $customer = User::query()
            ->withTrashed()
            ->find($id);

        try {
            $data = $request->only(['name', 'country_code', 'phone_number', 'email', 'role', 'is_verified']);

            if($request->get('current_password') || $request->get('password')) {
                $data['password'] = Hash::make($request->get('password'));
            }


            $customer->update($data);

            return response()->json([
                'ok'            => true,
                'customer'      => $customer,
                'message'       => __('Customer updated successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Store customer
     * @param CustomerRequest $request
     * @return JsonResponse
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'country_code', 'phone_number', 'email', 'role', 'is_verified']);
            $data['password'] = Hash::make($request->get('password'));

            $customer = User::query()->create($data);

            return response()->json([
                'ok'            => true,
                'customer'      => $customer,
                'message'       => __('Customer created successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Delete customer
     * @param User $customer
     * @return JsonResponse
     */
    public function destroy(User $customer): JsonResponse
    {
        try {
            $customer->delete();

            return response()->json([
                'ok'            => true,
                'message'       => __('Customer deleted successfully')
            ]);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }
}
