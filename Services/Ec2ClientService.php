<?php
namespace Services;
use Aws\Ec2\Ec2Client;

/*
 * Ec2ClientService
 */
class Ec2ClientService
{
    private static $_client = null;
    private static $_config = null;

    public function __construct()
    {
        self::_init();
    }

    /**
     * Aws\Ec2\Ec2Clientを初期化
     *
     * @return void
     */
    private static function _init(): void
    {
        self::$_config = require('./config/aws.php');
        $credentials = [
            'key'    => self::$_config['credentials']['secret_key_id'],
            'secret' => self::$_config['credentials']['secret'],
        ];

        self::$_client = new Ec2Client([
            'version'     => self::$_config['client']['ec2']['version'],
            'region'      => self::$_config['client']['ec2']['region'],
            'credentials' => $credentials,
        ]);
    }

    /**
     * バリデーション
     *
     * @param string $secrutiy_group_id
     * @param string $target_key
     * @return void
     */
    private static function _validate(string $security_group_id, string $target_key): bool
    {
        if (!isset($security_group_id) ||
            !isset($target_key) ||
            !array_key_exists($target_key, self::$_config['client']['ec2']['target_key'])) {
            return false;
        }
        return true;
    }

    /**
     * メイン処理実行
     *
     * @param string $security_group_id
     * @param string $target_key
     * @return array
     */
    public function exec(string $security_group_id, string $target_key): array
    {
        return self::_getListBySecurityGroupIdAndKey($security_group_id, $target_key);
    }

    /**
     * セキュリティグループ idで指定してEC2の指定された値を配列で返却
     *
     * @param string $secrutiy_group_id
     * @param string $target_key
     * @return string
     */
    private static function _getListBySecurityGroupIdAndKey(string $security_group_id, string $target_key): array
    {
        // バリデーション
        if (self::_validate($security_group_id, $target_key)) {
            return [];
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
        if (!isset($data["Reservations"])) {
            return [];
        }

        $response = [];
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

    /**
     * EC2情報をパース
     *
     * @param string $secrutiy_group_id
     * @param array $data
     * @return ?string
     */
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
