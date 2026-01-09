<?php

namespace App\Services;

use \FPDF; 
use App\Models\Asset;
use Carbon\Carbon;

class AssignmentPdfService extends FPDF
{
    protected $asset;
    protected $info; // Variable de clase para el helper isCategory

    public function generatePdf(Asset $asset)
    {
        $this->asset = $asset;
        
        // ==========================================
        // === BLINDAJE DE DATOS (FIX ERROR 500) ===
        // ==========================================
        
        // 1. Extraemos y aseguramos que sean arrays (si son null, se vuelven [])
        $specs   = $this->asset->specs ?? [];
        $info    = $this->asset->info ?? [];
        $network = $this->asset->network ?? [];
        
        // Guardamos info en la propiedad de clase para que isCategory la pueda usar sin fallar
        $this->info = $info; 

        // ==========================================

        $this->AddPage();
        $this->SetAutoPageBreak(true, 10);
        $this->SetLineWidth(0.2); 

        // LOGO
        $logoPath = public_path('img/hilton_logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 10, 30);
        } else {
            $this->SetFont('Arial', 'I', 8);
            $this->SetXY(15, 10);
            $this->Cell(30, 10, '[Logo]', 1, 0, 'C');
        }

        // CABECERA
        $this->SetFont('Arial', 'B', 16);
        $this->SetY(10); 
        $this->Cell(0, 10, 'HILTON', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $this->safeDecode('CARTA ENTREGA Y CONTROL DE ACTIVOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->safeDecode('CUNQR - Departamento de Sistemas'), 0, 1, 'C');
        $this->Ln(5);

        // A. TIPO DE ENTREGA
        $this->sectionTitle('A. TIPO DE ENTREGA');
        $this->SetFont('Arial', '', 8);
        
        $check = "X"; 
        $this->Cell(5, 5, $check, 1, 0, 'C'); 
        $this->Cell(25, 5, $this->safeDecode('Asignación'), 0, 0);
        $this->Cell(5, 5, '', 1, 0, 'C'); 
        $this->Cell(25, 5, $this->safeDecode('Préstamo'), 0, 0);
        $this->Cell(5, 5, '', 1, 0, 'C'); 
        $this->Cell(25, 5, $this->safeDecode('Devolución'), 0, 0);

        // Fecha
        $this->SetX(130);
        $this->Cell(15, 5, 'Fecha:', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(40, 5, Carbon::now()->format('d / m / Y'), 1, 1, 'C');
        $this->Ln(2);

        // B. INFORMACIÓN GENERAL
        $this->sectionTitle('B. INFORMACIÓN GENERAL');
        $member = $this->asset->member; 

        $pos = $member->corporate_info['position'] ?? '';
        $dep = $member->corporate_info['department'] ?? '';
        $tmId = $member->tm_id ?? '';

        $this->fieldRow([
            ['label' => 'Nombre Completo:', 'value' => $member ? $member->name : '', 'width' => 95],
            ['label' => 'No. Team Member:', 'value' => $tmId, 'width' => 45],
            ['label' => 'Reclutador:', 'value' => '53248', 'width' => 45] 
        ]);

        $this->fieldRow([
            ['label' => 'Posición:', 'value' => $pos, 'width' => 95],
            ['label' => 'Departamento:', 'value' => $dep, 'width' => 95]
        ]);
        $this->Ln(2);

        // C. DESCRIPCIÓN DE EQUIPO
        $isComputer = $this->isCategory('Laptop') || $this->isCategory('Desktop');
        
        $this->sectionTitle('C. DESCRIPCIÓN DE EQUIPO');
        
        // AHORA USAMOS LAS VARIABLES SEGURAS $info y $network
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isComputer ? ($info['category'] ?? '') : '', 'width' => 60],
            ['label' => 'Marca:', 'value' => $isComputer ? ($info['brand'] ?? '') : '', 'width' => 60],
            ['label' => 'Modelo:', 'value' => $isComputer ? ($info['model'] ?? '') : '', 'width' => 70]
        ]);

        $this->fieldRow([
            ['label' => 'Serial:', 'value' => $isComputer ? ($info['serial_number'] ?? '') : '', 'width' => 60],
            ['label' => 'Hilton Name:', 'value' => $isComputer ? ($info['hilton_name'] ?? '') : '', 'width' => 60],
            ['label' => 'Mac Address:', 'value' => $isComputer ? ($network['mac_address'] ?? '') : '', 'width' => 70]
        ]);
        
        // Accesorios
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 5, 'Accesorios Incluidos:', 0, 1);
        $this->SetFont('Arial', '', 8);
        $this->checkbox('Cargador', true); 
        $this->checkbox('Monitor', false);
        $this->checkbox('Teclado', false);
        $this->checkbox('Mouse', false);
        $this->checkbox('Candado', false);
        $this->checkbox('Docking', false);
        $this->Ln(5);

        // SECCIÓN CELULAR
        $isPhone = $this->isCategory('Phone') || $this->isCategory('Celular') || $this->isCategory('Mobile');
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, $this->safeDecode('CELULAR / DISPOSITIVO MÓVIL'), 1, 1, 'L', true);
        
        // USO DE $info Y $network AQUÍ TAMBIÉN
        $this->fieldRow([
            ['label' => 'Marca:', 'value' => $isPhone ? ($info['brand'] ?? '') : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isPhone ? ($info['model'] ?? '') : '', 'width' => 45],
            // Si specs[imei] no existe, intenta usar info[serial_number], si no, vacío.
            ['label' => 'IMEI / Serie:', 'value' => $isPhone ? ($specs['imei'] ?? ($info['serial_number'] ?? '')) : '', 'width' => 50],
            ['label' => 'Mac Address:', 'value' => $isPhone ? ($network['mac_address'] ?? '') : '', 'width' => 50]
        ]);

        $this->fieldRow([
            ['label' => 'Plan Celular:', 'value' => $isPhone ? ($specs['plan'] ?? '') : '', 'width' => 60],
            ['label' => 'Compañía:', 'value' => $isPhone ? ($specs['carrier'] ?? '') : '', 'width' => 40],
            ['label' => 'No. Celular:', 'value' => $isPhone ? ($specs['phone_number'] ?? '') : '', 'width' => 40],
            ['label' => 'SIM:', 'value' => $isPhone ? ($specs['sim'] ?? '') : '', 'width' => 50]
        ]);

        // SECCIÓN OTROS
        $isOther = !$isComputer && !$isPhone;
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, 'OTROS EQUIPOS', 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isOther ? ($info['category'] ?? '') : '', 'width' => 50],
            ['label' => 'Marca:', 'value' => $isOther ? ($info['brand'] ?? '') : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isOther ? ($info['model'] ?? '') : '', 'width' => 45],
            ['label' => 'Serial:', 'value' => $isOther ? ($info['serial_number'] ?? '') : '', 'width' => 50]
        ]);
        
        $this->Ln(5);

        // D. ACUERDO DE RESPONSABILIDAD
        $this->SetFillColor(200, 200, 200);
        $this->sectionTitle('D. ACUERDO DE RESPONSABILIDAD');
        
        $this->SetFont('Arial', 'BI', 8);
        $this->Cell(0, 4, $this->safeDecode('Asignación, Préstamo y/o Devolución.'), 0, 1, 'C');
        
        $this->SetFillColor(204, 221, 238); 
        $this->SetFont('Arial', '', 7); 
        $legalText = "Declaro recibir el/los equipo(s) antes descritos en las condiciones señaladas, los cuales la empresa (Hospitality Services Maya SA de CV) me otorga en condición de préstamo y como herramienta de trabajo, para cumplir con mis funciones operativas dentro de la compañía. Entiendo que el uso del equipo es para fines laborales exclusivamente, así mismo confirmo que son de mi conocimiento las políticas del correcto uso y consumo del plan celular, el cual ha sido asignado por un periodo de 24 meses. En caso de ocurrir algún daño o pérdida del equipo y/o accesorios, notificaré de inmediato al departamento de sistemas, a través de un correo electrónico, y este será revisado internamente.\n\n"
        . "Si se determina que el daño ocasionado es por un acto de negligencia, seré responsable del pago conforme a lo estipulado en la LFT en su ART 110* verificado por el equipo de Finanzas, considerando la devaluación que por uso aplique. El pago de dicha responsabilidad se realizará a través de nómina o en el finiquito correspondiente.\n\n"
        . "El equipo deberá ser devuelto a la compañía en las mismas condiciones que se entregó, solo con el desgaste natural por uso, para poder recibir el siguiente equipo durante el nuevo periodo de asignación, se considera daño físico grave pantalla rota, estrellada y golpes en la carcaza.\n\n"
        . "*Art 110- El pago de deudas contraídas con el patrón por anticipo de salarios, pagos hechos con exceso al trabajador, errores, pérdidas, averías o adquisición de artículos producidos por la empresa o establecimiento. La cantidad exigible en ningún caso podrá ser mayor del importe de los salarios de un mes y el descuento será al que convengan el trabajador y el patrón, sin que pueda ser mayor del treinta por ciento del excedente del salario mínimo.";

        $this->MultiCell(0, 3.5, $this->safeDecode($legalText), 1, 'J', true);
        
        $this->SetFillColor(204, 221, 238);
        $this->Cell(0, 6, $this->safeDecode('Iniciales del TM: ______JH______'), 1, 1, 'L', true);
        
        $this->Ln(2);
        
        // E. FIRMAS
        $this->sectionTitle('E. FIRMA DE ACEPTACION Y RECEPCION DEL EQUIPO');
        
        $boxWidth = 63; 
        $boxHeight = 25; 
        $y = $this->GetY();
        
        // HR
        $this->SetXY(10, $y);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell($boxWidth, 5, $this->safeDecode(''), 1, 0, 'C', true); 
        $this->SetXY(10, $y);
        $this->Cell($boxWidth, 5, $this->safeDecode('Firma de HR para Expediente'), 1, 0, 'C');
        $this->SetXY(10, $y + 5);
        $this->Cell($boxWidth, $boxHeight, '', 1, 0);

        // MIEMBRO
        $this->SetXY(10 + $boxWidth + 2, $y); 
        $this->Cell($boxWidth, 5, $this->safeDecode('Recibido por'), 1, 0, 'C');
        $this->SetXY(10 + $boxWidth + 2, $y + 5);
        $this->Cell($boxWidth, 5, $this->safeDecode('Firma Miembro de Equipo'), 1, 0, 'C', true); 
        $this->SetXY(10 + $boxWidth + 2, $y + 10);
        $this->Cell($boxWidth, $boxHeight - 5, '', 1, 0);

        // IT
        $this->SetXY(10 + ($boxWidth * 2) + 4, $y); 
        $this->Cell($boxWidth, 5, $this->safeDecode('Entregado por'), 1, 0, 'C');
        $this->SetXY(10 + ($boxWidth * 2) + 4, $y + 5);
        $this->Cell($boxWidth, 5, $this->safeDecode('Firma IT Cluster Director'), 1, 0, 'C', true);
        $this->SetXY(10 + ($boxWidth * 2) + 4, $y + 10);
        $this->Cell($boxWidth, $boxHeight - 5, '', 1, 0);

        return $this->Output('S');
    }

    // --- HELPERS SEGUROS ---

    private function safeDecode($text)
    {
        return utf8_decode($text ?? '');
    }

    private function sectionTitle($title)
    {
        $this->SetFillColor(180, 180, 180); 
        $this->SetTextColor(0, 0, 0); 
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->safeDecode(" $title"), 1, 1, 'L', true);
        $this->SetTextColor(0, 0, 0); 
    }

    private function fieldRow($fields)
    {
        $this->SetFont('Arial', 'B', 8);
        foreach ($fields as $field) {
            $this->Cell($field['width'] * 0.4, 6, $this->safeDecode($field['label']), 1, 0, 'L', false); 
            $this->SetFont('Arial', '', 8);
            $val = isset($field['value']) ? $field['value'] : '';
            $this->Cell($field['width'] * 0.6, 6, $this->safeDecode($val), 1, 0, 'L');
            $this->SetFont('Arial', 'B', 8); 
        }
        $this->Ln();
    }

    private function checkbox($label, $checked = false)
    {
        $checkChar = $checked ? 'X' : '';
        $this->Cell(5, 5, $checkChar, 1, 0, 'C');
        $this->Cell(25, 5, $this->safeDecode($label), 0, 0);
    }

    private function isCategory($keyword)
    {
        // BLINDAJE AQUÍ TAMBIÉN: Usamos la variable de clase segura
        return stripos($this->info['category'] ?? '', $keyword) !== false;
    }
}