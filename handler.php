<?php
require 'vendor/autoload.php';
require 'Services/Ec2ClientService.php';

// 実行
echo main($argv[1], $argv[2], $argv[3]);


/**
 * アプリ実行
 *
 *  $ php handler.php {service_alias_name} {service_name} {security_group_id} {target_key}
 *  alias @see ./config/alias.php
 *  ex.
 *  $ php handler.php ec2 sg-920936ea PrivateDnsName
 *
 * @param string $service_alias_name
 * @param string $search_key
 * @param string $target_key
 */
function main(string $service_alias_name, string $search_key, string $target_key): string
{
    $alias = require('./config/alias.php');
    $message = require('./config/message.php');

    if (!isset($alias['service'][$service_alias_name])) {
        return $message['error']['service_alias_not_found'];
    }
    $service = new $alias['service'][$service_alias_name]();
    return json_encode($service->exec($search_key, $target_key));
}
