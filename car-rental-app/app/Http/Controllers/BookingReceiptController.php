<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingReceiptController extends Controller
{
    /**
     * Download booking receipt as PDF
     */
    public function download(Booking $booking): Response
    {
        // Ensure the user can only download their own receipts
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        // Load relationships
        $booking->load(['user', 'vehicle']);

        // Generate PDF
        $pdf = Pdf::loadView('receipts.booking', [
            'booking' => $booking,
        ]);

        // Set paper size
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = 'CARTAR-Receipt-' . str_pad($booking->id, 8, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream booking receipt as PDF (view in browser)
     */
    public function stream(Booking $booking): Response
    {
        // Ensure the user can only view their own receipts
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        // Load relationships
        $booking->load(['user', 'vehicle']);

        // Generate PDF
        $pdf = Pdf::loadView('receipts.booking', [
            'booking' => $booking,
        ]);

        // Set paper size
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('booking-receipt.pdf');
    }
}
