<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Behavior;
/**
 * 自动执行任务
 */
class CronRunBehavior {

    public function run(&$params) 
    {
        if (C('CRON_CONFIG_ON')) 
        {

            $this -> checkTime();

        }
    }

    private function checkTime() 
    {
        if (F('CRON_CONFIG')) 
        {

            $crons = F('CRON_CONFIG');

        } 
        else if (C('CRON_CONFIG')) 
        {

            $crons = C('CRON_CONFIG');

        }
        if (!empty($crons) && is_array($crons))
         {
            $update = false;
            $log = array();
            foreach ($crons as $key => $cron) 
            {
                if (empty($cron[2]) || $_SERVER['REQUEST_TIME'] > $cron[2]) 
                {
                    G('cronStart');
                    R($cron[0]);
                    G('cronEnd');
                    $_useTime = G('cronStart', 'cronEnd', 6);
                    $cron[2] = $_SERVER['REQUEST_TIME'] + $cron[1];
                    $crons[$key] = $cron;
                    $log[] = 'Cron:' . $key . ' Runat ' . date('Y-m-d H:i:s') . ' Use ' . $_useTime . ' s ' . "\r\n";
                    $update = true;
                }
            }
            if ($update) 
            {
                \Think\Log::write(implode('', $log));
                F('CRON_CONFIG', $crons);
            }
        }
    }
}