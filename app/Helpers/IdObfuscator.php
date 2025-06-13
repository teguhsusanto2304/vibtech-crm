<?php

namespace App\Helpers;

class IdObfuscator
{
    // Kunci rahasia untuk XOR. Gunakan angka besar acak yang unik untuk aplikasi Anda.
    // Pastikan ini tetap rahasia. Jangan ubah setelah data mulai di-obfuscate.
    private static $xorKey = 0x5a3c9e7b; // Contoh: ganti dengan angka heksadesimal acak Anda
    private static $offset = 123456789; // Offset untuk membuat lebih bervariasi

    // Alphabet untuk konversi Base62 (angka, huruf kecil, huruf besar)
    private static $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private static $base; // Akan diinisialisasi di constructor/pertama kali dipakai

    public function __construct()
    {
        if (self::$base === null) {
            self::$base = strlen(self::$alphabet);
        }
    }

    /**
     * Mengubah integer menjadi string yang di-obfuscate (Base62 + XOR).
     *
     * @param int $id ID integer yang akan di-obfuscate.
     * @return string String hasil obfuscate.
     */
    public static function encode(int $id): string
    {
        new static(); // Memastikan alphabet dan base terinisialisasi

        // Tambahkan offset untuk membuat ID tidak sequential dan mengurangi kemungkinan 0 setelah XOR
        $processedId = $id + self::$offset;
        
        // Lakukan operasi XOR dengan kunci rahasia
        $xoredId = $processedId ^ self::$xorKey;

        // Konversi hasil XOR ke Base62
        $encoded = '';
        if ($xoredId === 0) {
            return '0';
        }
        while ($xoredId > 0) {
            $encoded = self::$alphabet[$xoredId % self::$base] . $encoded;
            $xoredId = floor($xoredId / self::$base);
        }

        // Untuk membuatnya sedikit lebih panjang dan menyerupai UUID, Anda bisa menambahkan padding.
        // Namun, padding statis akan membuatnya lebih mudah dipecahkan.
        // Untuk UUID-like, kita bisa mencoba formatnya, tapi ini tidak akan membuatnya memiliki panjang UUID sungguhan.
        // Contoh: Pangkas atau pad dengan nol jika Anda ingin panjang minimum,
        // tapi ini bisa menyebabkan benturan jika ID sangat kecil.
        
        // Untuk tujuan obfuscasi dan URL-safe, Base62 saja sudah cukup efektif.
        return $encoded;
    }

    /**
     * Mengubah string yang di-obfuscate kembali menjadi integer.
     *
     * @param string $obfuscatedId String yang akan di-decode.
     * @return int ID integer asli.
     */
    public static function decode(string $obfuscatedId): int
    {
        new static(); // Memastikan alphabet dan base terinisialisasi

        // Decode dari Base62
        $decoded = 0;
        for ($i = 0, $len = strlen($obfuscatedId); $i < $len; $i++) {
            $char = $obfuscatedId[$i];
            $pos = strpos(self::$alphabet, $char);
            if ($pos === false) {
                // Karakter tidak valid dalam alphabet
                throw new \InvalidArgumentException('Invalid obfuscated ID character: ' . $char);
            }
            $decoded = $decoded * self::$base + $pos;
        }

        // Balikkan operasi XOR
        $xoredId = $decoded ^ self::$xorKey;

        // Kurangi offset untuk mendapatkan ID asli
        $originalId = $xoredId - self::$offset;

        return $originalId;
    }
}