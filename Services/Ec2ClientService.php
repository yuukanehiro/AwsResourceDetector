<?php
namespace Services;
use Aws\Ec2\Ec2Client;

/*
 * Ec2ClientService
 */
class Ec2ClientService
{
    private static $_client = null;

    public function __construct()
    {
        self::init();
    }

    /**
     * Aws\Ec2\Ec2Clientを初期化
     *
     * @return void
     */
    private static function init(): void
    {
        $config = require('./config/aws.php');
        $credentials = [
            'key'    => $config['credentials']['secret_key_id'],
            'secret' => $config['credentials']['secret'],
        ];

        self::$_client = new Ec2Client([
            'version'     => $config['client']['ec2']['version'],
            'region'      => $config['client']['ec2']['region'],
            'credentials' => $credentials,
        ]);
    }

    private static function _validate(string $security_group_id, string $target_key): bool
    {
        if (!isset($security_group_id) || !isset($target_key)) {
            return false;
        }
        return true;
    }

    /**
     * セキュリティグループ idで指定してEC2の指定された値を配列で返却
     *
     * @param string $secrutiy_group_id
     * @param string $target_key
     * @return string
     */
    public static function getListBySecurityGroupIdAndKey(string $security_group_id, string $target_key): array
    {
        // バリデーション
        if (self::_validate($security_group_id, $target_key)) {
            [];
        }
        $data = self::$_client->describeInstances([
            'Filters' => [
                [
                    'Name' => 'instance.group-id',
                    'Values' => [
                        $security_group_id,
                    ],
                ],
            ],
        ]);

        $response = [];
        if (!isset($data["Reservations"])) {
            return [];
        }
        foreach ($data["Reservations"] as $datum) {
            foreach ($datum as $reservation_key => $item) {
                if ($reservation_key === "Instances" &&
                    !is_null($value = self::_parseTargetValue($target_key, $item))
                ) {
                    $response[] = $value;
                }
            }
        }
        // 重複を削除して返却
        return array_values(array_unique($response));
    }

    private static function _parseTargetValue(string $target_key, array $data): ?string
    {
        foreach ($data[0] as $instance_key => $value) {
            if ($instance_key === $target_key && !empty($value)) {
                return $value;
            }
        }
        return null;
    }
}
