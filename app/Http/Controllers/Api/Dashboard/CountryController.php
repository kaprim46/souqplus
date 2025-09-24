<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CountryController extends Controller
{

    private const MAX_PER_PAGE = 10;

    /**
     * Display a listing of the resource or search for a country
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $countries = Country::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->withCount('cities');

            if ($request->get('pagination'))
                $countries = $countries->paginate(self::MAX_PER_PAGE);

            else
                $countries = $countries->get();


            return response()->json([
                'ok'                => true,
                'countries'        => $countries
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validated->fails())
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid data'),
                'errors'    => $validated->errors()
            ]);
        }

        try {
            $country = Country::create($request->all());

            return response()->json([
                'ok'        => true,
                'country'   => $country,
                'message'   => __('Country created successfully')
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
     * Update the specified resource in storage.
     * @param Request $request
     * @param $country
     * @return JsonResponse
     */
    public function update(Request $request, $country): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validated->fails())
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid data'),
                'errors'    => $validated->errors()
            ]);
        }

        try {
            $country = Country::find($country);
            $country->update($request->all());

            return response()->json([
                'ok'        => true,
                'country'   => $country,
                'message'   => __('Country updated successfully')
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
     * Remove the specified resource from storage.
     * @param $country
     * @return JsonResponse
     */
    public function destroy($country): JsonResponse
    {
        try {
            $country = Country::find($country);
            $country->delete();

            return response()->json([
                'ok'        => true,
                'message'   => __('Country deleted successfully')
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
     * Display a listing of the resource or search for a city optimal for a country
     * @param Request $request
     * @param int|null $country
     * @return JsonResponse
     */
    public function cities(Request $request, ?int $country = null): JsonResponse
    {
        try {
            $cities = City::query()->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            });

            if ($country)
                $cities = $cities->where('country_id', $country);
            else
                $cities = $cities->with('country');

            if ($request->get('pagination'))
                $cities = $cities->paginate(self::MAX_PER_PAGE);

            else
                $cities = $cities->get();

            $res = [
                'ok'                => true,
                'cities'            => $cities
            ];

            if ($country)
                $res['country'] = Country::find($country) ?? null;

            return response()->json($res);
        } catch (Throwable $e)
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function storeCity(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id'
        ]);

        if ($validated->fails())
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid data'),
                'errors'    => $validated->errors()
            ]);
        }

        try {
            $city = City::create($request->all());

            $city->load('country');

            return response()->json([
                'message'   => __('City created successfully'),
                'ok'        => true,
                'city'      => $city
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'        => false,
                'message'   => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $city
     * @return JsonResponse
     */
    public function updateCity(Request $request, $city): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id'
        ]);

        if ($validated->fails())
        {
            return response()->json([
                'ok'        => false,
                'message'   => __('Invalid data'),
                'errors'    => $validated->errors()
            ]);
        }

        try {
            $city = City::find($city);
            $city->update($request->all());

            $city->refresh()->load('country');

            return response()->json([
                'message'   => __('City updated successfully'),
                'ok'        => true,
                'city'      => $city
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
     * Remove the specified resource from storage.
     * @param $city
     * @return JsonResponse
     */
    public function destroyCity($city): JsonResponse
    {
        try {
            $city = City::find($city);
            $city->delete();

            return response()->json([
                'ok'        => true,
                'message'   => __('City deleted successfully')
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
