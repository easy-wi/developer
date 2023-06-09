<?php


















// function getServerIds($type = "array" | "json"): array
// {
//     $sql = $GLOBALS['sql'];
//     $stmt = $sql->prepare("SELECT `id` FROM `serverlist`");
//     $stmt->execute();
//     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     foreach ($results as $result) {
//         $ids[] = $result['id'];
//     }
//     if ($type = "array" || $type = null) {
//         return $ids;
//     } else {
//         return json_encode($ids);
//     }
// }

// function getServerdetails(int $id)
// {
//     $sql = $GLOBALS['sql'];
//     $query = $sql->prepare("SELECT AES_DECRYPT(`steamServerToken`,?) AS `SteamServerToken` 
//                             FROM `serverlist` WHERE `id`=? LIMIT 1");
//     $query->execute(array($GLOBALS['aeskey'], $id));
//     $results = $query->fetch(PDO::FETCH_ASSOC);
//     return $results;
// }


// function getCurrentUri(): string
// {
//     $getBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
//     // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
//     $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($getBasePath));
//     // Don't take query params into account on the URL
//     if (strstr($uri, '?')) {
//         $uri = substr($uri, 0, strpos($uri, '?'));
//     }
//     // Remove trailing slash + enforce a slash at the start
//     return '/' . trim($uri, '/');
// }


// function secureOutput(array | string $val)
// {
//     return (is_array($val)) ? array_map('secure', $val) : htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
// }



// function getServerInfo(int $id)
// {
//     $query = $sql->prepare("SELECT `active`,`userid`,`stopped`,`running`,`serverip`,`port`,`rootID` FROM `gsswitch` WHERE `id`=? LIMIT 1");
//     $query->execute([$id]);
//     $row = $query->fetch(PDO::FETCH_ASSOC);

//     $serverInfo = [
//         'active' => $row['active'],
//         'userid' => $row['userid'],
//         'serverip' => $row['serverip'],
//         'port' =>  $row['port'],
//         'rootID' => $row['rootID'],
//         'is_running' => ($row['active'] == 'Y') ? true : false
//     ];
//     return $serverInfo;
// }
