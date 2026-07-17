<?php

// Catálogo de módulos administrables por permisos granulares.
// La clave es el identificador usado en EmpleadoPermisos.modulo y en las
// rutas (middleware 'permiso:<clave>,<accion>'). El valor es el nombre
// que se muestra en la pantalla de Gestión de Personal.

return [
    'dashboard'     => 'Dashboard',
    'pos'           => 'Punto de Venta',
    'historial'     => 'Historial de Pedidos',
    'especiales'    => 'Pedidos Especiales',
    'pedidos'       => 'Monitor de Pedidos',
    'flujo_caja'    => 'Flujo de Caja',
    'clientes'      => 'Clientes',
    'gastos'        => 'Gastos',
    'empleados'     => 'Empleados',
    'productos'     => 'Catálogo de Productos',
    'recursos'      => 'Categorías / Sucursales / Cargos',
    'caja'          => 'Corte Mensual',
    'configuracion' => 'Configuración',
];