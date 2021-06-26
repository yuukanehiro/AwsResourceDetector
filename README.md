# AwsResourceDetecter

# install
$ composer install

# Set .env for your environment
$ cp .env.sample .env

edit .env

# use ex. get InstanceList By SecurityGroupId
$ php handler.php {service_alias_name} {security_group_id} {target_key}
  
ex. service alias  
"ec2"
  
ex. command
$ php handler.php ec2 sg-99999xyz PrivateDnsName
