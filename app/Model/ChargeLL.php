<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\DownloadExcel;

class ChargeLL extends Model
{
    public $table = 'charge_ll';
    protected $guarded = [];

    /**
     * @param array $newRecord
     * @return bool
     */
    static public function whereLessThanThreeThenPush(Array $newRecord)
    {
        if (env('CHARGE_LL')) {
            //增加抽奖记录
            $count = DB::table('charge_ll')->where('mobile', $newRecord['mobile'])->count();
            if ($count < 3) {
                ChargeLL::create($newRecord);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 这个月是否有大奖抽出
     */
    static public function hasSurpriseInThisMonth()
    {
        $count = DB::table('charge_ll')->where('level', 2)->where('is_bingoo', 1)->whereRaw("date(updated_at) = '" . date('Y-m-d') . "'")->count();
        return $count ? true : false;
    }

    public function get_charge_list()
    {
        $list = DB::table($this->table)->paginate(15);
        return $list;
    }

    public function getBingooHistory(array $condition)
    {
        return DB::table($this->table)->where($condition)->get();
    }


    public function searchByCondition(array $condition, $isForDownload = false)
    {
        $temp = DB::table($this->table)
            ->where(function ($query) use ($condition) {
                // 合同号
                if (!empty($condition['pact_number'])) {
                    $query->where('pact_number', $condition['pact_number']);
                }

                // 提单时间起始值
                if (!empty($condition['td_start_time'])) {
                    $td_start_time = $condition['td_start_time'];
                    $query->whereRaw("unix_timestamp(created_at) >= $td_start_time");
                }

                // 提单时间结束值
                if (!empty($condition['td_end_time'])) {
                    $td_end_time = $condition['td_end_time'];
                    $query->whereRaw("unix_timestamp(created_at) <= $td_end_time");
                }

                // 抽奖时间起始值
                if (!empty($condition['cj_start_time'])) {
                    $cj_start_time = $condition['cj_start_time'];
                    $query->whereRaw("unix_timestamp(created_at) >= $cj_start_time");
                }

                // 抽奖时间结束值
                if (!empty($condition['cj_end_time'])) {
                    $cj_end_time = $condition['cj_end_time'];
                    $query->whereRaw("unix_timestamp(created_at) <= $cj_end_time");
                }

                if (!empty($condition['mobile'])) {
                    $query->where('mobile', $condition['mobile']);
                }

                if (!empty($condition['mbytes'])) {
                    $query->where('mbytes', $condition['mbytes']);
                }

                if (!empty($condition['operators'])) {
                    $query->where('server_type', $condition['operators']);
                }

                if (!empty($condition['charge_result'])) {
                    $query->where('is_bingoo', $condition['charge_result']);
                }
            });
        if (!$isForDownload) {
            $data = $temp->orderBy('id','desc')->paginate(30);
        } else {
            $data = $temp->get();
        }
        return $data;
    }


    // 充值结果转换为文字
    static public function chargeResultText($result)
    {
        switch ($result) {
            case 1:
                $result = '成功';
                break;
            case 0:
                $result = '失败';
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }


    
}