<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SUNAT (Perú) - Configuración
    |--------------------------------------------------------------------------
    |
    | Este proyecto deja preparado el módulo para emisión electrónica.
    | Para emisión real, se requiere:
    |  - Credenciales SOL (usuario + clave) del RUC emisor
    |  - Certificado digital (.pfx/.p12) y su clave
    |  - Acceso a internet desde el servidor
    |
    | Endpoints oficiales (producción) para Facturación Electrónica (billService):
    | https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl
    |
    */

    'endpoints' => [
        'fe_wsdl'  => env('SUNAT_FE_WSDL', 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl'),
        // Para GRE, SUNAT publica manuales y endpoints por servicio; se deja configurable.
        'gre_wsdl' => env('SUNAT_GRE_WSDL', ''),
    ],

    'storage_path' => env('SUNAT_STORAGE_PATH', storage_path('app/sunat')),
];
