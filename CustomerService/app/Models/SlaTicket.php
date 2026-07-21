<?php

namespace App\Models;

/**
 * @deprecated Use Ticket model directly. SlaTicket is now a wrapper for backward compatibility.
 */
class SlaTicket extends Ticket
{
    // Inherits everything from Ticket
    // This prevents breaking existing code that references SlaTicket
}