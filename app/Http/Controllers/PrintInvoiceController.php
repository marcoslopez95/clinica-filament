<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Enums\InvoiceType;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PrintInvoiceController extends Controller
{
    public function __invoke(Invoice $record)
    {
        $record->load(['details.content', 'details.referenceResults.referenceValue.unit', 'payments.paymentMethod', 'payments.currency', 'invoiceable']);

        foreach ($record->details as $detail) {
            if ($detail->content_type === 'App\Models\Exam') {
                $detail->content->load('examCategory');
            } elseif ($detail->content_type === 'App\Models\Product') {
                $detail->content->load('productCategory');
            }
        }

        $view = $record->invoice_type === InvoiceType::LABORATORY
            ? 'pdf.laboratory'
            : 'pdf.invoice';

        $html = view($view, ['record' => $record])->render();

        $mpdf = new Mpdf([
            'format' => 'Letter',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-' . $record->id . '.pdf"',
        ]);
    }
}
