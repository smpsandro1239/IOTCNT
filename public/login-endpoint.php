<?php
// Endpoint de login simples sem dependências Laravel
header('Content-Type: applicatio);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email e password são obrigatórios']);
    exit;
}

// Credenciais válidas
$validCredentials = [
    'admin@iotcnt.local' => [
        'password' => 'password',
        'role' => 'admin',
        'name' => 'Administrator',
        'redirect' => '/admin/dashboard-test'
    ],
    'user@iotcnt.local' => [
        'password' => 'password',
        'role' => 'user',
        'name' => 'User',
        'redirect' => '/dashboard-test'
    ]
];

if (!isset($validCredentials[$email])) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Utilizador não encontrado',
        'debug' => [
            'email_searched' => $email,
            'available_emails' => array_keys($validCredentials)
        ]
    ]);
    exit;
}

$user = $validCredentials[$email];

if ($password !== $user['password']) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Password incorrecta',
        'debug' => [
            'user_found' => $email,
            'password_test' => 'Password mismatch'
        ]
    ]);
    exit;
}

// Login bem-sucedido
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Login realizado com sucesso',
    'user' => [
        'email' => $email,
        'name' => $user['name'],
        'role' => $user['role']
    ],
    'redirect' => $user['redirect']
]);
?>
