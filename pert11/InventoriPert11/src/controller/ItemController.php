<?php
require_once "src/repository/ItemRepository.php";
require_once "src/view/ItemView.php";
require_once "src/view/MenuView.php";

class ItemController {
    private ItemRepository $repo;
    private ItemView $view;
    private MenuView $menu;

    public function __construct(ItemRepository $repo, ItemView $view, MenuView $menu) {
        $this->repo = $repo;
        $this->view = $view;
        $this->menu = $menu;
    }

    public function run(): void {
        while (true) {
            $choice = $this->menu->displayMainMenu();

            switch ($choice) {
                case '1':
                    $this->showAllItems();
                    break;
                case '2':
                    $this->searchItems();
                    break;
                case '3':
                    $this->addItem();
                    break;
                case '4':
                    $this->updateItem();
                    break;
                case '5':
                    $this->deleteItem();
                    break;
                case '6':
                    $this->view->displayMessage("Terima kasih telah menggunakan program inventori!");
                    exit;
                default:
                    $this->view->displayError("Pilihan tidak valid. Silakan coba lagi.");
            }
        }
    }

    private function showAllItems(): void {
        $items = $this->repo->getAllItems();
        $this->view->displayItems($items);
    }

    private function searchItems(): void {
        while (true) {
            $searchChoice = $this->menu->displaySearchMenu();

            switch ($searchChoice) {
                case '1':
                    $this->searchById();
                    break;
                case '2':
                    $this->searchByName();
                    break;
                case '3':
                    $this->searchByCategory();
                    break;
                case '4':
                    return;
                default:
                    $this->view->displayError("Pilihan tidak valid. Silakan coba lagi.");
            }
        }
    }

    private function searchById(): void {
        $id = (int)$this->menu->getSearchInput('id');
        $item = $this->repo->getItemById($id);

        if ($item) {
            $this->view->displayItem($item);
        } else {
            $this->view->displayError("Item dengan ID $id tidak ditemukan.");
        }
    }

    private function searchByName(): void {
        $name = $this->menu->getSearchInput('name');
        $items = $this->repo->getAllItems();
        $foundItems = [];

        foreach ($items as $item) {
            if (stripos($item->getName(), $name) !== false) {
                $foundItems[] = $item;
            }
        }

        if (!empty($foundItems)) {
            $this->view->displayMessage("Item dengan nama '$name' ditemukan:");
            $this->view->displayItems($foundItems);
        } else {
            $this->view->displayError("Tidak ada item dengan nama '$name'.");
        }
    }

    private function searchByCategory(): void {
        $category = $this->menu->getSearchInput('category');
        $items = $this->repo->getAllItems();
        $foundItems = [];

        foreach ($items as $item) {
            if (stripos($item->getCategory(), $category) !== false) {
                $foundItems[] = $item;
            }
        }

        if (!empty($foundItems)) {
            $this->view->displayMessage("Item dengan kategori '$category' ditemukan:");
            $this->view->displayItems($foundItems);
        } else {
            $this->view->displayError("Tidak ada item dengan kategori '$category'.");
        }
    }

    private function addItem(): void {
        try {
            $input = $this->menu->getItemInput();
            
            $id = $this->repo->getNextId();
            $item = new Barang($id, $input['name'], $input['price'], $input['stock'], $input['category']);
            
            $this->repo->addItem($item);
            $this->view->displayMessage("Item berhasil ditambahkan dengan ID $id!");
        } catch (Exception $e) {
            $this->view->displayError("Gagal menambah item: " . $e->getMessage());
        }
    }

    private function updateItem(): void {
        try {
            $input = $this->menu->getUpdateInput();
            $item = $this->repo->getItemById($input['id']);

            if (!$item) {
                $this->view->displayError("Item dengan ID {$input['id']} tidak ditemukan.");
                return;
            }

            $updated = false;

            if (!empty($input['name'])) {
                $item->setName($input['name']);
                $updated = true;
            }

            if ($input['price'] !== null) {
                $item->setPrice($input['price']);
                $updated = true;
            }

            if ($input['stock'] !== null) {
                $item->setStock($input['stock']);
                $updated = true;
            }

            if (!empty($input['category'])) {
                $item->setCategory($input['category']);
                $updated = true;
            }

            if ($updated) {
                $this->repo->updateItem($item);
                $this->view->displayMessage("Item dengan ID {$input['id']} berhasil diupdate!");
                $this->view->displayItem($item);
            } else {
                $this->view->displayMessage("Tidak ada perubahan yang dilakukan.");
            }
        } catch (Exception $e) {
            $this->view->displayError("Gagal mengupdate item: " . $e->getMessage());
        }
    }

    private function deleteItem(): void {
        try {
            $id = $this->menu->getDeleteInput();
            $item = $this->repo->getItemById($id);

            if (!$item) {
                $this->view->displayError("Item dengan ID $id tidak ditemukan.");
                return;
            }

            if ($this->menu->confirmDelete($item->getName())) {
                $this->repo->deleteItem($id);
                $this->view->displayMessage("Item dengan ID $id berhasil dihapus!");
            } else {
                $this->view->displayMessage("Penghapusan dibatalkan.");
            }
        } catch (Exception $e) {
            $this->view->displayError("Gagal menghapus item: " . $e->getMessage());
        }
    }
}