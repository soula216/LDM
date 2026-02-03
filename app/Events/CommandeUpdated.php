<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommandeUpdated
{
    use Dispatchable, SerializesModels;

    public $commande;

    /**
     * Create a new event instance.
     */
    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }
}
