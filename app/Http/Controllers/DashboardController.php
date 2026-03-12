<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $now = Carbon::now();

        // 1. KPIs (Tarjetas superiores)
        $kpis = [
            'total_assets' => Asset::count(),
            'assets_in_repair' => Asset::where('status', 'repair')->count(),
            'active_members' => Member::where('status', 'ACTIVO')->count(),
            'pending_it_members' => Member::where('status', 'PENDIENTE_IT')->count(),
        ];

        // 2. Gráficas de Distribución
        $assetsByStatus = Asset::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $assetsByProperty = Asset::select('properties.name', DB::raw('count(assets.id) as count'))
            ->join('properties', 'assets.property_id', '=', 'properties.id')
            ->groupBy('properties.id', 'properties.name')
            ->get();

        // 3. Alertas Críticas
        $expiringWarranties = Asset::with(['category'])
            ->whereNotNull('warranty_expiry')
            ->whereBetween('warranty_expiry', [$now, $now->copy()->addDays(30)])
            ->orderBy('warranty_expiry', 'asc')
            ->take(5)
            ->get(['id', 'category_id', 'brand', 'model', 'serial_number', 'warranty_expiry']);

        $pendingOffboardings = Member::where('status', 'BAJA')
            ->whereNull('termination_date')
            ->orderBy('hire_end_date', 'desc')
            ->take(5)
            ->get(['id', 'name', 'last_name', 'hire_end_date']);

        // Estructura del Payload
        return response()->json([
            'kpis' => $kpis,
            'charts' => [
                'by_status' => $assetsByStatus,
                'by_property' => $assetsByProperty
            ],
            'alerts' => [
                'expiring_warranties' => $expiringWarranties,
                'pending_offboardings' => $pendingOffboardings
            ]
        ]);
    }
}