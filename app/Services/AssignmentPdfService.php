<?php

namespace App\Services;

use \FPDF;
use App\Models\Member;
use Carbon\Carbon;

class AssignmentPdfService extends FPDF
{
    protected $member;

    public function generatePdf(Member $member)
    {
        $this->member = $member;

        // ==========================================
        // 1. DATOS DEL MIEMBRO (EMPLEADO)
        // ==========================================
        $mName  = $this->member->full_name ?? '';
        $mTmId  = $this->member->tm_id ?? '';

        $mPos   = $this->member->position_name ?? 'No especificado';
        $mDep   = $this->member->department_name ?? 'No especificado';

        // LÓGICA DE INICIALES 
        $initials = '';
        if (!empty($mName)) {
            $parts = explode(' ', trim($mName));
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }
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
        $this->Ln(4);

        // A. TIPO DE ENTREGA
        $this->sectionTitle('A. TIPO DE ENTREGA');
        $this->SetFont('Arial', '', 8);

        $this->SetX(15);
        $this->Cell(6, 6, 'X', 1, 0, 'C');
        $this->Cell(28, 6, $this->safeDecode(' Asignación'), 0, 0);
        $this->Cell(6, 6, '', 1, 0, 'C');
        $this->Cell(28, 6, $this->safeDecode(' Préstamo'), 0, 0);
        $this->Cell(6, 6, '', 1, 0, 'C');
        $this->Cell(28, 6, $this->safeDecode(' Devolución'), 0, 0);

        $this->SetX(140);
        $this->Cell(15, 6, 'Fecha:', 0, 0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(40, 6, Carbon::now()->format('d / m / Y'), 1, 1, 'C');
        $this->Ln(2);

        // B. INFORMACIÓN GENERAL
        $this->sectionTitle('B. INFORMACIÓN GENERAL');

        $this->fieldRow([
            ['label' => 'Nombre Completo:', 'value' => $mName, 'width' => 97],
            ['label' => 'NO. Team Member:', 'value' => $mTmId, 'width' => 98],
        ]);

        $this->fieldRow([
            ['label' => 'Posición:', 'value' => $mPos, 'width' => 97],
            ['label' => 'Departamento:', 'value' => $mDep, 'width' => 98]
        ]);
        $this->Ln(2);

        // ==========================================
        // 3. ITERAR SOBRE LOS ACTIVOS DEL USUARIO
        // ==========================================
        $this->sectionTitle('C. DESCRIPCIÓN DE EQUIPO(S)');

        // Traemos los activos del miembro con sus relaciones (categoría y accesorios)
        $assets = $this->member->assets()->with(['category', 'accessories'])->get();

        if ($assets->isEmpty()) {
            $this->SetFont('Arial', 'I', 9);
            $this->Cell(0, 10, 'No hay equipos asignados a este colaborador.', 0, 1, 'C');
        } else {
            foreach ($assets as $asset) {

                $categoryName = strtoupper($asset->category->name ?? 'EQUIPO');
                $specs = $asset->specs ?? [];

                $this->SetFillColor(208, 222, 193);
                $this->SetFont('Arial', 'B', 8);
                $this->Cell(0, 6, $this->safeDecode(" ACTIVO: $categoryName"), 1, 1, 'L', true);

                // FILA 1: Marca, Modelo, Serial
                $this->fieldRow([
                    ['label' => 'Marca:', 'value' => $asset->brand ?? 'N/A', 'width' => 60],
                    ['label' => 'Modelo:', 'value' => $asset->model ?? 'N/A', 'width' => 60],
                    ['label' => 'Serial / IMEI:', 'value' => $asset->serial_number ?? ($specs['imei'] ?? 'N/A'), 'width' => 75]
                ]);

                // FILA 2: Red y Nombres (Depende de si tiene red)
                if ($asset->category && $asset->category->has_network_fields) {
                    $this->fieldRow([
                        ['label' => 'Hilton Name:', 'value' => $asset->hilton_name ?? 'N/A', 'width' => 60],
                        ['label' => 'Mac Address:', 'value' => $asset->mac_address ?? 'N/A', 'width' => 60],
                        ['label' => 'IP Address:', 'value' => $asset->ip_address ?? 'DHCP', 'width' => 75]
                    ]);
                }

                // SI ES CELULAR: Mostrar campos de specs (Plan, Carrier, Línea)
                if (stripos($categoryName, 'PHONE') !== false || stripos($categoryName, 'CELULAR') !== false || stripos($categoryName, 'MOBILE') !== false) {
                    $this->fieldRow([
                        ['label' => 'Línea (Tel):', 'value' => $specs['phone_number'] ?? 'N/A', 'width' => 60],
                        ['label' => 'Compañía:', 'value' => $specs['carrier'] ?? 'N/A', 'width' => 60],
                        ['label' => 'Plan Celular:', 'value' => $specs['plan'] ?? 'N/A', 'width' => 75],
                    ]);

                    
                    $this->fieldRow([
                        ['label' => 'Descripción:', 'value' => $specs['description'] ?? 'N/A', 'width' => 195]
                    ]);
                }

                // ACCESORIOS DE ESTE ACTIVO (Tabla Relacional 'asset_accessories')
                if ($asset->accessories->count() > 0) {
                    $this->SetFont('Arial', 'B', 7);
                    $this->SetFillColor(193, 210, 222);
                    $this->Cell(195, 5, $this->safeDecode('  ACCESORIOS INCLUIDOS PARA ESTE EQUIPO'), 1, 1, 'L', true);

                    $this->SetFont('Arial', 'B', 7);
                    $this->SetFillColor(255, 255, 255); // Fondo blanco
                    $this->Cell(60, 5, 'Tipo de Accesorio', 1, 0, 'C', true);
                    $this->Cell(60, 5, 'Marca', 1, 0, 'C', true);
                    $this->Cell(75, 5, 'Numero de Serie (S/N)', 1, 1, 'C', true);

                    $this->SetFont('Arial', '', 7);

                    foreach ($asset->accessories as $acc) {
                        // Se usa 'L' (Izquierda) o 'C' (Centro) dependiendo de cómo prefieras que se vea
                        $this->Cell(60, 5, $this->safeDecode($acc->type ?? 'N/A'), 1, 0, 'C');
                        $this->Cell(60, 5, $this->safeDecode($acc->brand ?? 'N/A'), 1, 0, 'C');
                        $this->Cell(75, 5, $this->safeDecode($acc->serial_number ?? 'N/A'), 1, 1, 'C');
                    }
                }

                $this->Ln(3); // Espacio entre activos
            }
        }


        // ==========================================
        // 4. ACUERDO DE RESPONSABILIDAD
        // ==========================================
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

        // INICIALES AUTOMÁTICAS
        $textoIniciales = "Iniciales del TM: ______ {$initials} ______";
        $this->Cell(0, 6, $this->safeDecode($textoIniciales), 1, 1, 'L', true);

        $this->Ln(5);

        // ==========================================
        // E. FIRMAS
        // ==========================================
        $this->sectionTitle('E. FIRMA DE ACEPTACION Y RECEPCION DEL EQUIPO');

        // Salto de página preventivo si las firmas quedan cortadas
        if ($this->GetY() > 230) $this->AddPage();

        $y = $this->GetY();
        $boxW = 63;
        $boxH = 20;

        // IT
        $this->SetXY(10, $y);
        $this->Cell($boxW, 5, 'Firma IT Cluster Director', 1, 0, 'C', true);
        $this->SetXY(10, $y + 5);
        $this->Cell($boxW, $boxH, '', 1, 0);

        // MIEMBRO
        $this->SetXY(10 + $boxW + 2, $y);
        $this->Cell($boxW, 5, 'Firma Miembro de Equipo', 1, 0, 'C', true);
        $this->SetXY(10 + $boxW + 2, $y + 5);
        $this->Cell($boxW, $boxH, '', 1, 0);

        // RH
        $this->SetXY(10 + ($boxW * 2) + 4, $y);
        $this->Cell($boxW, 5, 'Firma Recursos Humanos', 1, 0, 'C', true);
        $this->SetXY(10 + ($boxW * 2) + 4, $y + 5);
        $this->Cell($boxW, $boxH, '', 1, 0);

        return $this->Output('S');
    }

    // --- HELPERS SEGUROS ---

    private function safeDecode($text)
    {
        return utf8_decode($text ?? '');
    }

    private function sectionTitle($title)
    {
        $this->SetFillColor(191, 191, 191);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 6, $this->safeDecode(" $title"), 1, 1, 'L', true);
    }

    private function fieldRow($fields)
    {
        $this->SetFont('Arial', 'B', 8);
        $height = 6;
        foreach ($fields as $field) {
            $w = $field['width'];
            $this->SetFont('Arial', 'B', 7);
            $this->Cell($w * 0.35, $height, $this->safeDecode($field['label']), 1, 0, 'L', false);
            $this->SetFont('Arial', '', 7);
            $val = isset($field['value']) ? $field['value'] : '';
            $this->Cell($w * 0.65, $height, $this->safeDecode($val), 1, 0, 'L');
        }
        $this->Ln();
    }
}
