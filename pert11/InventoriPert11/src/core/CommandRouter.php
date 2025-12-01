<?php

class CommandRouter
{
    private array $routes = [];
    private string $defaultCommand = 'help';

    public function __construct()
    {
        $this->registerDefaultRoutes();
    }

    public function register(string $command, callable $handler): void
    {
        $this->routes[$command] = $handler;
    }

    public function setDefaultCommand(string $command): void
    {
        $this->defaultCommand = $command;
    }

    public function dispatch(ArgParser $parser): void
    {
        $command = $parser->getCommand();
        
        if (empty($command)) {
            $command = $this->defaultCommand;
        }

        if (!isset($this->routes[$command])) {
            $this->handleUnknownCommand($command);
            return;
        }

        $handler = $this->routes[$command];
        $handler($parser);
    }

    private function registerDefaultRoutes(): void
    {
        $this->register('help', function(ArgParser $parser) {
            $this->showHelp();
        });

        $this->register('list', function(ArgParser $parser) {
            $this->handleListCommand($parser);
        });

        $this->register('add', function(ArgParser $parser) {
            $this->handleAddCommand($parser);
        });

        $this->register('update', function(ArgParser $parser) {
            $this->handleUpdateCommand($parser);
        });

        $this->register('delete', function(ArgParser $parser) {
            $this->handleDeleteCommand($parser);
        });

        $this->register('search', function(ArgParser $parser) {
            $this->handleSearchCommand($parser);
        });
    }

    private function showHelp(): void
    {
        echo "=== Sistem Inventori CLI ===\n\n";
        echo "Perintah yang tersedia:\n";
        echo "  help                    - Menampilkan bantuan ini\n";
        echo "  list                    - Menampilkan semua item\n";
        echo "  add <nama> <harga> <stok> <kategori> - Menambah item baru\n";
        echo "  update <id> [options]  - Mengupdate item\n";
        echo "  delete <id>             - Menghapus item\n";
        echo "  search <keyword>        - Mencari item\n\n";
        echo "Options untuk update:\n";
        echo "  --name=<nama>           - Update nama\n";
        echo "  --price=<harga>         - Update harga\n";
        echo "  --stock=<stok>          - Update stok\n";
        echo "  --category=<kategori>   - Update kategori\n\n";
        echo "Contoh:\n";
        echo "  php index.php add \"Laptop\" 15000000 10 \"Elektronik\"\n";
        echo "  php index.php update 1 --name=\"Laptop Baru\" --price=12000000\n";
        echo "  php index.php search laptop\n";
    }

    private function handleListCommand(ArgParser $parser): void
    {
        require_once "../repository/ItemRepository.php";
        require_once "../view/ItemView.php";
        
        $repo = new BarangRepository();
        $view = new BarangView();
        
        $items = $repo->getAllItems();
        $view->displayItems($items);
    }

    private function handleAddCommand(ArgParser $parser): void
    {
        require_once "../repository/ItemRepository.php";
        require_once "../view/ItemView.php";
        
        $repo = new BarangRepository();
        $view = new BarangView();
        
        $params = $parser->getParams();
        
        if (count($params) < 4) {
            $view->displayError("Usage: add <nama> <harga> <stok> <kategori>");
            return;
        }
        
        try {
            $id = $repo->getNextId();
            $item = new Barang($id, $params[0], (int)$params[1], (int)$params[2], $params[3]);
            $repo->addItem($item);
            $view->displayMessage("Item berhasil ditambahkan dengan ID $id!");
        } catch (Exception $e) {
            $view->displayError("Gagal menambah item: " . $e->getMessage());
        }
    }

    private function handleUpdateCommand(ArgParser $parser): void
    {
        require_once "../repository/ItemRepository.php";
        require_once "../view/ItemView.php";
        
        $repo = new BarangRepository();
        $view = new BarangView();
        
        $id = (int)$parser->getParam(0);
        $item = $repo->getItemById($id);
        
        if (!$item) {
            $view->displayError("Item dengan ID $id tidak ditemukan.");
            return;
        }
        
        $updated = false;
        
        if ($parser->hasOption('name')) {
            $item->setName($parser->getOption('name'));
            $updated = true;
        }
        
        if ($parser->hasOption('price')) {
            $item->setPrice((int)$parser->getOption('price'));
            $updated = true;
        }
        
        if ($parser->hasOption('stock')) {
            $item->setStock((int)$parser->getOption('stock'));
            $updated = true;
        }
        
        if ($parser->hasOption('category')) {
            $item->setCategory($parser->getOption('category'));
            $updated = true;
        }
        
        if ($updated) {
            $repo->updateItem($item);
            $view->displayMessage("Item dengan ID $id berhasil diupdate!");
            $view->displayItem($item);
        } else {
            $view->displayMessage("Tidak ada perubahan yang dilakukan.");
        }
    }

    private function handleDeleteCommand(ArgParser $parser): void
    {
        require_once "../repository/ItemRepository.php";
        require_once "../view/ItemView.php";
        
        $repo = new BarangRepository();
        $view = new BarangView();
        
        $id = (int)$parser->getParam(0);
        $item = $repo->getItemById($id);
        
        if (!$item) {
            $view->displayError("Item dengan ID $id tidak ditemukan.");
            return;
        }
        
        $repo->deleteItem($id);
        $view->displayMessage("Item dengan ID $id berhasil dihapus!");
    }

    private function handleSearchCommand(ArgParser $parser): void
    {
        require_once "../repository/ItemRepository.php";
        require_once "../view/ItemView.php";
        
        $repo = new BarangRepository();
        $view = new BarangView();
        
        $keyword = $parser->getParam(0);
        
        if (empty($keyword)) {
            $view->displayError("Usage: search <keyword>");
            return;
        }
        
        $items = $repo->getAllItems();
        $foundItems = [];
        
        foreach ($items as $item) {
            if (stripos($item->getName(), $keyword) !== false || 
                stripos($item->getCategory(), $keyword) !== false) {
                $foundItems[] = $item;
            }
        }
        
        if (!empty($foundItems)) {
            $view->displayMessage("Item dengan keyword '$keyword' ditemukan:");
            $view->displayItems($foundItems);
        } else {
            $view->displayError("Tidak ada item dengan keyword '$keyword'.");
        }
    }

    private function handleUnknownCommand(string $command): void
    {
        echo "Error: Perintah '$command' tidak dikenal.\n\n";
        $this->showHelp();
    }
}