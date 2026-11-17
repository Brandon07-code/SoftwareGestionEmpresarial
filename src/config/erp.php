<?php

/*
| Parámetros de negocio del ERP (Clase 14). Se leen con config('erp.clave').
| Tenerlos aquí evita «números mágicos» repartidos por el código.
*/
return [
    // Por debajo (o igual) de esta cantidad se considera stock bajo y se alerta
    'low_stock_threshold' => (int) env('ERP_LOW_STOCK', 5),

    // Hora del resumen diario que se envía a la gerencia (formato 24 h)
    'daily_summary_at' => env('ERP_DAILY_SUMMARY_AT', '19:00'),
];
