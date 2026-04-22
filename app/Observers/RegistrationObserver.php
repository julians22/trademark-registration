<?php

namespace App\Observers;

use App\Mail\RegistationCreated;
use App\Mail\RegistationCreatedUser;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationObserver
{
    /**
     * Handle the Registration "created" event.
     */
    public function created(Registration $registration): void
    {
        // Send email to admin if the registration is created
        try {
            Mail::to('dabnerjulian@gmail.com')->send(new RegistationCreated($registration));
        } catch (\Throwable $th) {
            Log::error('Failed to send registration email', [
                'error' => $th->getMessage(),
                'registration_id' => $registration->id,
            ]);
        }

        // Send mail to user if the registration is created
        try {
            Mail::to($registration->email)->send(new RegistationCreatedUser($registration));
        } catch (\Throwable $th) {
            Log::error('Failed to send registration email to user', [
                'error' => $th->getMessage(),
                'registration_id' => $registration->id,
                'user_email' => $registration->email,
            ]);
        }
    }

    /**
     * Handle the Registration "updated" event.
     */
    public function updated(Registration $registration): void
    {
        //
    }

    /**
     * Handle the Registration "deleted" event.
     */
    public function deleted(Registration $registration): void
    {
        //
    }

    /**
     * Handle the Registration "restored" event.
     */
    public function restored(Registration $registration): void
    {
        //
    }

    /**
     * Handle the Registration "force deleted" event.
     */
    public function forceDeleted(Registration $registration): void
    {
        //
    }
}
