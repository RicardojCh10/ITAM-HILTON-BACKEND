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

        // ==========================================
        // LOGOS
        // ==========================================
        $logoWidth = 25;

        // 1. LOGO IZQUIERDO
        $logoPathLeft = public_path('img/CUNQR_Logo.png');
        if (file_exists($logoPathLeft)) {
            $this->Image($logoPathLeft, 10, 10, $logoWidth);
        } else {
            $this->SetXY(10, 10);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell($logoWidth, 10, '[Logo Izq]', 1, 0, 'C');
        }

        // 2. LOGO DERECHO
        $logoPathRight = public_path('img/CUNWA_Logo.png');

        $pageWidth = $this->GetPageWidth();
        $rightX = $pageWidth - 10 - $logoWidth;

        if (file_exists($logoPathRight)) {
            $this->Image($logoPathRight, $rightX, 10, $logoWidth);
        } else {
            $this->SetXY($rightX, 10);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell($logoWidth, 10, '[Logo Der]', 1, 0, 'C');
        }

        $logoWidth = 25;
        $pageWidth = $this->GetPageWidth();

        $startX = 10 + $logoWidth + 2;

        $middleWidth = $pageWidth - ($startX * 2);

        $this->SetY(12);

        $this->SetFont('Arial', 'B', 11);
        $this->SetX($startX);
        $this->MultiCell($middleWidth, 5, "HILTON CANCUN, AN ALL-INCLUSIVE RESORT \n& WALDORF ASTORIA RIVIERA MAYA", 0, 'C');

        // 3. Subtítulos
        $this->SetFont('Arial', 'B', 9);
        $this->SetX($startX);
        $this->Cell($middleWidth, 5, $this->safeDecode('CARTA ENTREGA Y CONTROL DE ACTIVOS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetX($startX);
        $this->Cell($middleWidth, 5, $this->safeDecode('Departamento de Tecnologías de Información'), 0, 1, 'C');

        $this->Ln(2);

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

        $legalText = "En caso de daño, pérdida o avería del equipo y/o accesorios asignados, EL TRABAJADOR se obliga a notificar de inmediato al departamento correspondiente, a efecto de que la empresa lleve a cabo la investigación interna para determinar las circunstancias de tiempo, modo y lugar, así como la posible responsabilidad.\n\n"
            . "En caso de que se determine, mediante dicho procedimiento, que el daño es imputable al TRABAJADOR por negligencia, uso indebido o dolo, éste acepta y autoriza expresamente que el monto correspondiente pueda ser recuperado por la empresa mediante descuentos a su salario, en términos de lo dispuesto por el artículo 110, fracción I, de la Ley Federal del Trabajo.\n\n"
            . "Para tales efectos, las partes acuerdan que:\n"
            . "a) El monto total del adeudo no podrá exceder del importe equivalente a un mes de salario del TRABAJADOR.\n"
            . "b) Los descuentos que en su caso se realicen no podrán exceder del treinta por ciento del excedente del salario mínimo vigente.\n"
            . "c) Todo descuento deberá contar con la aceptación expresa y por escrito del TRABAJADOR.\n"
            . "d) En caso de terminación de la relación laboral, el saldo pendiente podrá ser descontado del finiquito o liquidación correspondiente, en los términos permitidos por la legislación laboral aplicable.\n\n"
            . "Lo anterior sin perjuicio del derecho del TRABAJADOR de manifestar lo que a su interés convenga dentro del procedimiento interno correspondiente.";

        $this->MultiCell(0, 3, $this->safeDecode($legalText), 1, 'J', false);

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
