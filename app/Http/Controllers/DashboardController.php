<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getMasterMetrics(Request $request)
    {
        $now = Carbon::now();

        // ==========================================
        // 1. OVERVIEW (Tarjetas Principales)
        // ==========================================
        $overview = [
            'total_assets' => DB::table('assets')->count(),
            'total_members' => DB::table('members')->count(),
            'active_members' => DB::table('members')->where('status', 'ACTIVO')->count(),
            'pending_it_members' => DB::table('members')->where('status', 'PENDIENTE_IT')->count(),
            'offboarding_members' => DB::table('members')->where('status', 'BAJA')->count(),
            'terminated_members' => DB::table('members')->where('status', 'TERMINADO')->count(),
            'assets_assigned' => DB::table('assets')->whereNotNull('member_id')->count(),
            'assets_unassigned' => DB::table('assets')->whereNull('member_id')->count(),
            'categories_count' => DB::table('asset_categories')->count(),
            'properties_count' => DB::table('properties')->count(),
        ];

        // ==========================================
        // 2. ASSETS ANALYTICS
        // ==========================================
        $assetsAnalytics = [
            'by_status' => DB::table('assets')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),

            'by_category' => DB::table('assets')
                ->join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
                ->select('asset_categories.name as category', DB::raw('count(assets.id) as count'))
                ->groupBy('asset_categories.id', 'asset_categories.name')
                ->get(),

            'by_property' => DB::table('assets')
                ->join('properties', 'assets.property_id', '=', 'properties.id')
                ->select('properties.name as property', DB::raw('count(assets.id) as count'))
                ->groupBy('properties.id', 'properties.name')
                ->get(),

            // JOIN Corregido: members -> positions -> departments
            'by_department' => DB::table('assets')
                ->join('members', 'assets.member_id', '=', 'members.id')
                ->join('positions', 'members.position_id', '=', 'positions.id')
                ->join('departments', 'positions.department_id', '=', 'departments.id')
                ->select('departments.name as department', DB::raw('count(assets.id) as count'))
                ->groupBy('departments.id', 'departments.name')
                ->get(),

            'unassigned' => ['count' => $overview['assets_unassigned']],

            'top_categories' => DB::table('assets')
                ->join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
                ->select('asset_categories.name as category', DB::raw('count(assets.id) as count'))
                ->groupBy('asset_categories.id', 'asset_categories.name')
                ->orderByDesc('count')
                ->take(5)
                ->get(),

            'by_brand' => DB::table('assets')
                ->select('brand', DB::raw('count(*) as count'))
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->groupBy('brand')
                ->orderByDesc('count')
                ->get(),

            'warranty_expiring' => [
                'next_30_days' => DB::table('assets')->whereBetween('warranty_expiry', [$now, $now->copy()->addDays(30)])->count(),
                'next_90_days' => DB::table('assets')->whereBetween('warranty_expiry', [$now, $now->copy()->addDays(90)])->count(),
            ]
        ];

        // ==========================================
        // 3. MEMBERS ANALYTICS
        // ==========================================
        $membersAnalytics = [
            'by_status' => DB::table('members')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),

            // JOIN Corregido: positions
            'by_department' => DB::table('members')
                ->join('positions', 'members.position_id', '=', 'positions.id')
                ->join('departments', 'positions.department_id', '=', 'departments.id')
                ->select('departments.name as department', DB::raw('count(members.id) as count'))
                ->groupBy('departments.id', 'departments.name')
                ->get(),

            'by_property' => DB::table('members')
                ->join('properties', 'members.property_id', '=', 'properties.id')
                ->select('properties.name as property', DB::raw('count(members.id) as count'))
                ->groupBy('properties.id', 'properties.name')
                ->get(),

            // PostgresSQL: Función TO_CHAR y GroupByRaw correctos
            'hiring_trend' => DB::table('members')
                ->select(DB::raw("TO_CHAR(hire_date, 'YYYY-MM') as month"), DB::raw('count(*) as count'))
                ->whereNotNull('hire_date')
                ->groupByRaw("TO_CHAR(hire_date, 'YYYY-MM')")
                ->orderBy('month', 'desc')
                ->take(6)
                ->get()
        ];

        // ==========================================
        // 4. IT LIFECYCLE
        // ==========================================
        // PostgresSQL: Resta de fechas castedas a ::date
        $avgOnboarding = DB::table('members')
            ->whereNotNull('admission_date')
            ->whereNotNull('hire_date')
            ->select(DB::raw('AVG(admission_date::date - hire_date::date) as avg_days'))
            ->first();

        $lifecycle = [
            'onboarding_pipeline' => [
                'pending_it' => $overview['pending_it_members'],
                'active' => $overview['active_members'],
                'offboarding' => $overview['offboarding_members']
            ],
            'onboarding_average_time' => ['avg_days' => round($avgOnboarding->avg_days ?? 0, 1)]
        ];

        // ==========================================
        // 5. SEGURIDAD & PLATAFORMAS (AHORA REALES)
        // ==========================================
        $platforms = [
            // Plataformas más usadas contando usuarios distintos
            'most_used' => DB::table('member_platform_permission')
                ->join('platform_permissions', 'member_platform_permission.platform_permission_id', '=', 'platform_permissions.id')
                ->join('platforms', 'platform_permissions.platform_id', '=', 'platforms.id')
                ->select('platforms.name as platform', DB::raw('count(distinct member_platform_permission.member_id) as members'))
                ->groupBy('platforms.id', 'platforms.name')
                ->orderByDesc('members')
                ->take(5)
                ->get(),
            // Conteo de permisos otorgados manualmente (is_override = true)
            'overrides' => [
                'overrides_total' => DB::table('member_platform_permission')->where('is_override', true)->count()
            ]
        ];

        // ==========================================
        // 6. DISTRIBUCIÓN POR USUARIO (Auditoría)
        // ==========================================
        // PostgresSQL: Agrupado por las columnas reales (no por el alias)
        $auditByMember = DB::table('members')
            ->join('assets', 'members.id', '=', 'assets.member_id')
            ->select(
                DB::raw("CONCAT(members.name, ' ', members.last_name) as member"),
                DB::raw('count(assets.id) as assets')
            )
            ->groupBy('members.id', 'members.name', 'members.last_name')
            ->orderByDesc('assets')
            ->take(10)
            ->get();

        // ==========================================
        // 7. MATRIZ DEPARTAMENTO VS CATEGORÍA
        // ==========================================
        // JOINs Corregidos: positions y asset_categories
        $rawMatrix = DB::table('assets')
            ->join('members', 'assets.member_id', '=', 'members.id')
            ->join('positions', 'members.position_id', '=', 'positions.id')
            ->join('departments', 'positions.department_id', '=', 'departments.id')
            ->join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->select('departments.name as department', 'asset_categories.name as category', DB::raw('count(assets.id) as count'))
            ->groupBy('departments.id', 'departments.name', 'asset_categories.id', 'asset_categories.name')
            ->get();

        // ==========================================

        // 8. ALERTAS CRÍTICAS (El bloque que faltaba)
        // ==========================================
        $alerts = [
            'expiring_warranties' => DB::table('assets')
                ->whereNotNull('warranty_expiry')
                ->whereBetween('warranty_expiry', [$now, $now->copy()->addDays(30)])
                ->select('id', 'category_id', 'brand', 'model', 'serial_number', 'warranty_expiry')
                ->orderBy('warranty_expiry', 'asc')
                ->take(5)
                ->get(),

            'pending_offboardings' => DB::table('members')
                ->where('status', 'BAJA')
                ->whereNull('termination_date')
                ->select('id', 'name', 'last_name', 'hire_end_date')
                ->orderBy('hire_end_date', 'desc')
                ->take(5)
                ->get()
        ];

        $matrix = [];
        foreach ($rawMatrix as $row) {
            $dept = $row->department;
            if (!isset($matrix[$dept])) {
                $matrix[$dept] = ['department' => $dept];
            }
            $matrix[$dept][$row->category] = $row->count;
        }

        // ==========================================
        // RESPUESTA FINAL JSON
        // ==========================================
        return response()->json([
            'overview' => $overview,
            'assets' => $assetsAnalytics,
            'members' => $membersAnalytics,
            'lifecycle' => $lifecycle,
            'platforms' => $platforms,
            'audit_by_member' => $auditByMember,
            'matrix' => array_values($matrix),
            'alerts' => $alerts
        ]);
    }
}
