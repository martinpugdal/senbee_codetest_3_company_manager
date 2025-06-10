<?php
## ajax purpose
require_once('app/functions.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SERVER['HTTP_CONTENT_TYPE']) &&
    str_contains($_SERVER['HTTP_CONTENT_TYPE'], 'application/json')
) {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['action'])) {
        echo json_encode(['error' => 'No action specified']);
        exit;
    }

    switch ($input['action']) {
        case 'create':
            echo json_encode(createCompany($input['cvr']));
            break;
        case 'delete':
            echo json_encode(deleteCompany($input['cvr']));
            break;
        case 'fetch':
            echo json_encode(getCompanies());
            break;
        case 'sync':
            echo json_encode(syncCompany($input['cvr']));
            break;
        default:
            echo json_encode(['error' => 'Unknown action']);
    }

    exit;
}
?>

<!-- just the HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/app.css">
    <title>Senbee Codetest 3 - Company Manager</title>
</head>
<body>

<div id="message"></div>
<form id="companyForm">
    <input type="text" id="cvr" placeholder="Indtast CVR" pattern="\d{8}" inputmode="numeric" required/>
    <button type="submit">Opret virksomhed</button>
</form>

<input type="text" id="companySearch" placeholder="Søg efter virksomhed..."/>
<ul id="companyList"></ul>

<script src="assets/js/app.js"></script>
</body>
</html>