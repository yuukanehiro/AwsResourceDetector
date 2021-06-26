<?php
require 'vendor/autoload.php';
require 'Services/Ec2ClientService.php';
use Services\Ec2ClientService;

// $ php handler.php {security_group_id} {target_key}
// ex. $ php handler.php sg-920936ea PrivateDnsName
$ec2 = new Ec2ClientService();
echo json_encode($ec2->getListBySecurityGroupIdAndKey($argv[1], $argv[2]));
