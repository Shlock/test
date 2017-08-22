<?php namespace YiZan\Services;
use YiZan\Models\CarBrand;
use Validator,DB ,Pinyin;
/**
 * 车辆品牌列表
 */
class CarBrandService extends BaseService 
{  
    /**
     * @param $name 搜索名称
     * @param $page 
     * */
    public static function carlist($name,$page, $pageSize = 20)
    {
        $list = CarBrand::orderBy('pinyin', 'ASC');
        if($name == true){
            $list->where('name', 'like', '%'.$name.'%');
        }
        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
        ->take($pageSize)
        ->get()
        ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];   
    }
    
    
    public static function getById($id)
    {
         return CarBrand::where('id',$id)->first();
    }
    public function savecar($id,$name,$ename,$logo,$pinyin)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        
        $rules = array(
            'name'   => ['required'],
            'logo'   => ['required']
        );
        $messages = array
        (
            'name.required'      => 70101,    //  品牌名称不能为空
            'logo.required'      => 70103    //  品牌logo图片不能为空
        );
        $validator = Validator::make(
            [
                'name'    => $name,
                'logo'    => $logo,
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
        $CarBrand = new CarBrand;
        if($id == ''){
            $CarBrand->name    	= $name;
            $CarBrand->ename    = null;
            $CarBrand->logo     = $logo;
            $CarBrand->pinyin   = Pinyin::Pinyin($name);
            $CarBrand->is_index = 0;
            $CarBrand->is_hot   = 0;
            
            
            DB::beginTransaction();
            try {
                $CarBrand->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 77006;
            }
        }else{
            DB::beginTransaction();
            try {
                $CarBrand->where('id',$id)->update(
                      [
                          'name' => $name,
                          'ename' => null,
                          'logo' => $logo,
                          'pinyin' => Pinyin::Pinyin($name),
                      ]
                 );
                DB::commit();
                $result['data'] = $CarBrand->find($id);
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 77007;
            }
        }
        return $result;
    }
    public static function getList($page, $pageSize = 20)
    {
        $list = CarBrand::orderBy('pinyin', "ASC")
        ->get()
        ->toArray();
        return $list;
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
            return  CarBrand::whereIn("id", $ids)->delete();
        }
    }
    
}