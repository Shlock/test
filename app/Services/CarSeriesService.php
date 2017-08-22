<?php namespace YiZan\Services;
use YiZan\Models\CarSeries;
use Pinyin , Validator ,DB;
/**
 * 车系列表
 */
class CarSeriesService extends BaseService 
{  
    /**
     * @param $name 搜索名称
     * @param $page 
     * */
    public static function getlist($name,$page, $pageSize = 20)
    {
        $list = CarSeries::orderBy('pinyin', 'ASC');
        
        if($name == true){
            $list->where('name', 'like', '%'.$name.'%');
        }
        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
        ->take($pageSize)
        ->with('brand')
        ->get()
        ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];        
    }
    public function save($id,$brandId,$pinyin,$name)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
    
        $rules = array(
            'name'   => ['required'],
            'brandId'   => ['required']
        );
        $messages = array
        (
            'name.required'      => 70101,    //  品牌名称不能为空
            'brandId.required'      => 70103    //  品牌logo图片不能为空
        );
        $validator = Validator::make(
            [
                'name'    => $name,
                'brandId'    => $brandId,
            ], $rules, $messages
        );
        if ($validator->fails()) {                  //验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
    
        if (count($logo) > 0) {
            $logo = self::moveCarImage($logo);
            if (!$logo) {
                $result['code'] = 70105;
                return $result;
            }
        } else {
            $logo = '';
        }
        $CarSeries = new CarSeries;
        if($id == ''){
            $CarSeries->name    	= $name;
            $CarSeries->brand_id  = $brandId;
            $CarSeries->pinyin   = Pinyin::Pinyin($pinyin);
            DB::beginTransaction();
            try {
                $CarSeries->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 77006;
            }
        }else{
            DB::beginTransaction();
            try {
                $CarSeries->where('id',$id)->update(
                    [
                        'name' => $name,
                        'brand_id' => $brandId,
                        'pinyin' => Pinyin::Pinyin($pinyin),
                    ]
                );
                DB::commit();
                $result['data'] = $CarSeries->find($id);
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 77007;
            }
        }
        return $result;
    }
    public static function delete($ids)
    {
        if(empty($ids)){
            $result['code'] = 70100; //请选择要删除的数据
            return $result;
        }
        if( !is_array( $ids ) ){
            $ids = [  '0' => $ids   ];
        }
        if( !empty($ids))
        {
            return  CarSeries::whereIn("id", $ids)->delete();
        }
    }
    public static function getBySeriesId($id)
    {
        return CarSeries::find($id);
    }
    public static function getById($brandId)
    {
        return CarSeries::orderBy('pinyin', "ASC")
                    ->where('brand_id',$brandId)->get()->toArray();
    }
}