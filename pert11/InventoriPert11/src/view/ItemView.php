<?php
class ItemView {
    public function displayItems(array $items) {
        echo "=== Daftar Barang ===\n";
        if (empty($items)) {
            echo "Tidak ada data barang.\n";
            return;
        }

        foreach ($items as $item) {
            $this->displayItem($item);
        }
    }

    public function displayItem(Barang $item) {
        echo "ID: " . $item->getId() . "\n";
        echo "Nama: " . $item->getName() . "\n";
        echo "Harga: Rp " . number_format($item->getPrice()) . "\n";
        echo "Stok: " . $item->getStock() . "\n";
        echo "Kategori: " . $item->getCategory() . "\n";
    }

    public function displayMessage(string $message) {
        echo "$message\n";
    }

    public function displayError(string $message) {
        echo "Error: $message\n";
    }

    public function showSearchResult(array $items) {
        echo "=== Hasil Pencarian ===\n";
        if (empty($items)) {
            echo "Tidak ada barang yang cocok.\n";
            return;
        }

        foreach ($items as $item) {
            echo "{$item->getId()} - {$item->getName()} (Rp {$item->getPrice()}, Stok {$item->getStock()}) - Category: {$item->getCategory()}\n";
        }
    }

    public function displayTotalValue(int $totalValue) {
        echo "=== Total Nilai Inventaris ===\n";
        echo "Total nilai: Rp " . number_format($totalValue) . "\n";
    }

    public function displayLowStockAlert(array $items) {
        if (empty($items)) {
            return;
        }

        echo "\n!!! PERINGATAN: STOK MENIPIS !!!\n";
        foreach ($items as $item) {
            echo "- {$item->getName()} (Stok: {$item->getStock()})\n";
        }
    }
}
