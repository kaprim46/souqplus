<?php

namespace App\Console\Commands;

use App\Models\AdvertisementStory;
use Illuminate\Console\Command;

class DeleteStoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete stories that are older than 24 hours.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Stories delete command executed.');

        $stories = AdvertisementStory::query()
            ->where('created_at', '<', now()->subDay())
            ->delete();

        $this->info('Stories deleted successfully.');
    }
}
