`#____ DAVLOPER : KALYAN KING 
#____ TELIGERM : KGF CYBER Tarmux Team 
#______ LINK : https://t.me/+DzGy2e840RJiOGZl
<?php

$email = 'hrrana';
$password = 'p@muFZ8FHsi9WZk';
$distributor = '43';


if (!isset($_GET['number']) || empty($_GET['number'])) {
    echo json_encode(['error' => ' Please provide the number parameter like: ?number=019XXXXXXXX']);
    exit;
}
$searchNumber = trim($_GET['number']);

$cookieFile = tempnam(sys_get_temp_dir(), 'bl_cookie');

function getLoginToken($cookieFile) {
    $ch = curl_init('https://blkdms.banglalink.net/Account/Login');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);

    if (preg_match('/name="__RequestVerificationToken" type="hidden" value="([^"]+)"/', $html, $matches)) {
        return $matches[1];
    } else {
        die(json_encode(['error' => ' Login token not found.']));
    }
}

function doLogin($email, $password, $distributor, $token, $cookieFile) {
    $postData = http_build_query([
        '__RequestVerificationToken' => $token,
        'Email' => $email,
        'Password' => $password,
        'Distributor' => $distributor,
    ]);

    $ch = curl_init('https://blkdms.banglalink.net/Account/Login');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

function getSearchToken($cookieFile) {
    $ch = curl_init('https://blkdms.banglalink.net/SmartSearchReport');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);

    if (preg_match('/name="__RequestVerificationToken" type="hidden" value="([^"]+)"/', $html, $matches)) {
        return $matches[1];
    } else {
        die(json_encode(['error' => ' SmartSearchReport token not found.']));
    }
}

function callSmartSearch($searchToken, $searchNumber, $cookieFile) {
    $postFields = http_build_query([
        '__RequestVerificationToken' => $searchToken,
        'SearchType' => '2',
        'SearchValue' => $searchNumber,
        'FromSC' => '',
        'ToSC' => '',
        'TotalQty' => '',
    ]);

    $ch = curl_init('https://blkdms.banglalink.net/SmartSearchReport');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Referer: https://blkdms.banglalink.net/SmartSearchReport',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function parseResponseToJSON($html) {
    $text = strip_tags($html);
    $text = preg_replace('/\s+/', ' ', $text);

    $pattern = '/SIM No:\s*(\S+)\s*MSISDN:\s*(\S+).*?Location:\s*(.*?)\s*Activation Status:\s*(.*?)\s*Distributor:\s*(.*?)\s*SAF Status:\s*(.*?)\s*Retailer:\s*(.*?)\s*Promotion:\s*(.*?)\s*Warehouse:\s*(.*?)\s*Product:\s*(.*?)\s*Lifting price:\s*(\d+)\s*Selling price:\s*(\d+)\s*Verification Date:\s*(.*?)\s*Activation Date:\s*(\d{2}-[A-Z]{3}-\d{4})/i';

    if (preg_match($pattern, $text, $matches)) {
        $data = [
            'sim_no' => $matches[1],
            'msisdn' => $matches[2],
            'location' => trim($matches[3]),
            'activation_status' => trim($matches[4]),
            'distributor' => trim($matches[5]),
            'saf_status' => trim($matches[6]),
            'retailer' => trim($matches[7]),
            'promotion' => trim($matches[8]),
            'warehouse' => trim($matches[9]),
            'product' => trim($matches[10]),
            'lifting_price' => (int)$matches[11],
            'selling_price' => (int)$matches[12],
            'verification_date' => trim($matches[13]),
            'activation_date' => trim($matches[14])
        ];

        return $data;
    } else {
        return ['error' => ' Data could not be parsed correctly.'];
    }
}

function getSmartSearchToken2($cookieFile) {
    $ch = curl_init('https://blkdms.banglalink.net/SmartSearchReport');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);

    if (preg_match('/name="__RequestVerificationToken" type="hidden" value="([^"]+)"/', $html, $matches)) {
        return $matches[1];
    } else {
        die(json_encode(['error' => ' SmartSearch token not found.']));
    }
}

function extractRetailerId($fullRetailerId) {
    if (preg_match('/^(R\d+)/', $fullRetailerId, $matches)) {
        return $matches[1];
    }
    return $fullRetailerId;
}

function searchReportAsJson($retailerId, $token, $cookieFile) {
    $postData = http_build_query([
        '__RequestVerificationToken' => $token,
        'SearchType' => '4',
        'SearchValue' => $retailerId,
        'developer' => 'Kalyan king',
        'Teligerm' => 'KGF_CYBER_TEAM',
    ]);

    $ch = curl_init('https://blkdms.banglalink.net/SmartSearchReport');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0',
            'Referer: https://blkdms.banglalink.net/SmartSearchReport',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    
    $text = strip_tags($response);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);

    
    $info = [];

    
    $patterns = [
        '/Retailer:\s*([^\s]+)/' => 'retailer_id',
        '/Retailer Name:\s*(.*?)\s+RSO CODE:/' => 'retailer_name',
        '/RSO CODE:\s*(.*?)\s+ROUTE CODE-1:/' => 'rso_code',
        '/ROUTE CODE-1:\s*(.*?)\s+Distributor:/' => 'route_code_1',
        '/Distributor:\s*(.*?)\s+Itop-up SR Number:/' => 'distributor',
        '/Itop-up SR Number:\s*([^\s]+)/' => 'topup_sr_number',
        '/Transaction Mobile No:\s*([^\s]+)/' => 'transaction_mobile_no',
        '/Retailer Type:\s*(.*?)\s+Itop-up Number:/' => 'retailer_type',
        '/Itop-up Number:\s*([^\s]+)/' => 'itopup_number',
        '/Retailer Contact No:\s*([^\s]+)/' => 'retailer_contact_no',
        '/Contact Person:\s*(.*?)\s+Contact Number:/' => 'contact_person',
        '/Contact Number:\s*([^\s]+)/' => 'contact_number',
        '/Create Date:\s*([^\s]+)/' => 'create_date',
        '/Status:\s*([^\s]+)/' => 'status',
        '/Verified:\s*([^\s]+)/' => 'verified',
        '/Termination Status:\s*([^\s]+)/' => 'termination_status',
        '/District:\s*(.*?)\s+Thana:/' => 'district',
        '/Thana:\s*(.*?)\s+BTS Code:/' => 'thana',
        '/BTS Code:\s*(.*?)\s+Address:/' => 'bts_code',
        '/Address:\s*(.*?)\s+NID:/' => 'address',
        '/NID:\s*([^\s]+)/' => 'nid',
        '/Cluster Market:\s*(.*?)\s+SIM Seller:/' => 'cluster_market',
        '/SIM Seller:\s*([^\s]+)/' => 'sim_seller'
    ];

    foreach ($patterns as $pattern => $key) {
        if (preg_match($pattern, $text, $matches)) {
            $info[$key] = trim($matches[1]);
        } else {
            $info[$key] = null;
        }
    }

    return $info;
}


try {
    $loginToken = getLoginToken($cookieFile);
    $loginSuccess = doLogin($email, $password, $distributor, $loginToken, $cookieFile);
    
    if (!$loginSuccess) {
        throw new Exception('Login failed');
    }
    
    $searchToken = getSearchToken($cookieFile);
    $response = callSmartSearch($searchToken, $searchNumber, $cookieFile);
    $firstData = parseResponseToJSON($response);

    if (isset($firstData['retailer']) && !empty($firstData['retailer'])) {
        $fullRetailerId = $firstData['retailer'];
        $retailerId = extractRetailerId($fullRetailerId);
        
        $secondToken = getSmartSearchToken2($cookieFile);
        $secondData = searchReportAsJson($retailerId, $secondToken, $cookieFile);
        
        $combinedData = array_merge($firstData, $secondData);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($combinedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge($firstData, ['retailer_info' => 'Retailer ID not found']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => ' Error: ' . $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} finally {
    if (file_exists($cookieFile)) {
        unlink($cookieFile);
    }
}

?>`
