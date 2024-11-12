<?php

namespace App\Jobs;

use App\Models\InvestmentInstrument;
use App\Services\YahooFinanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateInvestmentInstrumentPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(YahooFinanceService $yahooFinanceService)
    {
        $instruments = InvestmentInstrument::where('user_id', $this->user->id)->get();

        foreach ($instruments as $instrument) {
            //log the instrument
            $price = $yahooFinanceService->getPrice($instrument->ticker);
            if ($price !== null) {
                $instrument->update(['price' => $price]);
            }
        }
    }
}
