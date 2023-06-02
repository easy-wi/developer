<?php
function getAuthorizationHeader()
{
    $headers = null;
    if (!empty($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        static $requestHeaders = null;
        if ($requestHeaders === null) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        }
        if (!empty($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getBearerToken()
{
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}


function getServerIds($type = "array" | "json"): array
{
    $sql = $GLOBALS['sql'];
    $stmt = $sql->prepare("SELECT `id` FROM `serverlist`");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $result) {
        $ids[] = $result['id'];
    }
    if ($type = "array" || $type = null) {
        return $ids;
    } else {
        return json_encode($ids);
    }
}

function getServerdetails(int $id)
{
    $sql = $GLOBALS['sql'];
    $query = $sql->prepare("SELECT AES_DECRYPT(`steamServerToken`,?) AS `SteamServerToken` 
                            FROM `serverlist` WHERE `id`=? LIMIT 1");
    $query->execute(array($GLOBALS['aeskey'], $id));
    $results = $query->fetch(PDO::FETCH_ASSOC);
    return $results;
}


function getCurrentUri(): string
{
    $getBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
    // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
    $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($getBasePath));
    // Don't take query params into account on the URL
    if (strstr($uri, '?')) {
        $uri = substr($uri, 0, strpos($uri, '?'));
    }
    // Remove trailing slash + enforce a slash at the start
    return '/' . trim($uri, '/');
}


function getAddonsList()
{
    $sql = $GLOBALS['sql'];

    $query = $sql->prepare("SELECT 
    `id`,
    `menudescription`,
    `active`,
    `type`,
    `addon`,
    `folder`,
    `configs`,
    `cmd`,
    `rmcmd`
    FROM `addons`");

    $query2 = $sql->prepare("SELECT GROUP_CONCAT(DISTINCT s.`shorten` ORDER BY s.`shorten` ASC SEPARATOR ', ') AS `list`, COUNT(s.`id`) AS `amount` FROM `addons_allowed` AS a INNER JOIN `servertypes` AS s ON a.`servertype_id`=s.`id` WHERE a.`addon_id`=? ");
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $query2->execute(array($row['id']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
            $gamesList = [
                'amount' => $row2['amount'],
                'lsit' => $row2['list']
            ];
        }

        $table[] = [
            'id' => $row['id'],
            'addon' => $row['addon'],
            'folder' => $row['folder'],
            'configs' => $row['configs'],
            'cmd' => $row['cmd'],
            'rmcmd' => $row['rmcmd'],
            'active' => $row['active'],
            'gametype' => $gamesList,
            'description' => $row['menudescription'],
            'type' => $row['type']
        ];
    }
    echo json_encode($table);
}

function secureOutput(array | string $val)
{
    return (is_array($val)) ? array_map('secure', $val) : htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}


function deleteAddon($id)
{

    // Prepare the SQL statement
    $query = $conn->prepare("DELETE FROM `addons` WHERE `id` = ?");

    // Bind the parameter value to the statement
    $query->bind_param("i", $id);

    // Execute the statement
    $query_executed = $query->execute();

    // Check if the statement was executed successfully
    if ($query_executed === TRUE) {
        // Close the statement and database connection
        $query->close();
        $conn->close();
        return true;
    } else {
        // Close the statement and database connection
        $query->close();
        $conn->close();
        return false;
    }
}


function insertAddon($type, $addon, $paddon, $folder, $active, $menudescription, $configs, $cmd, $rmcmd, $depending)
{
    // Prepare the SQL statement
    $query = $conn->prepare("INSERT INTO `addons` (`type`,`addon`,`paddon`,`folder`,`active`,`menudescription`,`configs`,`cmd`,`rmcmd`,`depending`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,0)");

    // Bind the parameter values to the statement
    $query->bind_param("ssssssssss", $type, $addon, $paddon, $folder, $active, $menudescription, $configs, $cmd, $rmcmd, $depending);

    // Execute the statement
    $query_executed = $query->execute();

    // Check if the statement was executed successfully
    if ($query_executed === TRUE) {
        // Close the statement and database connection
        $query->close();
        $conn->close();
        return true;
    } else {
        // Close the statement and database connection
        $query->close();
        $conn->close();
        return false;
    }
}
// $result = insertAddon($type, $addon, $paddon, $folder, $active, $menudescription, $configs, $cmd, $rmcmd, $depending);



function updateAddon($id, $menudescription, $active, $folder, $addon, $paddon, $type, $configs, $cmd, $rmcmd, $depending)
{

    // Prepare the SQL statement
    $query = $conn->prepare("UPDATE `addons` SET `menudescription`=?,`active`=?,`folder`=?,`addon`=?,`paddon`=?,`type`=?,`configs`=?,`cmd`=?,`rmcmd`=?,`depending`=? WHERE `id`=? LIMIT 1");

    // Bind the parameter values to the statement
    $query->bind_param("ssssssssssi", $menudescription, $active, $folder, $addon, $paddon, $type, $configs, $cmd, $rmcmd, $depending, $id);

    // Execute the statement
    $query_executed = $query->execute();

    // Check if the statement was executed successfully
    if ($query_executed === TRUE) {
        // Close the statement and database connection
        $query->close();
        $conn->close();
        return true;
    } else {
        // Close the statement and database connection
        $query->close();
        $conn->close();
        return false;
    }
}
// $result = updateAddon($id, $menudescription, $active, $folder, $addon, $paddon, $type, $configs, $cmd, $rmcmd, $depending);

// if ($result === TRUE) {
//     echo "Addon updated successfully";
// } else {
//     echo "Error updating addon";
// }


function getServerInfo(int $id)
{
    $query = $sql->prepare("SELECT `active`,`userid`,`stopped`,`running`,`serverip`,`port`,`rootID` FROM `gsswitch` WHERE `id`=? LIMIT 1");
    $query->execute([$id]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    $serverInfo = [
        'active' => $row['active'],
        'userid' => $row['userid'],
        'serverip' => $row['serverip'],
        'port' =>  $row['port'],
        'rootID' => $row['rootID'],
        'is_running' => ($row['active'] == 'Y') ? true : false
    ];
    return $serverInfo;
}
