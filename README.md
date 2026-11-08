# Eiken Design — Prototipo en WordPress + WooCommerce + Webpay Plus

Repositorio independiente para evaluar y comparar **WordPress + WooCommerce + Transbank Webpay Plus REST** frente a la plataforma a medida de **Eiken Design** (`eiken_web`), manteniendo el repositorio original intacto.

---

## Contenido del Repositorio

* **[`theme/eiken-store/`](theme/eiken-store/)**: Tema de WordPress/WooCommerce con la identidad visual oficial de **Eiken Design** (fondo negro carbón `#0A0A0C`, acentos rojo-naranja racing `#FF2B00`–`#FF3D00`, logo oficial y tarjetas de tienda estilizadas).
* **[`scripts/seed-eiken.php`](scripts/seed-eiken.php)**: Script de aprovisionamiento automático que configura Peso Chileno (`CLP` sin decimales), páginas de WooCommerce y carga las categorías (`Racing`, `Vehicular`, `Textil`) y productos reales de Eiken Design (`Calcomanía Monster Energy`, `Polera estampada gaming`, `Polera estampada Honda`, `Polerón Personalizado F1`).
* **[`scripts/start-local.ps1`](scripts/start-local.ps1)**: Lanzador local **sin necesidad de instalar Docker ni XAMPP** (descarga PHP 8.3 portable + WordPress + SQLite + WooCommerce + plugin oficial Transbank Webpay Plus REST y levanta el servidor en `http://localhost:8080`).
* **[`docker-compose.yml`](docker-compose.yml)**: Configuración alternativa para levantar WordPress + MySQL 8.0 con Docker.

---

## Cómo ejecutarlo localmente (1 comando)

Desde la carpeta `C:\Users\BenjiVoll\eiken_wp`:

```powershell
npm start
```

* **Tienda Pública:** `http://localhost:8080`
* **Panel de Administración (`WP-Admin`):** `http://localhost:8080/wp-admin`
  * **Usuario:** `admin`
  * **Contraseña:** `eiken2026`
