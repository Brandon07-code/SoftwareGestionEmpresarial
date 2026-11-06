<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Plantilla para la importación masiva: el usuario la descarga, la llena y la vuelve a subir.
 * Los encabezados deben coincidir con las claves que espera ProductsImport.
 */
class ProductsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return ['nombre', 'descripcion', 'precio', 'stock', 'categoria'];
    }

    public function array(): array
    {
        return [
            ['Cera Mate Fijación Fuerte Uppercut', 'Cera modeladora a base de agua sin brillo residual', 45000, 25, 'Cuidado Capilar & Ceras'],
            ['Navaja Barbera Profesional Acero Inox', 'Portanavajas ergonómico de acero quirúrgico', 28000, 30, 'Accesorios & Cuidado Personal'],
        ];
    }
}
