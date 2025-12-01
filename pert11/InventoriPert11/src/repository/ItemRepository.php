<?php
require_once "src/model/Barang.php";
require_once "src/core/JsonStorage.php";

class ItemRepository {
    private array $items = [];
    private int $lastId = 0;
    private JsonStorage $storage;

    public function __construct() {
        $this->storage = new JsonStorage('data/data.json');
        $this->load();
    }

    private function load() {
        $data = $this->storage->getAll();
        if (!empty($data)) {
            foreach ($data as $itemData) {
                $item = new Barang($itemData['id'], $itemData['name'], $itemData['price'], $itemData['stock'], $itemData['category']);
                $this->items[$item->getId()] = $item;
                if ($item->getId() > $this->lastId) {
                    $this->lastId = $item->getId();
                }
            }
        }
    }

    private function save() {
        $this->storage->clear();
        foreach ($this->items as $item) {
            $this->storage->push('items', [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'stock' => $item->getStock(),
                'category' => $item->getCategory()
            ]);
        }
        $this->storage->set('lastId', $this->lastId);
    }

    public function add(string $name, int $price, int $stock, string $category): Item {
        $this->lastId++;
        $item = new Barang($this->lastId, $name, $price, $stock, $category);
        $this->items[$this->lastId] = $item;
        $this->save();
        return $item;
    }

    public function addItem(Item $item): void {
        $this->items[$item->getId()] = $item;
        $this->save();
    }

    public function getAll(): array {
        return $this->items;
    }

    public function getAllItems(): array {
        return $this->getAll();
    }

    public function getById(int $id): ?Item {
        return $this->items[$id] ?? null;
    }

    public function getItemById(int $id): ?Item {
        return $this->getById($id);
    }

    public function update(int $id, string $name, int $price, int $stock, string $category): bool {
        if (!isset($this->items[$id])) return false;
        $item = $this->items[$id];
        $item->setName($name);
        $item->setPrice($price);
        $item->setStock($stock);
        $item->setCategory($category);
        $this->save();
        return true;
    }

    public function updateItem(Item $item): void {
        $this->items[$item->getId()] = $item;
        $this->save();
    }

    public function delete(int $id): bool {
        if (!isset($this->items[$id])) return false;
        unset($this->items[$id]);
        $this->save();
        return true;
    }

    public function deleteItem(int $id): void {
        if (isset($this->items[$id])) {
            unset($this->items[$id]);
            $this->save();
        }
    }

    public function getNextId(): int {
        return $this->lastId + 1;
    }

    public function search(string $keyword): array {
        $result = [];
        foreach ($this->items as $item) {
            if (stripos($item->getName(), $keyword) !== false) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function getByCategory(string $category): array {
        $result = [];
        foreach ($this->items as $item) {
            if (strcasecmp($item->getCategory(), $category) == 0) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function getTotalValue(): int {
        $totalValue = 0;
        foreach ($this->items as $item) {
            $totalValue += $item->getPrice() * $item->getStock();
        }
        return $totalValue;
    }

    public function getLowStockItems(int $limit = 5): array {
        $lowStockItems = [];
        foreach ($this->items as $item) {
            if ($item->getStock() < $limit) {
                $lowStockItems[] = $item;
            }
        }
        return $lowStockItems;
    }
}