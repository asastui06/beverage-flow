<?php
session_start();

const DB_FILE = __DIR__ . '/data/db.json';

header('X-Content-Type-Options: nosniff');

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function fail(string $message, int $status = 400): void
{
    json_response(['ok' => false, 'error' => $message], $status);
}

function request_json(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        fail('Некорректный JSON в запросе.');
    }
    return $data;
}

function date_plus_text(int $days): string
{
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ];
    $ts = strtotime(($days >= 0 ? '+' : '') . $days . ' days');
    return date('j', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function default_products(): array
{
    return [
        [
            'id' => 1,
            'name' => 'Лимонад «Классический»',
            'type' => 'carbonated',
            'label' => 'Газированный',
            'vol' => 1.5,
            'price' => 120,
            'rating' => 4.8,
            'min' => 100,
            'desc' => 'Освежающий лимонад с натуральными цитрусовыми маслами для HoReCa.',
            'color' => '#f59e0b',
            'badge' => 'Хит'
        ],
        [
            'id' => 2,
            'name' => 'Морс «Клюква»',
            'type' => 'noncarbonated',
            'label' => 'Негазированный',
            'vol' => 1,
            'price' => 180,
            'rating' => 4.9,
            'min' => 120,
            'desc' => 'Натуральный морс с ягодным концентратом для кафе и столовых.',
            'color' => '#be123c',
            'badge' => 'Натуральный'
        ],
        [
            'id' => 3,
            'name' => 'Холодный чай «Персик»',
            'type' => 'tea',
            'label' => 'Чай',
            'vol' => 0.5,
            'price' => 150,
            'rating' => 4.7,
            'min' => 150,
            'desc' => 'Холодный чай для вендинга и розничных точек.',
            'color' => '#fb923c',
            'badge' => 'Лето'
        ],
        [
            'id' => 4,
            'name' => 'Энергетик «Pulse»',
            'type' => 'energy',
            'label' => 'Энергетик',
            'vol' => 0.45,
            'price' => 210,
            'rating' => 4.6,
            'min' => 200,
            'desc' => 'Тонизирующий напиток для мероприятий и спортивных площадок.',
            'color' => '#7c3aed',
            'badge' => 'B2B'
        ],
        [
            'id' => 5,
            'name' => 'Вода «AquaFlow»',
            'type' => 'water',
            'label' => 'Вода',
            'vol' => 1.5,
            'price' => 65,
            'rating' => 4.5,
            'min' => 300,
            'desc' => 'Питьевая вода ежедневного спроса с удобной логистикой.',
            'color' => '#0ea5e9',
            'badge' => 'Опт'
        ],
        [
            'id' => 6,
            'name' => 'Квас «Хлебный»',
            'type' => 'noncarbonated',
            'label' => 'Негазированный',
            'vol' => 1,
            'price' => 135,
            'rating' => 4.8,
            'min' => 150,
            'desc' => 'Сезонный напиток с традиционным вкусом в ПЭТ и кегах.',
            'color' => '#92400e',
            'badge' => 'Сезон'
        ],
        [
            'id' => 7,
            'name' => 'Тоник «Bitter»',
            'type' => 'carbonated',
            'label' => 'Газированный',
            'vol' => 0.33,
            'price' => 190,
            'rating' => 4.4,
            'min' => 100,
            'desc' => 'Тоник для баров и ресторанов, стабильная газированность.',
            'color' => '#047857',
            'badge' => 'Бар'
        ],
        [
            'id' => 8,
            'name' => 'Сокосодержащий «Манго»',
            'type' => 'juice',
            'label' => 'Сокосодержащий',
            'vol' => 1,
            'price' => 175,
            'rating' => 4.7,
            'min' => 120,
            'desc' => 'Фруктовый вкус с возможностью брендированной этикетки.',
            'color' => '#f97316',
            'badge' => 'Private label'
        ]
    ];
}

function seed_db(): array
{
    return [
        'products' => default_products(),
        'orders' => [
            [
                'id' => 24001,
                'company' => 'ООО «ФрешМаркет»',
                'email' => 'order@fresh.example',
                'productId' => 1,
                'productName' => 'Лимонад «Классический»',
                'liters' => 2400,
                'bottles' => 1600,
                'pallets' => 20,
                'weightKg' => 2880,
                'goodsCost' => 288000,
                'deliveryCost' => 16900,
                'total' => 304900,
                'region' => 'moscow',
                'status' => 'production',
                'createdAt' => date('c', time() - 172800),
                'readyDate' => date_plus_text(1)
            ],
            [
                'id' => 24002,
                'company' => 'ИП Громов',
                'email' => 'gromov@example.ru',
                'productId' => 5,
                'productName' => 'Вода «AquaFlow»',
                'liters' => 5000,
                'bottles' => 3334,
                'pallets' => 42,
                'weightKg' => 6000,
                'goodsCost' => 325000,
                'deliveryCost' => 0,
                'total' => 325000,
                'region' => 'pickup',
                'status' => 'new',
                'createdAt' => date('c', time() - 86400),
                'readyDate' => date_plus_text(2)
            ],
            [
                'id' => 24003,
                'company' => 'Сеть кафе «Север»',
                'email' => 'buy@sever.example',
                'productId' => 2,
                'productName' => 'Морс «Клюква»',
                'liters' => 1200,
                'bottles' => 1200,
                'pallets' => 15,
                'weightKg' => 1320,
                'goodsCost' => 216000,
                'deliveryCost' => 17380,
                'total' => 233380,
                'region' => 'central',
                'status' => 'shipped',
                'createdAt' => date('c', time() - 432000),
                'readyDate' => date_plus_text(-1)
            ]
        ],
        'clients' => [
            ['company' => 'ООО «ФрешМаркет»', 'email' => 'order@fresh.example', 'totalOrders' => 2400, 'ordersCount' => 1],
            ['company' => 'ИП Громов', 'email' => 'gromov@example.ru', 'totalOrders' => 5000, 'ordersCount' => 1],
            ['company' => 'Сеть кафе «Север»', 'email' => 'buy@sever.example', 'totalOrders' => 1200, 'ordersCount' => 1]
        ],
        'users' => [
            [
                'company' => 'Администратор BeverageFlow',
                'email' => 'admin@beverageflow.ru',
                'passwordHash' => password_hash('admin123', PASSWORD_DEFAULT),
                'isAdmin' => true
            ]
        ]
    ];
}

function ensure_db(): void
{
    $dir = dirname(DB_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    if (!file_exists(DB_FILE)) {
        file_put_contents(DB_FILE, json_encode(seed_db(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }
}

function read_db(): array
{
    ensure_db();
    $raw = file_get_contents(DB_FILE);
    $db = json_decode($raw ?: '', true);
    if (!is_array($db)) {
        $db = seed_db();
        write_db($db);
    }
    foreach (['products', 'orders', 'clients', 'users'] as $key) {
        if (!isset($db[$key]) || !is_array($db[$key])) {
            $db[$key] = [];
        }
    }
    return $db;
}

function write_db(array $db): void
{
    ensure_db();
    file_put_contents(DB_FILE, json_encode($db, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function public_user(?array $user): ?array
{
    if (!$user) {
        return null;
    }
    return [
        'company' => $user['company'] ?? '',
        'email' => $user['email'] ?? '',
        'isAdmin' => (bool)($user['isAdmin'] ?? false)
    ];
}

function state_payload(array $db): array
{
    return [
        'ok' => true,
        'products' => $db['products'],
        'orders' => $db['orders'],
        'clients' => $db['clients'],
        'currentUser' => public_user($_SESSION['user'] ?? null)
    ];
}

function type_label(string $type): string
{
    $labels = [
        'carbonated' => 'Газированный',
        'noncarbonated' => 'Негазированный',
        'tea' => 'Чай',
        'energy' => 'Энергетик',
        'water' => 'Вода',
        'juice' => 'Сокосодержащий'
    ];
    return $labels[$type] ?? 'Напиток';
}

function rates(): array
{
    return [
        'moscow' => ['label' => 'Москва и МО', 'base' => 2500, 'kg' => 5],
        'central' => ['label' => 'Центральный ФО', 'base' => 5500, 'kg' => 7],
        'volga' => ['label' => 'Приволжский ФО', 'base' => 8500, 'kg' => 9],
        'south' => ['label' => 'Южный ФО', 'base' => 9000, 'kg' => 10],
        'pickup' => ['label' => 'Самовывоз', 'base' => 0, 'kg' => 0]
    ];
}

function find_product(array $products, int $id): ?array
{
    foreach ($products as $product) {
        if ((int)$product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

function upsert_client(array &$db, string $company, string $email, int $liters): void
{
    foreach ($db['clients'] as &$client) {
        if (($client['email'] ?? '') === $email) {
            $client['company'] = $company;
            $client['totalOrders'] = (int)($client['totalOrders'] ?? 0) + $liters;
            $client['ordersCount'] = (int)($client['ordersCount'] ?? 0) + 1;
            return;
        }
    }
    unset($client);
    $db['clients'][] = [
        'company' => $company,
        'email' => $email,
        'totalOrders' => $liters,
        'ordersCount' => 1
    ];
}

function require_admin(): void
{
    if (empty($_SESSION['user']['isAdmin'])) {
        fail('Нет прав администратора.', 403);
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'state';
$db = read_db();

if ($action === 'exportOrders') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="orders_backup.json"');
    echo json_encode($db['orders'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'state') {
        json_response(state_payload($db));
    }
    fail('Неизвестное действие.');
}

$data = request_json();

switch ($action) {
    case 'login':
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $password = (string)($data['password'] ?? '');
        foreach ($db['users'] as $user) {
            if (($user['email'] ?? '') === $email && password_verify($password, $user['passwordHash'] ?? '')) {
                $_SESSION['user'] = public_user($user);
                json_response(state_payload($db));
            }
        }
        fail('Неверный email или пароль. Для демо: admin@beverageflow.ru / admin123', 401);

    case 'register':
        $company = trim((string)($data['company'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $password = (string)($data['password'] ?? '');
        if ($company === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            fail('Заполните компанию, корректный email и пароль от 6 символов.');
        }
        foreach ($db['users'] as $user) {
            if (($user['email'] ?? '') === $email) {
                fail('Email уже используется.');
            }
        }
        $user = [
            'company' => $company,
            'email' => $email,
            'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
            'isAdmin' => in_array($email, ['admin@beverageflow.ru', 'serbinovitch.alyona@yandex.ru'], true)
        ];
        $db['users'][] = $user;
        write_db($db);
        $_SESSION['user'] = public_user($user);
        json_response(state_payload($db));

    case 'demoLogin':
        $_SESSION['user'] = [
            'company' => 'Администратор BeverageFlow',
            'email' => 'admin@beverageflow.ru',
            'isAdmin' => true
        ];
        json_response(state_payload($db));

    case 'logout':
        unset($_SESSION['user']);
        json_response(state_payload($db));

    case 'createOrder':
        $company = trim((string)($data['company'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $productId = (int)($data['productId'] ?? 0);
        $liters = (int)($data['liters'] ?? 0);
        $region = (string)($data['region'] ?? 'moscow');
        $product = find_product($db['products'], $productId);
        $allRates = rates();
        if ($company === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            fail('Укажите компанию и корректный email.');
        }
        if (!$product) {
            fail('Товар не найден.');
        }
        if ($liters < (int)$product['min']) {
            fail('Минимальный заказ для выбранной позиции: ' . (int)$product['min'] . ' л.');
        }
        if (!isset($allRates[$region])) {
            $region = 'moscow';
        }
        $bottles = (int)ceil($liters / (float)$product['vol']);
        $pallets = (int)ceil($bottles / 80);
        $weight = $bottles * ((float)$product['vol'] + 0.25);
        $goods = $liters * (float)$product['price'];
        $delivery = $allRates[$region]['base'] + $weight * $allRates[$region]['kg'];
        $days = $liters > 500 ? (int)ceil(($liters - 500) / 3000) : 0;
        $order = [
            'id' => (int)round(microtime(true) * 1000),
            'company' => $company,
            'email' => $email,
            'productId' => (int)$product['id'],
            'productName' => $product['name'],
            'liters' => $liters,
            'bottles' => $bottles,
            'pallets' => $pallets,
            'weightKg' => (int)round($weight),
            'goodsCost' => (int)round($goods),
            'deliveryCost' => (int)round($delivery),
            'total' => (int)round($goods + $delivery),
            'region' => $region,
            'status' => 'new',
            'createdAt' => date('c'),
            'readyDate' => date_plus_text($days)
        ];
        array_unshift($db['orders'], $order);
        upsert_client($db, $company, $email, $liters);
        write_db($db);
        json_response(state_payload($db) + ['order' => $order]);

    case 'updateOrderStatus':
        require_admin();
        $id = (int)($data['id'] ?? 0);
        $status = (string)($data['status'] ?? 'new');
        $allowed = ['new', 'production', 'shipped', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            fail('Некорректный статус.');
        }
        foreach ($db['orders'] as &$order) {
            if ((int)$order['id'] === $id) {
                $order['status'] = $status;
                write_db($db);
                json_response(state_payload($db));
            }
        }
        unset($order);
        fail('Заказ не найден.', 404);

    case 'addProduct':
        require_admin();
        $name = trim((string)($data['name'] ?? ''));
        $type = (string)($data['type'] ?? 'carbonated');
        $vol = (float)($data['vol'] ?? 1);
        $price = (float)($data['price'] ?? 0);
        $min = (int)($data['min'] ?? 100);
        $desc = trim((string)($data['desc'] ?? ''));
        if ($name === '' || $vol <= 0 || $price <= 0 || $min <= 0) {
            fail('Заполните название, тару, цену и минимальный объем.');
        }
        $product = [
            'id' => (int)round(microtime(true) * 1000),
            'name' => $name,
            'type' => $type,
            'label' => type_label($type),
            'vol' => $vol,
            'price' => $price,
            'min' => $min,
            'rating' => 4.5,
            'desc' => $desc,
            'color' => '#0f766e',
            'badge' => 'Новинка'
        ];
        array_unshift($db['products'], $product);
        write_db($db);
        json_response(state_payload($db) + ['product' => $product]);

    case 'deleteProduct':
        require_admin();
        $id = (int)($data['id'] ?? 0);
        $db['products'] = array_values(array_filter($db['products'], fn($product) => (int)$product['id'] !== $id));
        write_db($db);
        json_response(state_payload($db));

    default:
        fail('Неизвестное действие.');
}
