<?php

namespace App\Services;

use \FPDF;
use App\Models\Member;
use Carbon\Carbon;

class AccessControlPdfService extends FPDF
{
    protected $member;

    public function generatePdf(Member $member)
    {
        $this->member = $member;

        // ==========================================
        // 1. DATOS DEL MIEMBRO
        // ==========================================
        $mName  = $this->member->full_name ?? '';
        $mTmId  = $this->member->tm_id ?? '';
        $mPos   = $this->member->position_name ?? 'No especificado';
        $mDep   = $this->member->department_name ?? 'No especificado';

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
        // 2. CONFIGURACIÓN VISUAL (ESTILO EXCEL AZUL)
        // ==========================================
        $this->AddPage('P', 'Letter');
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 10);
        
        // COLORES CORPORATIVOS
        // Borde Azul Marino (Hilton Blue)
        $this->SetDrawColor(31, 73, 125); 
        $this->SetLineWidth(0.3); // Borde ligeramente más remarcado

        // LOGO
        $logoPath = public_path('img/hilton_logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 10, 28);
        }

        // CABECERA PRINCIPAL
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(31, 73, 125); // Texto Azul
        $this->SetY(12);
        $this->Cell(0, 6, 'USER ACCESS REQUEST', 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(80, 80, 80); // Gris oscuro
        $this->Cell(0, 5, $this->safeDecode('CARTA DE RESPONSIVA Y CONTROL DE ACCESOS'), 0, 1, 'C');
        $this->Ln(3);

        // Reset Color Texto a Negro para el resto del doc
        $this->SetTextColor(0, 0, 0);

        // BLOQUE SOX COMPLIANCE
        $this->SetFillColor(230, 240, 250); // Azul muy claro
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 6, ' IT Risk Control Matrix - Sox Compliance', 1, 1, 'L', true);
        
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 5, " C10 - Access to critical systems and applications require user ID's and passwords.", 'LR', 1, 'L');
        $this->Cell(0, 5, $this->safeDecode(" C10 Acceso a sistemas y aplicaciones críticas requieren el uso de ID de usuario y contraseñas."), 'LRB', 1, 'L');
        $this->Ln(2);

        // BLOQUE DE HOTEL / REQUEST TYPE
        $this->SetFont('Arial', 'B', 7);
        $propertyName = $this->member->property->name ?? 'Hilton';
        
        $this->Cell(20, 6, ' HOTEL:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 7);
        $this->Cell(85, 6, " " . $this->safeDecode($propertyName), 1, 0, 'L');
        
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 6, ' REQUEST TYPE:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 7);
        $this->Cell(60, 6, " [ X ] HIRING", 1, 1, 'C');
        $this->Ln(2);

        // ==========================================
        // 3. INFORMACIÓN DEL COLABORADOR (GRID)
        // ==========================================
        $this->sectionTitle('A. DATOS DEL COLABORADOR');
        
        // Fila 1
        $this->fieldRow([
            ['label' => 'Requested Date:', 'value' => Carbon::now()->format('d-M-Y'), 'width' => 97.5],
            ['label' => 'Team Member Number:', 'value' => $mTmId, 'width' => 97.5]
        ]);
        // Fila 2
        $this->fieldRow([
            ['label' => 'Team Member Name:', 'value' => $mName, 'width' => 195]
        ]);
        // Fila 3
        $this->fieldRow([
            ['label' => 'Department:', 'value' => $mDep, 'width' => 97.5],
            ['label' => 'Position:', 'value' => $mPos, 'width' => 97.5]
        ]);
       
        $this->Ln(2);

        // ==========================================
        // 4. MATRIZ DE PLATAFORMAS Y PERMISOS
        // ==========================================

        $this->sectionTitle('B. MATRIZ DE ACCESOS AUTORIZADOS A SISTEMAS');
        $this->Ln(1);


        $groupedPermissions = $this->member->platformPermissions->groupBy(function ($perm) {
            return $perm->platform->name ?? 'Other Applications';
        });

        if ($groupedPermissions->isEmpty()) {
            $this->SetFont('Arial', 'I', 7);
            $this->Cell(0, 10, 'No access to IT systems requested or assigned.', 1, 1, 'C');
        } else {
            $hasOverrides = false;

            // Anchos de las columnas principales
            $colPlatformW = 60;
            $colPermsW = 135;

            // Encabezados de la Matriz
            $this->SetFillColor(31, 73, 125); // Azul Fuerte
            $this->SetTextColor(255, 255, 255); // Texto Blanco
            $this->SetFont('Arial', 'B', 8);
            
            $this->Cell($colPlatformW, 6, ' PLATAFORMA / SISTEMA', 1, 0, 'C', true);
            $this->Cell($colPermsW, 6, ' NIVELES DE ACCESO (ROLES ASIGNADOS)', 1, 1, 'C', true);
            
            $this->SetTextColor(0, 0, 0);

            foreach ($groupedPermissions as $platformName => $permissions) {
                
                $this->SetFont('Arial', '', 6); 
                $badgePadding = 4; 
                $gap = 2; 
                $lineHeight = 5;
                
                $simX = 0;
                $simY = 0;
                
                foreach ($permissions as $perm) {
                    $roleName = trim($perm->name);
                    if ($perm->pivot->is_override) { $roleName .= ' (*)'; }
                    
                    $w = $this->GetStringWidth($this->safeDecode($roleName)) + $badgePadding;
                    
                    if (($simX + $w) > ($colPermsW - 4)) {
                        $simX = 0;
                        $simY += $lineHeight;
                    }
                    $simX += $w + $gap;
                }
                
                $rowHeight = $simY + $lineHeight + 4; 

                if ($this->GetY() + $rowHeight > 255) {
                    $this->AddPage();
                }

                $xStart = $this->GetX();
                $yStart = $this->GetY();

                // 2. PLATAFORMA
                $this->SetFillColor(240, 245, 250);
                $this->SetFont('Arial', 'B', 7);
                $this->Rect($xStart, $yStart, $colPlatformW, $rowHeight, 'DF'); 
                
                // Centrar verticalmente el nombre de la plataforma
                $this->SetXY($xStart, $yStart + ($rowHeight / 2) - 2); 
                $this->Cell($colPlatformW, 4, " " . strtoupper($this->safeDecode($platformName)), 0, 0, 'L');

                // 3. DIBUJAR CONTENEDOR DE PERMISOS
                $this->Rect($xStart + $colPlatformW, $yStart, $colPermsW, $rowHeight, 'D');

                // 4. DIBUJAR LAS ETIQUETAS
                $currX = $xStart + $colPlatformW + 2; 
                $currY = $yStart + 2;

                $this->SetFont('Arial', '', 6);

                foreach ($permissions as $perm) {
                    $isOverride = $perm->pivot->is_override;
                    $roleName = trim($perm->name);
                    
                    if ($isOverride) {
                        $roleName .= ' (*)';
                        $hasOverrides = true;
                    }
                    
                    $w = $this->GetStringWidth($this->safeDecode($roleName)) + $badgePadding;
                    
                    // Salto de línea real
                    if (($currX + $w) > ($xStart + $colPlatformW + $colPermsW - 2)) {
                        $currX = $xStart + $colPlatformW + 2;
                        $currY += $lineHeight;
                    }
                    
                    $this->SetXY($currX, $currY);
                    
                    // Colores de la etiqueta
                    if ($isOverride) {
                        $this->SetFillColor(255, 235, 235); // Fondo rojizo para excepciones
                        $this->SetTextColor(180, 0, 0); // Letra roja
                        $this->SetDrawColor(255, 180, 180); // Borde rojo
                        $this->SetFont('Arial', 'B', 7); // Negrita
                    } else {
                        $this->SetFillColor(245, 245, 245); // Fondo gris estándar
                        $this->SetTextColor(50, 50, 50); // Letra gris oscuro
                        $this->SetDrawColor(200, 200, 200); // Borde gris claro
                        $this->SetFont('Arial', '', 7); // Normal
                    }
                    
                    // Dibujar la etiqueta individual
                    $this->Cell($w, 4.5, $this->safeDecode($roleName), 1, 0, 'C', true);
                    
                    // Mover X para la siguiente etiqueta
                    $currX += $w + $gap;
                }

                $this->SetDrawColor(31, 73, 125); 
                $this->SetTextColor(0, 0, 0);

                $this->SetY($yStart + $rowHeight); 
            }

            // LEYENDA DE AUDITORÍA
            if ($hasOverrides) {
                $this->Ln(1);
                $this->SetFont('Arial', 'B', 7);
                $this->SetTextColor(200, 50, 50); // Rojo auditoría
                $this->Cell(0, 2, $this->safeDecode('(*) EXCEPCION APROBADA'), 0, 1, 'R');
                $this->SetTextColor(0, 0, 0);
            }
        }
        $this->Ln(3);

        // ==========================================
        // 5. ACUERDO DE RESPONSABILIDAD
        // ==========================================

        $this->sectionTitle('C. ACUERDO DE RESPONSABILIDAD DE CREDENCIALES');

        $xStart = $this->GetX();
        $yStart = $this->GetY();
        
        $this->SetXY($xStart + 2, $yStart + 2);

        $this->SetFont('Arial', '', 7);
        $this->Write(5, "Yo, ");

        $this->SetFont('Arial', 'B', 7);
        $this->SetTextColor(0,0,0); 
        $this->Write(5, $this->safeDecode($mName));

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(0, 0, 0);
        $legalText = ", he recibido y reconozco la responsabilidad de las credenciales de acceso que se me han entregado. Asimismo, acepto el contenido de las politicas de operacion y de tecnologias de informacion (ITAM / SOX Compliance) para los miembros de equipo, obligandome a no compartir mis contrasenas y hacer un uso adecuado y etico de los recursos informaticos de la compania.";
        $this->Write(5, $this->safeDecode($legalText));

        $yEnd = $this->GetY() + 4 + 2; 
        
        $this->SetDrawColor(31, 73, 125); // Borde azul marino
        $this->Rect($xStart, $yStart, 195, $yEnd - $yStart, 'D'); 

        $this->SetY($yEnd);
        
        $this->Ln(3);

        // ==========================================
        // 6. FIRMAS DE AUTORIZACIÓN (TIPO CELDAS EXCEL)
        // ==========================================
        $this->sectionTitle('D. FIRMAS Y AUTORIZACIONES');

         $this->Ln(1);

        if ($this->GetY() > 220) {
            $this->AddPage();
            $this->SetY(30);
        }

        // Matemáticas para 4 cajas exactas
        $boxW = 45; 
        $spacing = 5; // Espacio entre cajas
        $hHeader = 6;
        $hSign = 16;
        $hFooter = 6;
        
        $col1 = 10;
        $col2 = $col1 + $boxW + $spacing;
        $col3 = $col2 + $boxW + $spacing;
        $col4 = $col3 + $boxW + $spacing;

        // FILA 1: Títulos de las Firmas (Cabeceras azules)
        $this->SetFillColor(230, 240, 250);
        $this->SetFont('Arial', 'B', 8);
        
        $this->SetXY($col1, $this->GetY());
        $this->Cell($boxW, $hHeader, 'Human Resources', 1, 0, 'C', true);
        $this->SetXY($col2, $this->GetY());
        $this->Cell($boxW, $hHeader, 'Head of Department', 1, 0, 'C', true);
        $this->SetXY($col3, $this->GetY());
        $this->Cell($boxW, $hHeader, 'Information Technology', 1, 0, 'C', true);
        $this->SetXY($col4, $this->GetY());
        $this->Cell($boxW, $hHeader, 'Team Member', 1, 1, 'C', true);

        // FILA 2: Espacio blanco para la firma física
        $ySign = $this->GetY();
        $this->SetXY($col1, $ySign);
        $this->Cell($boxW, $hSign, '', 'LR', 0); // Solo bordes izquierdo y derecho
        $this->SetXY($col2, $ySign);
        $this->Cell($boxW, $hSign, '', 'LR', 0);
        $this->SetXY($col3, $ySign);
        $this->Cell($boxW, $hSign, '', 'LR', 0);
        $this->SetXY($col4, $ySign);
        $this->Cell($boxW, $hSign, '', 'LR', 1);

        // FILA 3: Firmas y fechas
        $yFoot = $this->GetY();
        $this->SetFont('Arial', '', 6);
        $this->SetTextColor(150, 150, 150);

        $this->SetXY($col1, $yFoot);
        $this->Cell($boxW, $hFooter, 'Firma y Fecha', 'LRB', 0, 'C'); 
        $this->SetXY($col2, $yFoot);
        $this->Cell($boxW, $hFooter, 'Firma y Fecha', 'LRB', 0, 'C');
        $this->SetXY($col3, $yFoot);
        $this->Cell($boxW, $hFooter, 'Firma y Fecha', 'LRB', 0, 'C');
        $this->SetXY($col4, $yFoot);
        $this->Cell($boxW, $hFooter, 'Firma y Fecha', 'LRB', 1, 'C');

        return $this->Output('S');
    }

    // --- HELPERS ESTRUCTURALES ---

    private function safeDecode($text)
    {
        return utf8_decode($text ?? '');
    }

    private function sectionTitle($title)
    {
        $this->SetFillColor(31, 73, 125); // Azul Marino
        $this->SetTextColor(255, 255, 255); // Texto Blanco
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 6, $this->safeDecode(" $title"), 1, 1, 'L', true);
        $this->SetTextColor(0, 0, 0); // Regresar a negro
    }

    private function fieldRow($fields)
    {
        $height = 6;
        $this->SetFillColor(230, 240, 250); // Azul clarito para los labels
        
        foreach ($fields as $field) {
            $w = $field['width'];
            
            // Label (Gris/Azul con borde)
            $this->SetFont('Arial', 'B', 7);
            $this->Cell($w * 0.35, $height, $this->safeDecode($field['label']), 1, 0, 'L', true);
            
            // Valor (Blanco con borde)
            $this->SetFont('Arial', '', 7);
            $val = isset($field['value']) ? $field['value'] : '';
            $this->Cell($w * 0.65, $height, " " . $this->safeDecode($val), 1, 0, 'L');
        }
        $this->Ln();
    }
}