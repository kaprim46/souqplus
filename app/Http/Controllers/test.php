<?php

namespace App\Http\Events\Salla;

use App\Http\Events\EventInterface;
use App\Http\Events\SallaTrait;
use App\Models\Device;
use App\Models\User;
use App\Traits\Whatsapp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class order_updated implements EventInterface
{
    use SallaTrait, Whatsapp;

    public function __invoke(array $data, User|null $customer): JsonResponse
    {
        if(!$customer->sallaAuthorize) {
            Log::error('Salla authorization not found', ['customer_id' => $customer->id]);

            return response()->json(['message' => 'Salla authorization not found'], 500);
        }

        if(!getUserPlanData('messages_limit', $customer->id)) {
            Log::error('Messages limit reached', ['customer_id' => $customer->id]);

            return response()->json(['message' => 'Messages limit reached'], 500);
        }
        $order          = $data['order'];

        $placeholders   = [
            '{customer_name}'           => $order['customer']['name'] ?? '',
            '{customer_email}'          => $order['customer']['email'] ?? '',
            '{customer_mobile}'         => $order['customer']['mobile'] ?? '',
            '{customer_city}'           => $order['customer']['city'] ?? '',
            '{order_id}'                => $order['reference_id'] ?? $order['id'],
            '{order_status}'            => $order['status']['name'] ?? '',
            '{url_complete_order}'      => $order['urls']['customer'] ?? '',
            '{total_amount}'            => $order['total']['amount'] . ' ' . $order['total']['currency'],
            '{customer_digital_products_codes}' => json_encode(array_column(array_column($order['items'], 'codes'), 0)),
            '{currency}'                => $order['total']['currency'],
            '{shipping_company}'        => $order['shipping']['company'] ?? '',
            '{tacking_link}'            => $order['shipping']['shipment']['tracking_link'] ?? '',
            '{tracking_number}'         => $order['shipping']['shipment']['pickup_id'] ?? '',
            '{checkout_url}'            => $order['checkout_url'] ?? '',
            '{total_weight}'            => $order['total_weight'] ?? '',
            '{products}'                => implode(', ', array_column($order['items'], 'name')),
            '{products_count}'          => count($order['items']),
            '{key_from_digital_product}'      => false
        ];



        $status = $data['order']['status']['slug'] ?? 'unknown';

        $appConfiguration = $this->getAppInfo($customer->sallaAuthorize->merchant_id, $customer->sallaAuthorize->access_token);
        $settings         = $appConfiguration['settings'];

        if(!isset($settings['api_key']) || !isset($settings['instance_id']))
        {
            Log::error('Salla settings not found', ['customer_id' => $customer->id]);

            return response()->json(['message' => 'Salla settings not found'], 500);
        }

        if($settings['api_key'] !== $customer->authkey)
        {
            Log::error('API key mismatch', ['customer_id' => $customer->id]);

            return response()->json(['message' => 'API key mismatch'], 500);
        }

        $device = Device::query()
            ->where('user_id', $customer->id)
            ->where('uuid', $settings['instance_id'])
            ->first();

        if(!$device)
        {
            Log::error('Device not found', ['customer_id' => $customer->id, 'settings' => $settings]);

            return response()->json(['message' => 'Device not found'], 500);
        }

        if($settings["{$status}_status"])
        {
            $phone = "{$order['customer']['mobile']}";

            $this->firstOrCreateContact($customer, [
                'mobile' => $order['customer']['mobile'],
                'mobile_code' => '',
                'first_name' => $order['customer']['name'],
                'last_name' => '',
            ]);

            Log::info('Order updated', ['message' => replacePlaceholders($settings[$status], $placeholders)]);

            $sentMessage = $this->messageSend([
                'message' => replacePlaceholders($settings[$status], $placeholders),
            ], $device->id, env('SALLA_MODE') === 'test' ? '212631230451' : $phone, 'plain-text', false, 100);

            if (!$sentMessage || $sentMessage["status"] != 200) {
                Log::error('Failed to send message', ['customer_id' => $customer->id, 'phone' => $phone]);
                return response()->json(['message' => 'Failed to send message'], 500);
            }

            $logs["device_id"]      = $device->id;
            $logs["user_id"]        = $customer->id;
            $logs["from"]           = $device->phone ?? null;
            $logs["type"]           = "single-send";
            $logs["to"]             = $phone;
            $this->saveLog($logs);

            return response()->json(['message' => 'Message sent'], 200);
        }

        Log::info('Status disabled', ['customer_id' => $customer->id, 'status' => $status]);

        return response()->json(['message' => 'Status disabled'], 200);
    }
}