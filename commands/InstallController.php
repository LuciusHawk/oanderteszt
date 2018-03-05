<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Monitor;
use yii\console\Controller;

/**
 * Add 50 monitor to db
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InstallController extends Controller
{
    public function actionIndex()
    {
        $randMonitors = array();
        for ($i = 0; $i < 50; $i++) {
            $randMonitors[] = [
                'entityName' => 'Monitor' . rand(44444, 999999),
                'brand' => 'ChinaTech' . rand(3, 333),
            ];
        }
        foreach ($randMonitors as $randMonitor) {
            $monitor = new Monitor();
            $monitor->createMonitor();
            $monitor->entityName = $randMonitor['entityName'];
            $monitor->brand = $randMonitor['brand'];
            $monitor->save();
            echo "$monitor->entityName\r\n";
        }
    }
}
