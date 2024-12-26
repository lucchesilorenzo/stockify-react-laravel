<?php

namespace App\Jobs;

use App\Models\CustomerShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateShipmentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shipmentId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $shipmentId)
    {
        $this->shipmentId = $shipmentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shipment = CustomerShipment::find($this->shipmentId);
        if ($shipment) {
            $shipment->update(['status' => 'DELIVERED']);
        }
    }
}
