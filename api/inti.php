<?php

require './vendor/autoload.php';


// require_once '../stuff/api/api_list.php';
// require_once '../third_party/phpseclib/autoloader.php';
// require_once '../third_party/password_compat/password.php';

// define('EASYWIDIR', '../');
// require_once '../stuff/keyphrasefile.php';
// require_once '../stuff/config.php';
// require_once './function.php';
// require_once '../stuff/methods/class_app.php';
// require_once '../stuff/methods/functions.php';
// require_once '../stuff/methods/class_ftp.php';
// require_once '../stuff/methods/functions_gs.php';
// require_once '../stuff/methods/functions_ssh_exec.php';


class wiApi
{
    public $sql;

    function __construct()
    {
        include 'token.php';
        if ($this->getBearerToken() !== $apiKey) {
            http_response_code(403);
            header('HTTP/1.1 403 Forbidden');
            $jsonArray['status'] = '403';
            $jsonArray['status_text'] = '403 Forbidden';
            echo json_encode($jsonArray);
            die();
        }

        require_once '../stuff/config.php';
        require_once '../stuff/keyphrasefile.php';

        try {
            $dbConnect['type'] = "mysql";
            $dbConnect['host'] = $host;
            $dbConnect['user'] = $user;
            $dbConnect['pwd'] = $pwd;
            $dbConnect['db'] = $db;
            $dbConnect['charset'] = "utf8";

            $dbConnect['connect'] = "{$dbConnect['type']}:host={$dbConnect['host']};dbname={$dbConnect['db']};charset={$dbConnect['charset']}";
            $sql = new \PDO($dbConnect['connect'], $dbConnect['user'], $dbConnect['pwd']);

            $this->sql = $sql;
        } catch (PDOException $error) {
            die($error->getMessage());
        }
    }

    private function getAuthorizationHeader()
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

    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        return null;
    }
}




class addons extends wiApi
{

    function deleteAddon(int $id)
    {
        $sql = $this->sql;
        // Prepare thبe SQL statement
        $query = $sql->prepare("DELETE FROM `addons` WHERE `id` = ?");
        // Bind the parameter value to the statement
        $query->bind_param("i", $id);
        // Execute the statement
        $query_executed = $query->execute();
        // Check if the statement was executed successfully
        if ($query_executed === TRUE) {
            // Close the statement and database connection
            $query->close();
            $sql->close();
            return true;
        } else {
            // Close the statement and database connection
            $query->close();
            $sql->close();
            return false;
        }
    }

    function updateAddon(int $id, string $menudescription, string $active, string $folder, string $addon, string $paddon, string $type, string $configs, string $cmd, string $rmcmd, string $depending)
    {
        $sql = $this->sql;
        // Prepare the SQL statement
        $query = $sql->prepare("UPDATE `addons` SET `menudescription`=?,`active`=?,`folder`=?,`addon`=?,`paddon`=?,`type`=?,`configs`=?,`cmd`=?,`rmcmd`=?,`depending`=? WHERE `id`=? LIMIT 1");

        // Bind the parameter values to the statement
        $query->bind_param("ssssssssssi", $menudescription, $active, $folder, $addon, $paddon, $type, $configs, $cmd, $rmcmd, $depending, $id);

        // Execute the statement
        $query_executed = $query->execute();

        // Check if the statement was executed successfully
        if ($query_executed === TRUE) {
            // Close the statement and database connection
            $query->close();
            $sql->close();
            return true;
        } else {
            // Close the statement and database connection
            $query->close();
            $sql->close();
            return false;
        }
    }

    function getAddonsList()
    {
        $sql = $this->sql;

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
        return json_encode($table);
    }

    function insertAddon(string $type, string $addon, string $paddon, string $folder, string $active, string $menudescription, string $configs, string $cmd, string $rmcmd, string $depending)
    {
        $sql = $this->sql;
        // Prepare the SQL statement
        $query = $sql->prepare("INSERT INTO `addons` (`type`,`addon`,`paddon`,`folder`,`active`,`menudescription`,`configs`,`cmd`,`rmcmd`,`depending`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,0)");

        // Bind the parameter values to the statement
        $query->bind_param("ssssssssss", $type, $addon, $paddon, $folder, $active, $menudescription, $configs, $cmd, $rmcmd, $depending);

        // Execute the statement
        $query_executed = $query->execute();

        // Check if the statement was executed successfully
        if ($query_executed === TRUE) {
            // Close the statement and database connection
            $query->close();
            $sql->close();
            return true;
        } else {
            // Close the statement and database connection
            $query->close();
            $sql->close();
            return false;
        }
    }
}
