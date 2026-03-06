<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class OrganizationMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizationChart = [

        "Accounting-Finance" => [
                "Asistente de Direccion de Finanzas",
                "Auditor de Grupos",
                "Auditor de Ingresos",
                "Auxiliar Contable-AP",
                "Auxiliar Contable-AR",
                "Auxiliar de Auditoria de Grupos",
                "Auxiliar de Costos",
                "Auxiliar Fiscal",
                "Cajero General",
                "Contador General",
                "Contralor de Costos",
                "Director de Finanzas Cluster",
                "Encargado de Cuentas por pagar",
                "Gerente de Credito",
                "Gerente de Impuestos"
        ],
        "AMAI Beach Club" =>[
                "Ayudante de Barman",
                "Ayudante de Mesero (a) Restaurant",
                "Cantinero",
                "Gerente de Beach Club",
                "Mesero (a) de Restaurante"
        ],
        "Auma" => [
                "Ayudante de Mesero (a) Restaurant",
                "Hostess",
                "Mesero (a) de Restaurante"
        ],
        "Banquetes" => [
                "Asistente Operativo de Banquetes",
                "Encargado de Montaje",
                "Mesero (a) de Banquetes",
                "Steward",
                "Supervisor de Banquetes"
        ],
        "Bares" => [
                "Ayudante de Barman",
                "Cantinero",
                "Gerente de Bares",
                "Mixologo",
                "Supervisor de Bar",
                "Wine Steward"
        ],
        "Bell Services" => [
            "Bell Boy"
        ],
        "Boutique" => [
            "Cajera Vendedora",
            "Jefe de Tienda"
        ],
        "Catering & Events" => [
            "Blue Coat",
            "Coord de Gpo y Eventos Lujo",
            "Coordinador de Bodas",
            "Coordinador de Eventos",
            "Director Catering & Events",
            "Gerente de Eventos",
            "Gerente de Grupos y Eventos Lujo",
            "Subdirector de Eventos y Catering"
        ],
        "Cocina" => [
            "Auxiliar Administrativo",
            "Ayudante de Cocina",
            "Carnicero",
            "Chef Carnicero",
            "Chef de Partie",
            "Chef de Room Service",
            "Chef Ejecutivo",
            "Cocinero A",
            "Cocinero B",
            "Coordinador de Higiene Alimentaria",
            "Coordinador de Higiene en Alimentos",
            "Senior Chef de Cuisine",
            "Signature Chef",
            "Sous Chef",
            "Sous Chef Ejecutivo",
            "Sous Chef Ejecutivo Junior",
            "Sous Chef II",
            "Supervisor de Cocina"
        ],
        "Cocina Banquetes" => [
            "Cocinero A",
            "Cocinero B",
            "Panadero",
            "Pastelero A",
            "Sous Chef Pastelero"
        ],
        "Employee Dining Room" => [
            "Cocinero A",
            "Cocinero B",
            "Coordinador de Comedor",
            "Encargado de Restaurante de TM",
            "Steward",
            "Supervisor de Cocina"
        ],
        "Engineering" => [
            "Albañil Tablaroquero",
            "Asistente Administrativo de Mantenimiento",
            "Carpintero/Barnizador",
            "Gerente de Mantenimiento",
            "Mecanico de Cocina",
            "Mecanico de Lavanderia",
            "Operador de Cuartos",
            "Operador de sala de maquina",
            "Pintor",
            "Plomero/Electricista",
            "Subgerente de Mantenimiento",
            "Supervisor de Mantenimiento",
            "Tecnico de Spa",
            "Tecnico Electromecanico",
            "Tecnico en Aire Acondicionado",
            "Tecnico en Refrigeracion",
            "Técnico Fanacolero"
        ],
        "Entertainment" => [
            "Asistente Gte de Actividades",
            "Auxiliar Administrativo de Entretenimiento",
            "Auxiliar de Act Rec y Manualidades",
            "Auxiliar de Entretenimiento",
            "Disk Jockey",
            "Entrenador de Gimnasio",
            "Gerente de Actividades",
            "Jefe de Actividades y Recreacion",
            "Maestra de Artes Plasticas",
            "Supervisor Disk Jockey",
            "Wellness Instructor"
        ],
        "Executive" => [
            "Asistente Administrativo de Gerencia",
            "Director de Operaciones",
            "Gerente ambiental",
            "Gerente de Hotel",
            "Gerente General",
            "Managing Director Complex",
            "Steward de Reciclaje"
        ],
  "Experiencias & Reservaciones Restaurante" => [
    "Agente de ventas F&B",
    "Supervisor de Ventas F&B"
  ],
  "F-B Administration" => [
    "Asistente Admin de Alimentos y Bebidas y Cocina",
    "Asistente Admin de Direccion A&B",
    "Asistente Operativo de A&B",
    "Auxiliar Administrativo",
    "Gerente de Alimentos y Bebidas",
    "Subgerente de Alimentos y Bebidas"
  ],
  "Front Office" => [
    "Agente de Recepcion",
    "Agente de Servicio al Cliente",
    "Director de División Cuartos",
    "Gerente de Recepcion",
    "Gerente Nocturno",
    "Rooms Controller",
    "Subgerente de Recepcion",
    "Subgerente de Recepcion Lujo",
    "Supervisor de Recepcion"
  ],
  "Grab & Go - Azulinda" => [
    "Barista",
    "Mesero (a) de Bares",
    "Supervisor de Bar"
  ],
  "Guest Service" => [
    "Concierge",
    "E-Concierge",
    "Gerente de Experiencia al Husped",
    "Gerente de Servicio al Cliente",
    "Lobby Ambassador",
    "Supervisor de Concierge"
  ],
  "Housekeeping" => [
    "Asistente de Ama de LLaves",
    "Auxiliar Administrativo",
    "Camarista",
    "Coordinador de Ama de Llaves",
    "Housekeeping Manager",
    "Houseman",
    "Supervisor de ama de llaves"
  ],
  "Human Resources" => [
    "Asistente de Direccion de Recursos Humanos",
    "Auxiliar de Nominas",
    "Auxiliar de Recursos Humanos",
    "Coordinador de Capacitacion",
    "Coordinador de Capacitacion y Administracion",
    "Coordinador de Recursos Humanos",
    "Coordinador de Recursos Humanos Lujo",
    "Coordinador de Relaciones Laborales",
    "Director de Recursos Humanos Cluster",
    "Encargado de Nominas",
    "Gerente de Calidad",
    "Gerente de Capacitacion",
    "Gerente de Personal",
    "Instructor de Excelencia"
  ],
  "In Room Dining" => [
    "Gerente de Room Services",
    "Mesero (a) Room Service",
    "Supervisor Room Service"
  ],
  "In Room Dining & W" => [
    "Mesero (a) Room Service",
    "Supervisor Room Service",
    "Toma Ordenes"
  ],
  "Information and Technologies" => [
    "Asistente Gerente de Sistemas",
    "Coordinador de Sistemas",
    "Director de Sistemas Cluster"
  ],
  "Kids Club" => [
    "Auxiliar de Kids Club",
    "Supervisor de Kids Club"
  ],
  "La Luce" => [
    "Ayudante de Mesero (a) Restaurant",
    "Capitan de Restaurant",
    "Gerente de Restaurant",
    "Mesero (a) de Restaurante"
  ],
  "Laundry & Valet" => [
    "Aux de Roperia",
    "Auxiliar de Lavanderia",
    "Costurera",
    "Gerente de Lavanderia",
    "Jefe de Lavanderia",
    "Operador de Lavanderia",
    "Supervisor de Lavanderia",
    "Valet de tintoreria"
  ],
  "Marketing & Communications" => [
    "Coordinación de Marketing"
  ],
  "Marketing & Sales" => [
    "Administrador Delphi-SalesForce",
    "Area Director of Marketing",
    "Complex Asst Dir of Sales",
    "Complex Director of Groups Sales",
    "Complex Group Sales Manager",
    "Complex L & W Manager-KA",
    "Complex Leisure Sales Coordinator",
    "Complex Leisure Sales Mgr MX",
    "Complex Lux Leisure Sales Mgr",
    "Complex Sales Assistant",
    "Complex Sales Optmztn Exec",
    "Complex Sales Optmztn Exec US",
    "Complex Site Inspection Mgr",
    "Complex Wedding Sales Manager",
    "Coordinador de Site Inspection",
    "Creador de contenido",
    "Ejecutivo de Site Inspection",
    "Ejecutivo Ventas Grupos",
    "Ejecutivo Ventas Leisure",
    "Gerente de Bodas",
    "Gerente de Marketing",
    "Gerente de Marketing Lujo",
    "Gerente de Ventas Grupos",
    "Leisure Sales Manager",
    "Luxury Complex Director of Sales & Marketing",
    "Subdirector de Ventas",
    "Subdirector de Ventas Complex"
  ],
  "Maxal" => [
    "Ayudante de Mesero (a) Restaurant",
    "Gerente de Restaurant",
    "Hostess",
    "Mesero (a) de Restaurante"
  ],
  "Mini-Bar" => [
    "Supervisor de MiniBar",
    "Surtidor de Minibar"
  ],
  "Mini-Bar & W" => [
    "Surtidor de Minibar"
  ],
  "Pasteleria" => [
    "Ayudante de Panaderia",
    "Ayudante de Pasteleria",
    "Chef de Partie Pastelero",
    "Chef Pastelero",
    "Panadero",
    "Pastelero A",
    "Sous Chef Pastelero",
    "Supervisor de Panaderia"
  ],
  "Peacock Alley" => [
    "Cantinero",
    "Mesero (a) de Restaurante"
  ],
  "Pool Waiters" => [
    "Mesero (a) de Bares",
    "Pool Manager"
  ],
  "Public Areas" => [
    "Auxiliar de Areas Publicas",
    "Jefe de Areas Publicas",
    "Pulidor",
    "Supervisor de Areas Publicas"
  ],
  "Purchasing & Storerooms" => [
    "Encargado de Almacen",
    "Encargado de Compras de Mantenimiento",
    "Encargado(a) de Compras",
    "Recibidor de Mercancia",
    "Supervisor de Almacen"
  ],
  "Reservations" => [
    "Agente de Reservaciones",
    "Gerente de Reservaciones",
    "Supervisor de Reservaciones",
    "Supervisor de Reservaciones Lujo"
  ],
  "Restaurante Chaya" => [
    "Cantinero",
    "Hostess",
    "Mesero (a) de Restaurante",
    "Mesero Lider"
  ],
  "Restaurante JA´O" => [
    "Cantinero",
    "Hostess",
    "Mesero (a) de Restaurante",
    "Supervisor de Restaurante"
  ],
  "Restaurante Malpeque" => [
    "Cantinero",
    "Gerente Senior de Restaurante",
    "Hostess",
    "Mesero (a) de Restaurante"
  ],
  "Restaurante Pool & Beach" => [
    "Cantinero",
    "Gerente de Pool & Beach",
    "Mesero (a) de Restaurante"
  ],
  "Revenue" => [
    "Analista de Revenue",
    "Area Director de Revenue",
    "Asistente de Direccion de Revenue Cluster",
    "Gerente de Revenue",
    "Gerente de Revenue Lujo"
  ],
  "Seguridad" => [
    "Agente de Seguridad",
    "Chofer interno seguridad",
    "Gerente de Seguridad",
    "Guardavidas",
    "Jefe de Seguridad",
    "Supervisor de Guardavidas",
    "Supervisor de Seguridad"
  ],
  "Spa & Pool" => [
    "Estilista",
    "Recepcionista Spa",
    "Spa Attendant",
    "Supervisor de Spa",
    "Terapeuta Lider",
    "Therapist"
  ],
  "Stewarding" => [
    "Chief Steward",
    "Encargado de Montaje",
    "Gerente de Steward",
    "Steward",
    "Steward Banquetes",
    "Supervisor Steward"
  ],
  "Sunan" => [
    "Ayudante de Mesero (a) Restaurant",
    "Hostess",
    "Mesero (a) de Restaurante",
    "Mesero Lider"
  ],
  "Taqueria" => [
    "Ayudante de Mesero (a) Restaurant",
    "Mesero (a) de Restaurante"
  ],
  "Telefonos" => [
    "Agente de Telefono",
    "Supervisor de Telefonos"
  ],
  "Vela" => [
    "Ayudante de Mesero (a) Restaurant",
    "Capitan de Restaurant",
    "Gerente de Restaurant",
    "Hostess",
    "Mesero (a) de Restaurante"
  ]
        ];

        //Lennar el departamento y posiciones
        DB::transaction(function () use ($organizationChart) {
            
            foreach ($organizationChart as $deptName => $positions) {
                $dept = Department::firstOrCreate(['name' => trim($deptName)]);

                foreach ($positions as $posName) {
                    Position::firstOrCreate([
                        'department_id' => $dept->id,
                        'name' => trim($posName)
                    ]);
                }
            }

          // Migrar los miembros a las nuevas posiciones
            $members = Member::whereNull('position_id')->get();

            foreach ($members as $member) {
                $position = Position::where('name', $member->position)
                            ->whereHas('department', function($q) use ($member) {
                                $q->where('name', $member->department);
                            })->first();

                if ($position) {
                    $member->position_id = $position->id;
                    
                    if ($member->hire_date && !$member->admission_date) {
                        $member->admission_date = $member->hire_date;
                    }
                    if ($member->termination_date && !$member->hire_end_date) {
                        $member->hire_end_date = $member->termination_date;
                    }

                    $member->save();
                }
            }
        });
    }
}
