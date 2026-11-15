# Camionero Carga

Registro de fletes por cliente. Cada flete tiene productos (`servicio` + `flete`) y abonos parciales.

## Qué guarda

| Tabla | Uso |
| --- | --- |
| `users` | Quién entra al sistema |
| `clientes` | Nombre del cliente. Se crea desde el flete si todavía no existe |
| `fletes` | Fecha y total general |
| `flete_items` | Productos. `total` = `servicio` + `flete` |
| `flete_pagos` | Abonos. No pueden pasar del saldo |

Los módulos de servicios, agencias y su dashboard ya no forman parte de la aplicación. La migración `2026_11_15_000001_drop_legacy_servicio_tables` elimina las tablas `pagos`, `servicios` y `agencias` si todavía existen. No toca clientes ni fletes.

## Montos

El navegador muestra el total, pero el servidor lo vuelve a calcular. Un total enviado a mano no se guarda.

## Acceso

El registro público está cerrado. Entran solo los usuarios que ya existen. Después del login se abre el listado de fletes.

## Puesta en marcha

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

En el servidor, `composer deploy` corre las migraciones. Haz una copia de la base antes de desplegar esta versión: la migración nueva borra servicios, agencias y pagos del módulo viejo.
