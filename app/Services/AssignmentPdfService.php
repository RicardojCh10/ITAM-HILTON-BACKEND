<?php

namespace App\Services;

use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\Asset;
use Carbon\Carbon;

class AssignmentPdfService extends Fpdf
{
    protected $asset;

    public function generatePdf(Asset $asset)
    {
        $this->asset = $asset;
        
        $specs = $this->asset->specs ?? []; 

        $this->AddPage();
        $this->SetAutoPageBreak(true, 10);
        $this->SetLineWidth(0.2); 

        // --- CABECERA ---
        if (file_exists(public_path('img/hilton_logo.png'))) {
            $this->Image(public_path('img/hilton_logo.png'), 15, 10, 30);
        }
        
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('HILTON'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, utf8_decode('CARTA ENTREGA Y CONTROL DE ACTIVOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode('CUNQR - Departamento de Sistemas'), 0, 1, 'C');
        $this->Ln(5);

        // --- A. TIPO DE ENTREGA ---
        $this->sectionTitle('A. TIPO DE ENTREGA');
        $this->SetFont('Arial', '', 8);
        
        $check = "X"; 
        
        $this->Cell(5, 5, $check, 1, 0, 'C'); 
        $this->Cell(25, 5, utf8_decode('Asignación'), 0, 0);
        
        $this->Cell(5, 5, '', 1, 0, 'C'); 
        $this->Cell(25, 5, utf8_decode('Préstamo'), 0, 0);
        
        $this->Cell(5, 5, '', 1, 0, 'C'); 
        $this->Cell(25, 5, utf8_decode('Devolución'), 0, 0);

        // Fecha
        $this->SetX(130);
        $this->Cell(15, 5, 'Fecha:', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(40, 5, Carbon::now()->format('d / m / Y'), 1, 1, 'C');
        $this->Ln(2);

        // --- B. INFORMACIÓN GENERAL ---
        $this->sectionTitle('B. INFORMACIÓN GENERAL');
        $member = $this->asset->assigned_to;

        // Fila 1: Nombres
        $this->fieldRow([
            ['label' => 'Nombre Completo:', 'value' => $member ? $member->name : '', 'width' => 95],
            ['label' => 'No. Team Member:', 'value' => $member->tm_id ?? '', 'width' => 45],
            ['label' => 'Reclutador:', 'value' => '53248', 'width' => 45] 
        ]);

        $position = $member && isset($member->corporate_info['position']) ? $member->corporate_info['position'] : '';
        $department = $member && isset($member->corporate_info['department']) ? $member->corporate_info['department'] : '';

        $this->fieldRow([
            ['label' => 'Posición:', 'value' => $position, 'width' => 95],
            ['label' => 'Departamento:', 'value' => $department, 'width' => 95]
        ]);
        $this->Ln(2);

        // --- C. DESCRIPCIÓN DE EQUIPO (CÓMPUTO) ---
        $isComputer = $this->isCategory('Laptop') || $this->isCategory('Desktop');
        
        $this->sectionTitle('C. DESCRIPCIÓN DE EQUIPO (Cómputo / Tablet / Router)');
        
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isComputer ? $this->asset->info['category'] : '', 'width' => 60],
            ['label' => 'Marca:', 'value' => $isComputer ? $this->asset->info['brand'] : '', 'width' => 60],
            ['label' => 'Modelo:', 'value' => $isComputer ? $this->asset->info['model'] : '', 'width' => 70]
        ]);

        $this->fieldRow([
            ['label' => 'Serial:', 'value' => $isComputer ? $this->asset->info['serial_number'] : '', 'width' => 60],
            ['label' => 'Hilton Name:', 'value' => $isComputer ? $this->asset->info['hilton_name'] : '', 'width' => 60],
            ['label' => 'Mac Address:', 'value' => $isComputer ? ($this->asset->network['mac_address'] ?? '') : '', 'width' => 70]
        ]);
        
        // Accesorios Checkboxes
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 5, 'Accesorios Incluidos:', 0, 1);
        $this->SetFont('Arial', '', 8);
        
        $acc = $isComputer ? ($specs['accessories'] ?? []) : [];
        
        $this->checkbox('Cargador', true); 
        $this->checkbox('Monitor', false);
        $this->checkbox('Teclado', false);
        $this->checkbox('Mouse', false);
        $this->checkbox('Candado', false);
        $this->checkbox('Docking', false);
        $this->Ln(5);

        // --- SECCIÓN CELULAR ---
        $isPhone = $this->isCategory('Phone') || $this->isCategory('Celular') || $this->isCategory('Mobile');
        
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, utf8_decode('CELULAR / DISPOSITIVO MÓVIL'), 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Marca:', 'value' => $isPhone ? $this->asset->info['brand'] : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isPhone ? $this->asset->info['model'] : '', 'width' => 45],
            ['label' => 'IMEI / Serie:', 'value' => $isPhone ? ($specs['imei'] ?? $this->asset->info['serial_number']) : '', 'width' => 50],
            ['label' => 'Mac Address:', 'value' => $isPhone ? ($this->asset->network['mac_address'] ?? '') : '', 'width' => 50]
        ]);

        $this->fieldRow([
            ['label' => 'Plan Celular:', 'value' => $isPhone ? ($specs['plan'] ?? 'TELCEL PLUS EMP 3') : '', 'width' => 60],
            ['label' => 'Compañía:', 'value' => $isPhone ? ($specs['carrier'] ?? 'TELCEL') : '', 'width' => 40],
            ['label' => 'No. Celular:', 'value' => $isPhone ? ($specs['phone_number'] ?? '') : '', 'width' => 40],
            ['label' => 'SIM:', 'value' => $isPhone ? ($specs['sim'] ?? '') : '', 'width' => 50]
        ]);

        // Accesorios Celular
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 5, 'Accesorios y Estado:', 0, 1);
        $this->SetFont('Arial', '', 8);
        
        $this->checkbox('Cargador', $isPhone); 
        $this->checkbox('Cable USB', $isPhone);
        $this->checkbox('Audífonos', false);
        $this->checkbox('Caja', $isPhone);
        $this->checkbox('Batería', $isPhone);
        
        $this->SetX(140);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode('Condición:'), 0, 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, $isPhone ? strtoupper($this->asset->status) : '', 1, 1, 'C');
        $this->Ln(2);

        $isOther = !$isComputer && !$isPhone;
        
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, utf8_decode('OTROS EQUIPOS'), 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isOther ? $this->asset->info['category'] : '', 'width' => 50],
            ['label' => 'Marca:', 'value' => $isOther ? $this->asset->info['brand'] : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isOther ? $this->asset->info['model'] : '', 'width' => 45],
            ['label' => 'Serial:', 'value' => $isOther ? $this->asset->info['serial_number'] : '', 'width' => 50]
        ]);
        
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 8, utf8_decode('Descripción Adicional:'), 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 8, utf8_decode($isOther ? ($specs['description'] ?? '') : ''), 1, 1);
        $this->Ln(5);

        // --- D. ACUERDO DE RESPONSABILIDAD ---
        $this->sectionTitle('D. ACUERDO DE RESPONSABILIDAD');
        $this->SetFont('Arial', '', 7.5); 
        
        $legalText = "Declaro recibir el/los equipo(s) antes descritos en las condiciones señaladas, los cuales la empresa (Hospitality Services Maya SA de CV) me otorga en condición de préstamo y como herramienta de trabajo, para cumplir con mis funciones operativas dentro de la compañía.\n\n"
        . "Entiendo que el uso del equipo es para fines laborales exclusivamente, así mismo confirmo que son de mi conocimiento las políticas del correcto uso y consumo del plan celular, el cual ha sido asignado por un periodo de 24 meses.\n\n"
        . "En caso de ocurrir algún daño o pérdida del equipo y/o accesorios, notificaré de inmediato al departamento de sistemas, a través de un correo electrónico, y este será revisado internamente.\n\n"
        . "Si se determina que el daño ocasionado es por un acto de negligencia, seré responsable del pago conforme a lo estipulado en la LFT en su ART 110 verificado por el equipo de Finanzas, considerando la devaluación que por uso aplique. El pago de dicha responsabilidad se realizará a través de nómina o en el finiquito correspondiente.\n\n"
        . "El equipo deberá ser devuelto a la compañía en las mismas condiciones que se entregó, solo con el desgaste natural por uso, para poder recibir el siguiente equipo durante el nuevo periodo de asignación, se considera daño físico grave pantalla rota, estrellada y golpes en la carcaza.\n\n"
        . "\"Art 110- El pago de deudas contraídas con el patrón por anticipo de salarios, pagos hechos con exceso al trabajador, errores, pérdidas, averías o adquisición de artículos producidos por la empresa o establecimiento. La cantidad exigible en ningún caso podrá ser mayor del importe de los salarios de un mes y el descuento será al que convengan el trabajador y el patrón, sin que pueda ser mayor del treinta por ciento del excedente del salario mínimo.\"";

        $this->MultiCell(0, 3.5, utf8_decode($legalText));
        $this->Ln(5);

        // Iniciales TM
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, 'Iniciales del TM:', 0, 0);
        $this->Cell(30, 5, '________', 0, 0); 
        $this->Cell(50, 5, 'Firma de HR para Expediente:', 0, 0, 'R');
        $this->Cell(40, 5, '__________________', 0, 1);
        $this->Ln(5);

        // --- E. FIRMAS ---
        $this->sectionTitle('E. FIRMA DE ACEPTACIÓN Y RECEPCIÓN DEL EQUIPO');
        $this->Ln(15);

        $y = $this->GetY();
        $this->Line(30, $y, 90, $y);   
        $this->Line(120, $y, 180, $y); 

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(95, 5, utf8_decode('Recibido por'), 0, 0, 'C');
        $this->Cell(95, 5, utf8_decode('Entregado por'), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 8);
        $this->Cell(95, 5, utf8_decode('Firma Miembro de Equipo'), 0, 0, 'C');
        $this->Cell(95, 5, utf8_decode('Firma IT Cluster Director'), 0, 1, 'C');

        if ($member) {
            $this->Ln(2);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(95, 5, utf8_decode($member->name), 0, 1, 'C');
        }

        return $this->Output('S');
    }

    // --- HELPERS PARA DISEÑO ---

    private function sectionTitle($title)
    {
        $this->SetFillColor(0, 0, 0); 
        $this->SetTextColor(255, 255, 255); 
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, utf8_decode("  $title"), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0); 
        $this->Ln(2);
    }

    private function fieldRow($fields)
    {
        $this->SetFont('Arial', 'B', 8);
        foreach ($fields as $field) {
            $this->Cell($field['width'] * 0.4, 6, utf8_decode($field['label']), 1, 0, 'L', false); 
            $this->SetFont('Arial', '', 8);
            $val = isset($field['value']) ? $field['value'] : '';
            $this->Cell($field['width'] * 0.6, 6, utf8_decode($val), 1, 0, 'L');
            $this->SetFont('Arial', 'B', 8); 
        }
        $this->Ln();
    }

    private function checkbox($label, $checked = false)
    {
        $checkChar = $checked ? 'X' : '';
        $this->Cell(5, 5, $checkChar, 1, 0, 'C');
        $this->Cell(25, 5, utf8_decode($label), 0, 0);
    }

    private function isCategory($keyword)
    {
        return stripos($this->asset->info['category'] ?? '', $keyword) !== false;
    }
}