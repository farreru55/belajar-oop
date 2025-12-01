<?php
class MenuView {
    public function displayMainMenu(): int {
        echo "\n=== Sistem Inventaris Barang ===\n";
        echo "1. Tampilkan Semua Barang\n";
        echo "2. Cari Barang\n";
        echo "3. Tambah Barang\n";
        echo "4. Update Barang\n";
        echo "5. Hapus Barang\n";
        echo "6. Keluar\n";
        echo "Pilih menu: ";
        return intval(trim(fgets(STDIN)));
    }

    public function displaySearchMenu(): int {
        echo "\n=== Cari Barang ===\n";
        echo "1. Berdasarkan ID\n";
        echo "2. Berdasarkan Nama\n";
        echo "3. Berdasarkan Kategori\n";
        echo "4. Kembali\n";
        echo "Pilih menu: ";
        return intval(trim(fgets(STDIN)));
    }

    public function getSearchInput(string $field): string {
        echo "Masukkan $field: ";
        return trim(fgets(STDIN));
    }

    public function getItemInput(): array {
        echo "Nama Barang: ";
        $name = trim(fgets(STDIN));

        $price = -1;
        while ($price < 0) {
            echo "Harga: ";
            $price = intval(trim(fgets(STDIN)));
            if ($price < 0) {
                echo "Harga tidak boleh negatif. Silakan coba lagi.\n";
            }
        }

        $stock = -1;
        while ($stock < 0) {
            echo "Stok: ";
            $stock = intval(trim(fgets(STDIN)));
            if ($stock < 0) {
                echo "Stok tidak boleh negatif. Silakan coba lagi.\n";
            }
        }

        echo "Kategori: ";
        $category = trim(fgets(STDIN));

        return [
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'category' => $category
        ];
    }

    public function getUpdateInput(): array {
        echo "Masukkan ID barang yang akan diupdate: ";
        $id = intval(trim(fgets(STDIN)));

        echo "Nama Baru (kosongkan jika tidak ingin diubah): ";
        $name = trim(fgets(STDIN));

        $price = null;
        echo "Harga Baru (kosongkan jika tidak ingin diubah): ";
        $priceInput = trim(fgets(STDIN));
        if ($priceInput !== '') {
            $price = intval($priceInput);
            while ($price < 0) {
                echo "Harga tidak boleh negatif. Silakan coba lagi.\n";
                echo "Harga Baru (kosongkan jika tidak ingin diubah): ";
                $price = intval(trim(fgets(STDIN)));
            }
        }


        $stock = null;
        echo "Stok Baru (kosongkan jika tidak ingin diubah): ";
        $stockInput = trim(fgets(STDIN));
        if ($stockInput !== '') {
            $stock = intval($stockInput);
            while ($stock < 0) {
                echo "Stok tidak boleh negatif. Silakan coba lagi.\n";
                echo "Stok Baru (kosongkan jika tidak ingin diubah): ";
                $stock = intval(trim(fgets(STDIN)));
            }
        }

        echo "Kategori Baru (kosongkan jika tidak ingin diubah): ";
        $category = trim(fgets(STDIN));

        return [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'category' => $category
        ];
    }

    public function getDeleteInput(): int {
        echo "Masukkan ID barang yang akan dihapus: ";
        return intval(trim(fgets(STDIN)));
    }

    public function confirmDelete(string $itemName): bool {
        echo "Apakah Anda yakin ingin menghapus '$itemName'? (y/n): ";
        $confirmation = strtolower(trim(fgets(STDIN)));
        return $confirmation === 'y';
    }
}
