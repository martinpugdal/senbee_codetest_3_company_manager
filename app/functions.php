<?php
require_once('database.php');

$db = new Database('sqlite:' . __DIR__ . '/../data/companies.db', '', '');

function getCompanies(): array
{
    global $db;
    try {
        $stmt = $db->getConnection()->query("SELECT * FROM companies");
        return array_map(function ($company) {
            return [
                'cvr' => $company['cvr_number'],
                'name' => $company['name'],
                'phone' => $company['phone'],
                'email' => $company['email'],
                'address' => $company['address']
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        error_log('Error fetching companies: ' . $e->getMessage());
        return [];
    }
}

function fetchCompanyDataFromCVRAPI($cvr): array
{
    if (!is_numeric($cvr)) {
        return [
            'error' => 'INVALID_CVR_NUMBER'
        ];
    }
    $url = 'https://cvrapi.dk/api?search=' . $cvr . '&country=dk'; // no need for urlencode, since CVR numbers are numeric
    $options = [
        "http" => [
            "header" => "User-Agent: whatever\r"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return [
            'error' => 'COULD_NOT_FETCH_CVR_DATA'
        ];
    }

    $data = json_decode($response, true);

    if (isset($data['error'])) {
        if ($data['error'] === 'QUOTA_EXCEEDED') {
            return [
                'error' => 'CVR_IS_RATELIMITED'
            ];
        }
        return [
            'error' => 'CVR_API_ERROR: ' . $data['error']
        ];
    }

    if ($data) {
        return [
            'cvr' => $cvr,
            'name' => $data['name'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null
        ];
    }

    return [
        'error' => 'NO_COMPANY_FOUND'
    ];
}

function createCompany($cvr): array
{
    $companyData = fetchCompanyDataFromCVRAPI($cvr);

    if (isset($companyData['error'])) {
        return $companyData;
    }

    global $db;
    try {
        $stmt = $db->getConnection()->prepare("INSERT INTO companies (cvr_number, name, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $cvr,
            'name' => $companyData['name'],
            'phone' => $companyData['phone'],
            'email' => $companyData['email'],
            'address' => $companyData['address']
        ]);
        return ['success' => true, 'data' => $companyData];
    } catch (PDOException $e) {
        error_log('Error creating company: ' . $e->getMessage());
        return ['error' => 'DATABASE_ERROR'];
    }
}

function deleteCompany($id): array
{
    global $db;
    try {
        $stmt = $db->getConnection()->prepare("DELETE FROM companies WHERE cvr_number = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        error_log('Error deleting company: ' . $e->getMessage());
        return ['error' => 'DATABASE_ERROR'];
    }
}

function syncCompany($cvr): array
{
    global $db;
    $data = fetchCompanyDataFromCVRAPI($cvr);
    if (isset($data['error'])) return $data;
    $stmt = $db->getConnection()->prepare("UPDATE companies SET name=?, phone=?, email=?, address=? WHERE cvr_number=?");
    $stmt->execute([$data['name'], $data['phone'], $data['email'], $data['address'], $cvr]);
    return ['success' => true, 'data' => $data];
}
