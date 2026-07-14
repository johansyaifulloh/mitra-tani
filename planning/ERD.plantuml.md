# ERD Mantri Tani — PlantUML

File ini berisi diagram ERD dalam format **PlantUML**, selaras dengan migration Laravel (`database/migrations/`).

**Versi:** 1.3 · **Tanggal:** 7 Juli 2026

> **Fix Syntax Error:** `ERD.puml` sekarang **1 diagram saja**. Diagram lain ada di folder [`diagrams/`](diagrams/). Jangan paste banyak `@startuml` sekaligus ke plantuml.com.

---

## Cara render

| Tool | Cara pakai |
|------|------------|
| [PlantUML Online](https://www.plantuml.com/plantuml/uml) | Copy seluruh isi `ERD.puml` → paste |
| VS Code / Cursor | Install extension **PlantUML** → Alt+D preview |
| IntelliJ / PhpStorm | Plugin PlantUML integration |
| CLI | `java -jar plantuml.jar planning/ERD.puml` → export PNG/SVG |
| draw.io | Arrange → Insert → Advanced → PlantUML |

**File sumber:** [`ERD.puml`](ERD.puml) · diagram lain: [`diagrams/`](diagrams/)

---

## 1. Diagram Relasi Utama (13 tabel)

```plantuml
@startuml mantri-tani-erd-main
skinparam linetype ortho

entity "users" as users {
  * **id** : bigint <<PK>>
  --
  name : varchar(255)
  email : varchar(255) <<UK, nullable>>
  phone : varchar(20) <<UK, nullable>>
  password : varchar(255)
  role : enum(customer, admin, owner)
  email_verified_at : timestamp
  created_at : timestamp
  updated_at : timestamp
}

entity "refresh_tokens" as refresh_tokens {
  * **id** : bigint <<PK>>
  --
  * user_id : bigint <<FK>>
  token_hash : varchar(255) <<UK>>
  expires_at : timestamp
  revoked_at : timestamp
  created_at : timestamp
}

entity "categories" as categories {
  * **id** : bigint <<PK>>
  --
  name : varchar(100)
  slug : varchar(100) <<UK>>
  icon : varchar(10)
  color : varchar(7)
  description : text
  display_order : int
  is_active : boolean
  created_at : timestamp
  updated_at : timestamp
}

entity "products" as products {
  * **id** : bigint <<PK>>
  --
  * category_id : bigint <<FK>>
  name : varchar(255)
  slug : varchar(255) <<UK>>
  description : text
  price : decimal(12,2)
  stock : int
  emoji : varchar(10)
  image_path : varchar(255)
  badge : varchar(20)
  status : enum(active, draft)
  created_at : timestamp
  updated_at : timestamp
}

entity "addresses" as addresses {
  * **id** : bigint <<PK>>
  --
  * user_id : bigint <<FK>>
  label : varchar(50)
  recipient_name : varchar(255)
  phone : varchar(20)
  street : text
  district : varchar(255)
  is_default : boolean
  created_at : timestamp
  updated_at : timestamp
}

entity "cart_items" as cart_items {
  * **id** : bigint <<PK>>
  --
  * user_id : bigint <<FK>>
  * product_id : bigint <<FK>>
  quantity : int
  is_selected : boolean
  created_at : timestamp
  updated_at : timestamp
}

entity "orders" as orders {
  * **id** : bigint <<PK>>
  --
  code : varchar(20) <<UK>>
  * user_id : bigint <<FK>>
  address_id : bigint <<FK, nullable>>
  buyer_name : varchar(255)
  buyer_phone : varchar(20)
  subtotal : decimal(12,2)
  admin_fee : decimal(12,2)
  total : decimal(12,2)
  payment_status : enum
  pickup_status : enum
  payment_method : varchar(50)
  midtrans_order_id : varchar(100)
  midtrans_transaction_id : varchar(100)
  snap_token : varchar(255)
  paid_at : timestamp
  expired_at : timestamp
  created_at : timestamp
  updated_at : timestamp
}

entity "order_items" as order_items {
  * **id** : bigint <<PK>>
  --
  * order_id : bigint <<FK>>
  * product_id : bigint <<FK>>
  product_name : varchar(255)
  unit_price : decimal(12,2)
  quantity : int
  subtotal : decimal(12,2)
}

entity "pickup_proofs" as pickup_proofs {
  * **id** : bigint <<PK>>
  --
  order_id : bigint <<FK, UK>>
  * verified_by : bigint <<FK>>
  photo_path : varchar(255)
  note : text
  verified_at : timestamp
}

entity "midtrans_settings" as midtrans_settings {
  * **id** : bigint <<PK>>
  --
  server_key : varchar(255)
  client_key : varchar(255)
  mode : enum(sandbox, production)
  notification_url : varchar(255)
  finish_url : varchar(255)
  unfinish_url : varchar(255)
  error_url : varchar(255)
  expiry_duration : int
  is_active : boolean
  updated_at : timestamp
}

entity "payment_channels" as payment_channels {
  * **id** : bigint <<PK>>
  --
  code : varchar(30) <<UK>>
  name : varchar(100)
  channel_group : enum(qris, va, ewallet)
  icon : varchar(10)
  color : varchar(7)
  is_enabled : boolean
}

entity "favorites" as favorites {
  * **id** : bigint <<PK>>
  --
  * user_id : bigint <<FK>>
  * product_id : bigint <<FK>>
  created_at : timestamp
}

entity "activity_logs" as activity_logs {
  * **id** : bigint <<PK>>
  --
  user_id : bigint <<FK, nullable>>
  action : varchar(50)
  subject_type : varchar(100)
  subject_id : bigint
  description : text
  created_at : timestamp
}

users ||--o{ refresh_tokens : CASCADE
users ||--o{ addresses : CASCADE
users ||--o{ cart_items : CASCADE
users ||--o{ orders : RESTRICT
users ||--o{ favorites : CASCADE
users ||--o{ activity_logs : SET NULL
users ||--o{ pickup_proofs : RESTRICT

categories ||--o{ products : RESTRICT

products ||--o{ cart_items : CASCADE
products ||--o{ order_items : RESTRICT
products ||--o{ favorites : CASCADE

addresses ||--o{ orders : SET NULL

orders ||--|{ order_items : CASCADE
orders ||--o| pickup_proofs : CASCADE

note right of orders
  payment_status:
  menunggu_pembayaran | lunas | expired
  pickup_status:
  menunggu_approval | disetujui | selesai | ditolak
end note

@enduml
```

---

## 2. Diagram Ringkas (hanya relasi)

```plantuml
@startuml mantri-tani-erd-simple
skinparam linetype ortho

entity users
entity refresh_tokens
entity categories
entity products
entity addresses
entity cart_items
entity orders
entity order_items
entity pickup_proofs
entity favorites
entity activity_logs

users ||--o{ refresh_tokens : JWT
users ||--o{ addresses
users ||--o{ cart_items
users ||--o{ orders
users ||--o{ favorites
users ||--o{ activity_logs
users ||--o{ pickup_proofs

categories ||--o{ products

products ||--o{ cart_items
products ||--o{ order_items
products ||--o{ favorites

addresses ||--o{ orders

orders ||--|{ order_items
orders ||--o| pickup_proofs

@enduml
```

---

## 3. Alur Modul (component)

```plantuml
@startuml mantri-tani-module-flow
skinparam componentStyle rectangle

package "Auth & User" #e8f5e9 {
  [users] as U
  [refresh_tokens] as RT
  U --> RT
}

package "Katalog" #fff8e1 {
  [categories] as CAT
  [products] as PRD
  CAT --> PRD
}

package "Pelanggan" #e3f2fd {
  [addresses] as ADR
  [cart_items] as CRT
  [favorites] as FAV
}

package "Transaksi & Pickup" #fce4ec {
  [orders] as ORD
  [order_items] as OIT
  [pickup_proofs] as PUP
}

package "Midtrans" #f3e5f5 {
  [midtrans_settings] as MTS
  [payment_channels] as PCH
}

U --> ADR
U --> CRT
U --> FAV
U --> ORD
U --> PUP
CRT --> PRD
FAV --> PRD
ADR --> ORD
ORD --> OIT
OIT --> PRD
ORD ..> MTS
ORD ..> PCH

@enduml
```

---

## 4. Lifecycle Order (state)

```plantuml
@startuml mantri-tani-order-lifecycle
[*] --> menunggu_pembayaran : checkout

menunggu_pembayaran --> lunas : Midtrans settlement
menunggu_pembayaran --> expired : timeout

state lunas {
  [*] --> menunggu_approval
}

menunggu_approval --> disetujui : admin + foto
menunggu_approval --> ditolak : admin tolak

disetujui --> selesai : finalisasi

selesai --> [*]
ditolak --> [*]
expired --> [*]

@enduml
```

---

## 5. Alur JWT Auth (sequence)

```plantuml
@startuml mantri-tani-jwt-auth
actor Client as C
participant "API Laravel" as A
database Database as D

C -> A : POST /api/auth/login
A -> D : validasi users
A -> A : generate JWT + refresh_token
A -> D : simpan refresh_tokens
A --> C : access_token + refresh_token

C -> A : GET /api/... Bearer token
A --> C : response

C -> A : POST /api/auth/logout
A -> D : revoked_at refresh_token

@enduml
```

---

## 6. Alur Midtrans Snap (sequence)

```plantuml
@startuml mantri-tani-midtrans-snap
actor Pelanggan as C
participant "Mantri Tani" as T
participant Midtrans as M

C -> T : checkout
T -> M : POST Snap transaction
M --> T : snap_token
T -> T : simpan ke orders
T --> C : tampilkan Snap
C -> M : bayar
M -> T : POST /api/midtrans/notification
T -> T : update payment_status
M --> C : redirect finish_url

@enduml
```

---

## 7. Ringkasan relasi & ON DELETE

| Parent | Child | Kardinalitas | ON DELETE |
|--------|-------|--------------|-----------|
| users | refresh_tokens | 1:N | CASCADE |
| users | addresses | 1:N | CASCADE |
| users | cart_items | 1:N | CASCADE |
| users | orders | 1:N | RESTRICT |
| users | favorites | 1:N | CASCADE |
| users | activity_logs | 1:N | SET NULL |
| users | pickup_proofs | 1:N | RESTRICT |
| categories | products | 1:N | RESTRICT |
| products | cart_items | 1:N | CASCADE |
| products | order_items | 1:N | RESTRICT |
| products | favorites | 1:N | CASCADE |
| addresses | orders | 1:N | SET NULL |
| orders | order_items | 1:N | CASCADE |
| orders | pickup_proofs | 1:0..1 | CASCADE |

---

## 8. File terkait

| File | Fungsi |
|------|--------|
| `planning/ERD.md` | Dokumentasi lengkap + definisi kolom |
| `planning/ERD.puml` | **Source PlantUML** (semua diagram) |
| `planning/ERD.plantuml.md` | Dokumentasi ERD |
| `planning/FLOWCHART.puml` | Flowchart alur bisnis |
| `planning/FLOWCHART.plantuml.md` | Dokumentasi flowchart |
| `database/migrations/` | Implementasi schema di Laravel |
