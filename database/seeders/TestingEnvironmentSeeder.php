<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Catálogos y Datos Base
        $typeDocuments = \App\Models\TypeDocument::factory(5)->create();
        $currencies = \App\Models\Currency::factory(3)->create();
        $specializations = \App\Models\Specialization::factory(10)->create();
        $departments = \App\Models\Department::factory(5)->create();
        $unitCategories = \App\Models\UnitCategory::factory(3)->create();
        $units = \App\Models\Unit::factory(10)->create();
        $warehouses = \App\Models\Warehouse::factory(2)->create();
        $productCategories = \App\Models\ProductCategory::factory(5)->create();
        $serviceCategories = \App\Models\ServiceCategory::factory(5)->create();
        $expenseCategories = \App\Models\ExpenseCategory::factory(5)->create();
        $paymentMethods = \App\Models\PaymentMethod::factory(4)->create();

        // 2. Personal y Proveedores
        $doctors = \App\Models\Doctor::factory(10)->create([
            'type_document_id' => $typeDocuments->random()->id,
            'specialization_id' => $specializations->random()->id,
        ]);

        $suppliers = \App\Models\Supplier::factory(5)->create([
            'type_document_id' => $typeDocuments->random()->id,
        ]);

        // 3. Pacientes
        $patients = \App\Models\Patient::factory(20)->create([
            'type_document_id' => $typeDocuments->random()->id,
        ]);

        // 4. Productos y Servicios
        $products = \App\Models\Product::factory(20)->create([
            'unit_id' => $units->random()->id,
            'product_category_id' => $productCategories->random()->id,
            'currency_id' => $currencies->random()->id,
        ]);

        $services = \App\Models\Service::factory(10)->create([
            'unit_id' => $units->random()->id,
            'service_category_id' => $serviceCategories->random()->id,
        ]);

        $exams = \App\Models\Exam::factory(15)->create([
            'currency_id' => $currencies->random()->id,
        ]);

        // 5. Inventario
        foreach ($products as $product) {
            \App\Models\Inventory::factory()->create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouses->random()->id,
            ]);
        }

        // 6. Habitaciones
        \App\Models\Room::factory(10)->create([
            'currency_id' => $currencies->random()->id,
        ]);

        // 7. Gastos
        \App\Models\Expense::factory(10)->create([
            'currency_id' => $currencies->random()->id,
            'expense_category_id' => $expenseCategories->random()->id,
            'supplier_id' => $suppliers->random()->id,
        ]);

        // 8. Facturación y Pagos
        foreach ($patients->random(10) as $patient) {
            $invoice = \App\Models\Invoice::factory()->create([
                'invoiceable_id' => $patient->id,
                'invoiceable_type' => \App\Models\Patient::class,
                'currency_id' => $currencies->random()->id,
                'type_document_id' => $typeDocuments->random()->id,
            ]);

            // Detalles de factura (mezcla de productos, servicios y exámenes)
            $details = \App\Models\InvoiceDetail::factory(3)->create([
                'invoice_id' => $invoice->id,
            ]);

            foreach ($details as $detail) {
                \App\Models\InvoiceDetailTax::factory()->create(['invoice_detail_id' => $detail->id]);
            }

            \App\Models\InvoiceDiscount::factory()->create(['invoice_id' => $invoice->id]);

            // Pagos
            $payment = \App\Models\Payment::factory()->create([
                'invoice_id' => $invoice->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'currency_id' => $currencies->random()->id,
            ]);

            // Reembolsos ocasionales
            if (rand(0, 1)) {
                \App\Models\Refund::factory()->create([
                    'invoice_id' => $invoice->id,
                    'currency_id' => $currencies->random()->id,
                    'payment_method_id' => $paymentMethods->random()->id,
                ]);
            }
        }
    }
}
