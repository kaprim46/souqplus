<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Kreait\Firebase\Exception\DatabaseException;

class FirebaseMessagesProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data)
    {
        $this->data         = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $firebaseService = new FirebaseService();

        try {
            $firebaseService->connect()->getReference('messages')->push($this->data);
        } catch (\Exception|DatabaseException $e) {
            \Log::error('Failed to push message to Firebase', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
