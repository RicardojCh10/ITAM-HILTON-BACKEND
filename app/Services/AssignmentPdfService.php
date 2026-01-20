<?php

namespace App\Services;

use \FPDF;
use App\Models\Asset;
use Carbon\Carbon;

class AssignmentPdfService extends FPDF
{
    protected $asset;

    public function generatePdf(Asset $asset)
    {
        $this->asset = $asset;
        
        // ==========================================
        // 1. MAPEO DE DATOS (FIX DE CAMPOS VACÍOS)
        // ==========================================
        
        // Datos del Activo
        $specs    = is_string($this->asset->specs) ? json_decode($this->asset->specs, true) : ($this->asset->specs ?? []);
        $category = $this->asset->category ?? '';
        $brand    = $this->asset->brand ?? '';
        $model    = $this->asset->model ?? '';
        $serial   = $this->asset->serial_number ?? '';
        $hilton   = $this->asset->hilton_name ?? '';
        $mac      = $this->asset->mac_address ?? '';

        // Datos del Miembro
        $member = $this->asset->member; 
        $mName  = $member->full_name ?? '';
        $mTmId  = $member->tm_id ?? '';
        $mPos   = $member->position ?? '';    // Columna directa
        $mDep   = $member->department ?? '';  // Columna directa

        // LÓGICA DE INICIALES 
        $initials = '';
        if (!empty($mName)) {
            $parts = explode(' ', trim($mName));
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }
            // Limitamos a 2 o 3 letras por estética
            $initials = substr($initials, 0, 3);
        }

        // ==========================================
        // 2. CONFIGURACIÓN VISUAL (TAMAÑO CARTA)
        // ==========================================
        $this->AddPage('P', 'Letter'); 
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 10);
        $this->SetLineWidth(0.2); 

        // LOGO
        $logoPath = public_path('img/hilton_logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 12, 10, 25);
        } else {
            $this->SetXY(12, 10);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(25, 10, '[Logo]', 1, 0, 'C');
        }

        // CABECERA
        $this->SetFont('Arial', 'B', 16);
        $this->SetY(12); 
        $this->Cell(0, 8, 'HILTON', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, $this->safeDecode('CARTA ENTREGA Y CONTROL DE ACTIVOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->safeDecode('CUNQR - Departamento de Sistemas'), 0, 1, 'C');
        $this->Ln(6);

        // A. TIPO DE ENTREGA
        $this->sectionTitle('A. TIPO DE ENTREGA');
        $this->SetFont('Arial', '', 9);
        
        $this->SetX(15);
        $this->Cell(6, 6, 'X', 1, 0, 'C'); 
        $this->Cell(28, 6, $this->safeDecode(' Asignación'), 0, 0);
        $this->Cell(6, 6, '', 1, 0, 'C'); 
        $this->Cell(28, 6, $this->safeDecode(' Préstamo'), 0, 0);
        $this->Cell(6, 6, '', 1, 0, 'C'); 
        $this->Cell(28, 6, $this->safeDecode(' Devolución'), 0, 0);

        $this->SetX(140);
        $this->Cell(15, 6, 'Fecha:', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(40, 6, Carbon::now()->format('d / m / Y'), 1, 1, 'C');
        $this->Ln(8);

        // B. INFORMACIÓN GENERAL
        $this->sectionTitle('B. INFORMACIÓN GENERAL');
        
        // Usamos las variables mapeadas arriba ($mName, $mPos, etc.)
        $this->fieldRow([
            ['label' => 'Nombre Completo:', 'value' => $mName, 'width' => 100],
            ['label' => 'NO. Team Member:', 'value' => $mTmId, 'width' => 90],
        ]);

        $this->fieldRow([
            ['label' => 'Posición:', 'value' => $mPos, 'width' => 97],
            ['label' => 'Departamento:', 'value' => $mDep, 'width' => 98]
        ]);
        $this->Ln(3);

        // C. DESCRIPCIÓN DE EQUIPO
        $isComputer = $this->isCategory('Laptop') || $this->isCategory('Desktop');
        $this->sectionTitle('C. DESCRIPCIÓN DE EQUIPO');
        
        // Usamos las variables mapeadas ($category, $brand, etc.)
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isComputer ? $category : '', 'width' => 60],
            ['label' => 'Marca:', 'value' => $isComputer ? $brand : '', 'width' => 60],
            ['label' => 'Modelo:', 'value' => $isComputer ? $model : '', 'width' => 75]
        ]);

        $this->fieldRow([
            ['label' => 'Serial:', 'value' => $isComputer ? $serial : '', 'width' => 60],
            ['label' => 'Hilton Name:', 'value' => $isComputer ? $hilton : '', 'width' => 60],
            ['label' => 'Mac Address:', 'value' => $isComputer ? $mac : '', 'width' => 75]
        ]);
        
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 5, 'Accesorios Incluidos:', 0, 1);
        
        $chkY = $this->GetY();
        $this->SetXY(10, $chkY);   $this->checkbox('Cargador', true); 
        $this->SetXY(45, $chkY);   $this->checkbox('Monitor', false);
        $this->SetXY(80, $chkY);   $this->checkbox('Teclado', false);
        $this->SetXY(115, $chkY);  $this->checkbox('Mouse', false);
        $this->SetXY(150, $chkY);  $this->checkbox('Candado', false);
        $this->SetXY(180, $chkY);  $this->checkbox('Docking', false);
        $this->Ln(8);

        // SECCIÓN CELULAR
        $isPhone = $this->isCategory('Phone') || $this->isCategory('Celular') || $this->isCategory('Mobile');
        $this->SetFillColor(240, 240, 240);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, $this->safeDecode('CELULAR / DISPOSITIVO MÓVIL'), 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Marca:', 'value' => $isPhone ? $brand : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isPhone ? $model : '', 'width' => 50],
            ['label' => 'IMEI / Serie:', 'value' => $isPhone ? ($specs['imei'] ?? $serial) : '', 'width' => 50],
            ['label' => 'Mac Address:', 'value' => $isPhone ? $mac : '', 'width' => 50]
        ]);

        $this->fieldRow([
            ['label' => 'Plan Celular:', 'value' => $isPhone ? ($specs['plan'] ?? '') : '', 'width' => 65],
            ['label' => 'Compañía:', 'value' => $isPhone ? ($specs['carrier'] ?? '') : '', 'width' => 40],
            ['label' => 'No. Celular:', 'value' => $isPhone ? ($specs['phone_number'] ?? '') : '', 'width' => 40],
            ['label' => 'SIM:', 'value' => $isPhone ? ($specs['sim'] ?? '') : '', 'width' => 50]
        ]);
        $this->Ln(2);

        // SECCIÓN OTROS
        $isOther = !$isComputer && !$isPhone;
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, 'OTROS EQUIPOS', 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isOther ? $category : '', 'width' => 50],
            ['label' => 'Marca:', 'value' => $isOther ? $brand : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isOther ? $model : '', 'width' => 45],
            ['label' => 'Serial:', 'value' => $isOther ? $serial : '', 'width' => 55]
        ]);
        $this->Ln(5);

        // D. ACUERDO DE RESPONSABILIDAD
        $this->sectionTitle('D. ACUERDO DE RESPONSABILIDAD');

        $this->SetFillColor(204, 221, 238); 
        $this->SetFont('Arial', '', 7); 
        $legalText = "Declaro recibir el/los equipo(s) antes descritos en las condiciones señaladas, los cuales la empresa (Hospitality Services Maya SA de CV) me otorga en condición de préstamo y como herramienta de trabajo, para cumplir con mis funciones operativas dentro de la compañía. Entiendo que el uso del equipo es para fines laborales exclusivamente, así mismo confirmo que son de mi conocimiento las políticas del correcto uso y consumo del plan celular, el cual ha sido asignado por un periodo de 24 meses. En caso de ocurrir algún daño o pérdida del equipo y/o accesorios, notificaré de inmediato al departamento de sistemas, a través de un correo electrónico, y este será revisado internamente.\n\n"
        . "Si se determina que el daño ocasionado es por un acto de negligencia, seré responsable del pago conforme a lo estipulado en la LFT en su ART 110* verificado por el equipo de Finanzas, considerando la devaluación que por uso aplique. El pago de dicha responsabilidad se realizará a través de nómina o en el finiquito correspondiente.\n\n"
        . "El equipo deberá ser devuelto a la compañía en las mismas condiciones que se entregó, solo con el desgaste natural por uso, para poder recibir el siguiente equipo durante el nuevo periodo de asignación, se considera daño físico grave pantalla rota, estrellada y golpes en la carcaza.\n\n"
        . "*Art 110- El pago de deudas contraídas con el patrón por anticipo de salarios, pagos hechos con exceso al trabajador, errores, pérdidas, averías o adquisición de artículos producidos por la empresa o establecimiento. La cantidad exigible en ningún caso podrá ser mayor del importe de los salarios de un mes y el descuento será al que convengan el trabajador y el patrón, sin que pueda ser mayor del treinta por ciento del excedente del salario mínimo.";


        $this->MultiCell(0, 3.5, $this->safeDecode($legalText), 1, 'J', false);
        
        $this->SetFont('Arial', 'B', 8);
        $this->Ln(2);
        
        // AQUI PONEMOS LAS INICIALES AUTOMATICAS
        $textoIniciales = "Iniciales del TM: ______ {$initials} ______";
        $this->Cell(0, 6, $this->safeDecode($textoIniciales), 1, 1, 'L', true);
        
        $this->Ln(5);

        // E. FIRMAS
        $this->sectionTitle('E. FIRMA DE ACEPTACION Y RECEPCION DEL EQUIPO');
        
        if ($this->GetY() > 240) $this->AddPage(); 
        
        $y = $this->GetY();
        $boxW = 63;
        $boxH = 20;

        
        // IT
        $this->SetXY(10, $y);
        $this->Cell($boxW, 5, 'Firma IT Cluster Director', 1, 0, 'C', true);
        $this->SetXY(10, $y+5);
        $this->Cell($boxW, $boxH, '', 1, 0);

        // MIEMBRO
        $this->SetXY(10 + $boxW + 2, $y);
        $this->Cell($boxW, 5, 'Firma Miembro de Equipo', 1, 0, 'C', true);
        $this->SetXY(10 + $boxW + 2, $y+5);
        $this->Cell($boxW, $boxH, '', 1, 0);

        // RH
        $this->SetXY(10 + ($boxW*2) + 4, $y);
        $this->Cell($boxW, 5, 'Firma Recursos Humanos', 1, 0, 'C', true);
        $this->SetXY(10 + ($boxW*2) + 4, $y+5);
        $this->Cell($boxW, $boxH, '', 1, 0);

        return $this->Output('S');
    }

    // --- HELPERS SEGUROS ---

    private function safeDecode($text) {
        return utf8_decode($text ?? '');
    }

    private function sectionTitle($title) {
        $this->SetFillColor(200, 200, 200); 
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, $this->safeDecode(" $title"), 1, 1, 'L', true);
    }

    private function fieldRow($fields) {
        $this->SetFont('Arial', 'B', 8);
        $height = 6;
        foreach ($fields as $field) {
            $w = $field['width'];
            $this->SetFont('Arial', 'B', 7);
            $this->Cell($w * 0.35, $height, $this->safeDecode($field['label']), 1, 0, 'L', false); 
            $this->SetFont('Arial', '', 8);
            $val = isset($field['value']) ? $field['value'] : '';
            $this->Cell($w * 0.65, $height, $this->safeDecode($val), 1, 0, 'L');
        }
        $this->Ln();
    }

    private function checkbox($label, $checked = false) {
        $this->SetFont('Arial', '', 8);
        $checkChar = $checked ? 'X' : '';
        $this->Cell(5, 5, $checkChar, 1, 0, 'C');
        $this->Cell(20, 5, $this->safeDecode($label), 0, 0);
    }

    private function isCategory($keyword) {
        $cat = $this->asset->category ?? '';
        return stripos($cat, $keyword) !== false;
    }
}