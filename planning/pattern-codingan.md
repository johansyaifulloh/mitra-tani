# Laravel Coding Pattern Standard

## Web Application - Clean Flow & Debug Friendly

---

# 1. Tujuan

Pattern ini digunakan agar kode Laravel:

* Mudah dibaca oleh developer lain.
* Mudah dilakukan debugging.
* Mudah mencari sumber error.
* Memiliki alur proses yang konsisten.
* Tidak bergantung pada magic framework.
* Mudah dikembangkan.

Prinsip utama:

```
READABLE CODE
        +
CLEAR PROCESS
        +
EASY DEBUGGING
        =
MAINTAINABLE APPLICATION
```

---

# 2. Flow Utama

Semua proses harus mengikuti urutan:

```
1. Receive Request

        |

2. Validate Request

        |

3. Get Database Reference

        |

4. Validate Database Result

        |

5. Business Logic

        |

6. Database Transaction

        |

7. Redirect / Return View
```

Tidak boleh mencampur proses.

---

# 3. Struktur Layer

```
Controller

    |
    |
Form Request

    |
    |
Service

    |
    |
Repository / DB Query

    |
    |
Database
```

---

# 4. Aturan Controller

Controller hanya bertugas:

* Menerima request.
* Memanggil validation.
* Memanggil service.
* Mengembalikan halaman.

Controller tidak boleh:

* Query database.
* Perhitungan bisnis.
* Transaction.
* Logic panjang.

Contoh:

```php
public function store(
    StoreUserRequest $request
)
{

    //--------------------------------------------------
    // STEP 1
    // Mengambil data hasil validasi request
    //--------------------------------------------------

    $data = $request->validated();



    //--------------------------------------------------
    // STEP 2
    // Menjalankan proses penyimpanan melalui service
    //--------------------------------------------------

    $this->userService
        ->create($data);



    //--------------------------------------------------
    // STEP 3
    // Redirect kembali setelah proses berhasil
    //--------------------------------------------------

    return redirect()
        ->route('user.index')
        ->with(
            'success',
            'Data berhasil disimpan'
        );

}
```

---

# 5. Aturan Komentar

Setiap proses besar WAJIB diberi komentar.

Format:

```php
//--------------------------------------------------
// STEP NOMOR
// Tujuan proses
// Alasan proses dilakukan
//--------------------------------------------------
```

Contoh:

```php
//--------------------------------------------------
// STEP 3
// Mengambil data customer dari database
// Data digunakan untuk validasi transaksi
//--------------------------------------------------

$customer = DB::table('customers')
    ->where('id',$id)
    ->first();
```

Komentar harus menjelaskan:

* Apa yang dilakukan.
* Kenapa dilakukan.
* Data digunakan untuk apa.

---

# 6. Request Validation

Gunakan Form Request.

Contoh:

```
app/
 └── Http/
      └── Requests/
```

Contoh:

```php
public function rules()
{
    return [

        'name'=>'required',

        'email'=>'required|email'

    ];
}
```

Tugas:

* Mengecek input user.
* Mengecek format data.

Tidak boleh:

```php
User::where();
```

---

# 7. Database Validation

Semua SELECT untuk validasi dikumpulkan.

Contoh:

```php
//--------------------------------------------------
// STEP 1
// Mengambil data user
// Digunakan untuk memastikan user tersedia
//--------------------------------------------------

$user = DB::table('users')
    ->where(
        'id',
        $data['user_id']
    )
    ->first();



//--------------------------------------------------
// STEP 2
// Mengambil data produk
// Digunakan untuk validasi produk transaksi
//--------------------------------------------------

$product = DB::table('products')
    ->where(
        'id',
        $data['product_id']
    )
    ->first();
```

---

# 8. Validasi Hasil Database

Setelah semua SELECT selesai.

Contoh:

```php
//--------------------------------------------------
// STEP 3
// Memastikan data referensi tersedia
//--------------------------------------------------

if(!$user){

    throw new Exception(
        'User tidak ditemukan'
    );

}


if(!$product){

    throw new Exception(
        'Product tidak ditemukan'
    );

}
```

Dilarang:

```php
if($user){

    if($product){

    }

}
```

Gunakan early return / exception.

---

# 9. Business Logic

Tempat:

* Perhitungan.
* Aturan bisnis.
* Manipulasi data.

Contoh:

```php
//--------------------------------------------------
// STEP 4
// Menghitung total transaksi
//--------------------------------------------------

$total =
$product->price *
$data['qty'];
```

Tidak boleh:

```php
DB::insert();

DB::update();
```

---

# 10. Database Transaction

Semua perubahan database wajib menggunakan transaction.

Gunakan:

```php
DB::beginTransaction();

try{


}
catch(Exception $e){


}
```

---

Contoh:

```php
DB::beginTransaction();

try{


    //--------------------------------------------------
    // STEP 5
    // Menyimpan data transaksi utama
    //--------------------------------------------------

    DB::table('orders')
        ->insert([
            'user_id'=>$data['user_id'],
            'total'=>$total
        ]);



    //--------------------------------------------------
    // STEP 6
    // Mengurangi stok produk
    //--------------------------------------------------

    DB::table('stocks')
        ->where(
            'product_id',
            $product->id
        )
        ->update([
            'qty'=>DB::raw(
                'qty-'.$data['qty']
            )
        ]);



    //--------------------------------------------------
    // STEP 7
    // Menyimpan perubahan permanen
    //--------------------------------------------------

    DB::commit();


}
catch(Exception $e){


    //--------------------------------------------------
    // STEP 8
    // Membatalkan semua perubahan
    // Karena terjadi error
    //--------------------------------------------------

    DB::rollback();



    //--------------------------------------------------
    // STEP 9
    // Simpan error untuk debugging
    //--------------------------------------------------

    Log::error(
        $e->getMessage()
    );


    throw $e;

}
```

---

# 11. Aturan Transaction

Di dalam transaction hanya:

BOLEH:

```
INSERT

UPDATE

DELETE
```

Tidak boleh:

```
Validasi

Perhitungan

SELECT banyak
```

Flow:

```
SELECT

↓

CHECK

↓

LOGIC

↓

TRANSACTION

↓

SAVE
```

---

# 12. Larangan Magic Laravel

Hindari penggunaan yang membuat proses sulit dilacak.

Contoh hindari:

```php
User::create($request->all());
```

Karena:

* Tidak terlihat field yang masuk.
* Sulit debugging.
* Risiko field tidak sengaja ikut.

Gunakan:

```php
DB::table('users')
->insert([

    'name'=>$data['name'],

    'email'=>$data['email']

]);
```

---

# 13. Query Database

Prioritas:

```
DB::table()

        |

        |

Repository

        |

        |

Model
```

Query harus terlihat jelas.

Hindari:

```php
magic relationship
```

jika membuat alur sulit diketahui.

---

# 14. Tidak Boleh Query Dalam Loop

Salah:

```php
foreach($items as $item){

    DB::table('products')
    ->where('id',$item->id)
    ->first();

}
```

Benar:

```php
DB::table('products')
->whereIn(
    'id',
    $ids
)
->get();
```

---

# 15. Debugging Standard

Setiap proses besar harus mudah ditemukan.

Gunakan:

```php
Log::info(
    'Create Order Start'
);
```

Error:

```php
Log::error(
    'Create Order Failed',
    [
        'message'=>$e->getMessage()
    ]
);
```

---

# 16. Function Rule

Satu function satu tugas.

Maksimal:

```
30 - 50 baris
```

Jika lebih:

Pecah function.

---

# 17. Final Pattern

```
CONTROLLER

 |
 |-- Request
 |
 |-- Validation
 |
 |-- Call Service


SERVICE

 |
 |-- Get Database Data
 |
 |-- Validate Database
 |
 |-- Business Logic
 |
 |-- DB Transaction
 |
 |-- Commit / Rollback


DATABASE


CONTROLLER

 |
 |-- Redirect
 |
 |-- Message
```

---

# 18. Checklist Review

Sebelum commit:

[ ] Setiap blok memiliki komentar.

[ ] Flow proses mudah dibaca.

[ ] Tidak ada query di Controller.

[ ] Tidak ada query dalam foreach.

[ ] Tidak ada nested IF panjang.

[ ] Tidak menggunakan magic create/update.

[ ] Semua field insert terlihat jelas.

[ ] Transaction menggunakan DB::beginTransaction.

[ ] Error tercatat di Log.

[ ] Developer lain bisa mengikuti alur tanpa membaca seluruh project.

---

# Prinsip Akhir

```
CODE DITULIS UNTUK MANUSIA,

BUKAN HANYA UNTUK MESIN.
```

Kode yang baik adalah kode yang ketika error terjadi, developer dapat menemukan masalah dalam hitungan menit.
