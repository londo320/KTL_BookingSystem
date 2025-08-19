<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\PDFService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestPDFEmail extends Command
{
    protected $signature = 'test:pdf-email {booking_id} {email}';

    protected $description = 'Test PDF email functionality';

    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        $email = $this->argument('email');

        try {
            $booking = Booking::with(['slot.depot', 'bookingType', 'customer', 'user'])->findOrFail($bookingId);

            $this->info('Found booking: '.$booking->id);

            // Generate PDF
            $pdfService = new PDFService;
            $pdf = $pdfService->generateBookingPDF('admin.bookings.pdf', compact('booking'));

            $this->info('PDF generated successfully. Type: '.get_class($pdf));

            // Handle different PDF library outputs based on class type
            if ($pdf instanceof \Mpdf\Mpdf || $pdf instanceof \mPDF\mPDF) {
                // mPDF
                $pdfContent = $pdf->Output('', 'S');
            } elseif ($pdf instanceof \Barryvdh\DomPDF\PDF) {
                // DomPDF - get raw PDF content using render()
                $pdfContent = $pdf->render();
            } else {
                throw new \Exception('Unknown PDF library for email: '.get_class($pdf));
            }

            $this->info('PDF content extracted. Size: '.strlen($pdfContent).' bytes');

            // Send email
            Mail::send('admin.bookings.email', [
                'booking' => $booking,
                'customMessage' => 'This is a test email',
            ], function ($mail) use ($email, $booking, $pdfContent) {
                $mail->to($email)
                    ->subject('Test - Booking Details #'.$booking->id)
                    ->attachData($pdfContent, "booking-{$booking->id}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });

            $this->info('PDF email sent successfully to '.$email);

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send PDF email: '.$e->getMessage());
            $this->error('Stack trace: '.$e->getTraceAsString());

            return 1;
        }
    }
}
