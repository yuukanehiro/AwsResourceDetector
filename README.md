# AwsResourceDetecter

# install
$ composer install

# Set .env for your environment
$ cp .env.sample .env

edit .env

# use ex. get InstanceList By SecurityGroupId
$ php handler.php {security_group_id} {target_key}
  
ex.  
$ php handler.php sg-99999xyz PrivateDnsName
