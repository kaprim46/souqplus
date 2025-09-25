<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingController extends Controller
{
    /**
     * Get settings with full URLs for assets
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $settings = [
                'app_currency' => Setting::get('app_currency', '$'),
                'app_description' => Setting::get('app_description', 'test'),
                'app_email' => Setting::get('app_email', 'support@sooqplus.net'),
                'app_favicon' => $this->getAssetUrl('app_favicon', 'favicon', 'favicon.png'),
                'app_keywords' => Setting::get('app_keywords', 'x'),
                'app_logo' => $this->getAssetUrl('app_logo', 'logo', 'logo.png'),
                'app_name' => Setting::get('app_name', 'SooqPlus'),
                'app_robots' => Setting::get('app_robots', 'index, follow'),
                'app_social_media' => Setting::get('app_social_media', '{"facebook":"","twitter":"","instagram":"","linkedin":""}'),
                'app_whatsapp' => Setting::get('app_whatsapp', '+966 53 860 8467'),
                'app_timezone' => Setting::get('app_timezone', 'Asia/Riyadh'),
                'app_url' => Setting::get('app_url', 'https://sooqplus.net/'),
                'warning_comments_message' => Setting::get('warning_comments_message', '<p class="v-nazih-paragraph">الرجاء عدم نشر اي تعليق فيه اساءة واحترام المجتمع</p>')
            ];

            return response()->json([
                'ok' => true,
                'settings' => $settings
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later'),
            ]);
        }
    }

    /**
     * Update settings
     * @param SettingRequest $request
     * @return JsonResponse
     */
    public function update(SettingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if($request->hasFile('app_logo')) {
                $oldLogo = Setting::get('app_logo');
                if($oldLogo && Storage::disk('public')->exists('logo/' . $oldLogo)) {
                    Storage::disk('public')->delete('logo/' . $oldLogo);
                }

                $logoExtension = $request->file('app_logo')->extension();
                $logoFilename = 'logo_' . time() . '.' . $logoExtension; 
                
                
                $request->file('app_logo')->storeAs('logo', $logoFilename, 'public');
                $data['app_logo'] = $logoFilename;
            }

            if($request->hasFile('app_favicon')) {
                $oldFavicon = Setting::get('app_favicon');
                if($oldFavicon && Storage::disk('public')->exists('favicon/' . $oldFavicon)) {
                    Storage::disk('public')->delete('favicon/' . $oldFavicon);
                }

                $faviconExtension = $request->file('app_favicon')->extension();
                $faviconFilename = 'favicon_' . time() . '.' . $faviconExtension; 
                
                
                $request->file('app_favicon')->storeAs('favicon', $faviconFilename, 'public');
                $data['app_favicon'] = $faviconFilename;
            }

            // Update settings in database
            foreach ($data as $key => $value) {
                Setting::query()->updateOrInsert(
                    [
                        'key' => $key
                    ], [
                        'value' => $value
                    ]
                );
            }

            $updatedSettings = [
                'app_currency' => Setting::get('app_currency', '$'),
                'app_description' => Setting::get('app_description', 'test'),
                'app_email' => Setting::get('app_email', 'support@sooqplus.net'),
                'app_favicon' => $this->getAssetUrl('app_favicon', 'favicon', 'favicon.png'),
                'app_keywords' => Setting::get('app_keywords', 'x'),
                'app_logo' => $this->getAssetUrl('app_logo', 'logo', 'logo.png'),
                'app_name' => Setting::get('app_name', 'SooqPlus'),
                'app_robots' => Setting::get('app_robots', 'index, follow'),
                'app_social_media' => Setting::get('app_social_media', '{"facebook":"","twitter":"","instagram":"","linkedin":""}'),
                'app_whatsapp' => Setting::get('app_whatsapp', '+966 53 860 8467'),
                'app_timezone' => Setting::get('app_timezone', 'Asia/Riyadh'),
                'app_url' => Setting::get('app_url', 'https://sooqplus.net/'),
                'warning_comments_message' => Setting::get('warning_comments_message', '<p class="v-nazih-paragraph">الرجاء عدم نشر اي تعليق فيه اساءة واحترام المجتمع</p>')
            ];

            return response()->json([
                'ok'        => true,
                'message'   => __('Settings updated successfully'),
                'settings'  => $updatedSettings
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Something went wrong, please try again later')
            ]);
        }
    }

    /**
     * Get full URL for asset
     * @param string $settingKey
     * @param string $folder
     * @param string $default
     * @return string
     */
    private function getAssetUrl(string $settingKey, string $folder, string $default): string
    {
        $filename = Setting::get($settingKey, $default);
        
        if ($filename && $filename !== $default && Storage::disk('public')->exists($folder . '/' . $filename)) {
            return asset('storage/' . $folder . '/' . $filename);
        }
        
        return $filename;
    }
}