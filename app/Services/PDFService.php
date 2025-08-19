<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class PDFService
{
    public function generateBookingPDF($view, $data)
    {
        $view = $this->getCleanViewName($view);

        // Try DomPDF first (Laravel integration)
        try {
            $pdf = DomPDF::loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf;
        } catch (\Exception $e) {
            // Fall back to mPDF if DomPDF fails
            return $this->generateWithMpdf($view, $data);
        }
    }

    private function generateWithMpdf($view, $data)
    {
        try {
            // Try different mPDF class names for different versions
            if (class_exists('\Mpdf\Mpdf')) {
                // mPDF v8+
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 11,
                    'margin_left' => 10,
                    'margin_right' => 10,
                    'margin_top' => 10,
                    'margin_bottom' => 10,
                    'orientation' => 'P',
                ]);
            } elseif (class_exists('\mPDF\mPDF')) {
                // mPDF v6-7
                $mpdf = new \mPDF\mPDF([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 11,
                    'margin_left' => 10,
                    'margin_right' => 10,
                    'margin_top' => 10,
                    'margin_bottom' => 10,
                    'orientation' => 'P',
                ]);
            } else {
                throw new \Exception('mPDF class not found');
            }

            $html = view($view, $data)->render();
            $mpdf->WriteHTML($html);

            return $mpdf;
        } catch (\Exception $e) {
            // If mPDF fails, throw a clear error
            throw new \Exception('mPDF generation failed. Error: '.$e->getMessage());
        }
    }

    private function getCleanViewName($view)
    {
        // Use final clean templates
        if ($view === 'customer.bookings.pdf') {
            return 'customer.bookings.pdf-final';
        } elseif ($view === 'admin.bookings.pdf') {
            return 'admin.bookings.pdf-cards';
        }

        return $view;
    }
}
