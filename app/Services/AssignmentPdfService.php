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
        
        // 1. BLINDAJE DE DATOS (Specs)
        $specs = $this->asset->specs ?? [];

        $this->AddPage();
        $this->SetAutoPageBreak(true, 10);
        $this->SetLineWidth(0.2); 

        // 2. BLINDAJE DE IMAGEN (Evita error 500 si no hay logo)
        $logoPath = public_path('img/hilton_logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 10, 30);
        } else {
            // Si no hay logo, ponemos texto para que no truene
            $this->SetFont('Arial', 'I', 8);
            $this->SetXY(15, 10);
            $this->Cell(30, 10, '[Sin Logo]', 1, 0, 'C');
        }

        // --- CABECERA ---
        $this->SetFont('Arial', 'B', 16);
        $this->SetY(10); // Aseguramos posición Y
        $this->Cell(0, 10, 'HILTON', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $this->safeDecode('CARTA ENTREGA Y CONTROL DE ACTIVOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->safeDecode('CUNQR - Departamento de Sistemas'), 0, 1, 'C');
        $this->Ln(5);

        // --- A. TIPO DE ENTREGA ---
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

        // --- B. INFORMACIÓN GENERAL ---
        $this->sectionTitle('B. INFORMACIÓN GENERAL');
        $member = $this->asset->assigned_to;

        // Protección contra nulos en corporate_info
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

        // --- C. DESCRIPCIÓN ---
        $isComputer = $this->isCategory('Laptop') || $this->isCategory('Desktop');
        
        $this->sectionTitle('C. DESCRIPCIÓN DE EQUIPO');
        
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

        // --- SECCIÓN CELULAR ---
        $isPhone = $this->isCategory('Phone') || $this->isCategory('Celular') || $this->isCategory('Mobile');
        
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, $this->safeDecode('CELULAR / DISPOSITIVO MÓVIL'), 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Marca:', 'value' => $isPhone ? $this->asset->info['brand'] : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isPhone ? $this->asset->info['model'] : '', 'width' => 45],
            ['label' => 'IMEI / Serie:', 'value' => $isPhone ? ($specs['imei'] ?? $this->asset->info['serial_number']) : '', 'width' => 50],
            ['label' => 'Mac Address:', 'value' => $isPhone ? ($this->asset->network['mac_address'] ?? '') : '', 'width' => 50]
        ]);

        $this->fieldRow([
            ['label' => 'Plan Celular:', 'value' => $isPhone ? ($specs['plan'] ?? '') : '', 'width' => 60],
            ['label' => 'Compañía:', 'value' => $isPhone ? ($specs['carrier'] ?? '') : '', 'width' => 40],
            ['label' => 'No. Celular:', 'value' => $isPhone ? ($specs['phone_number'] ?? '') : '', 'width' => 40],
            ['label' => 'SIM:', 'value' => $isPhone ? ($specs['sim'] ?? '') : '', 'width' => 50]
        ]);

        // --- SECCIÓN OTROS ---
        $isOther = !$isComputer && !$isPhone;
        
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, 'OTROS EQUIPOS', 1, 1, 'L', true);
        
        $this->fieldRow([
            ['label' => 'Tipo Equipo:', 'value' => $isOther ? $this->asset->info['category'] : '', 'width' => 50],
            ['label' => 'Marca:', 'value' => $isOther ? $this->asset->info['brand'] : '', 'width' => 45],
            ['label' => 'Modelo:', 'value' => $isOther ? $this->asset->info['model'] : '', 'width' => 45],
            ['label' => 'Serial:', 'value' => $isOther ? $this->asset->info['serial_number'] : '', 'width' => 50]
        ]);
        
        $this->Ln(5);
        
        // FIRMAS
        $this->sectionTitle('FIRMAS');
        $this->Ln(15);
        $y = $this->GetY();
        $this->Line(30, $y, 90, $y);   
        $this->Line(120, $y, 180, $y); 
        $this->SetFont('Arial', '', 8);
        $this->Cell(95, 5, 'Firma Usuario', 0, 0, 'C');
        $this->Cell(95, 5, 'Firma IT', 0, 1, 'C');

        return $this->Output('S');
    }

    // --- HELPERS SEGUROS ---

    private function safeDecode($text)
    {
        return utf8_decode($text ?? '');
    }

    private function sectionTitle($title)
    {
        $this->SetFillColor(0, 0, 0); 
        $this->SetTextColor(255, 255, 255); 
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, $this->safeDecode("  $title"), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0); 
        $this->Ln(2);
    }

    private function fieldRow($fields)
    {
        $this->SetFont('Arial', 'B', 8);
        foreach ($fields as $field) {
            $this->Cell($field['width'] * 0.4, 6, $this->safeDecode($field['label']), 1, 0, 'L', false); 
            $this->SetFont('Arial', '', 8);
            // Validamos que 'value' no sea null
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
        return stripos($this->asset->info['category'] ?? '', $keyword) !== false;
    }
}