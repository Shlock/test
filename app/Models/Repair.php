<?php namespace YiZan\Models;

class Repair extends Base {
	protected $visible = ['id', 'district_id', 'build_id', 'room_id','puser_id', 'user_id', 'type', 'seller_id',
		'content', 'images', 'status', 'create_time' , 'dipose_time', 'finish_time', 'seller', 'build', 'room', 'puser', 'types', 'statusStr', 'statusFlowImage', 'statusNameDate'];

	protected $appends = array(
        "statusFlowImage",
        "statusNameDate",
        "statusStr"
    );

	public function seller() {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function district() {
        return $this->belongsTo('YiZan\Models\District', 'district_id', 'id');
    }

    public function build() {
        return $this->belongsTo('YiZan\Models\PropertyBuilding', 'build_id', 'id');
    }

    public function room() {
        return $this->belongsTo('YiZan\Models\PropertyRoom', 'room_id', 'id');
    }

    public function puser() {
        return $this->belongsTo('YiZan\Models\PropertyUser', 'puser_id', 'id');
    }

    public function types() {
        return $this->belongsTo('YiZan\Models\RepairType', 'type', 'id');
    }

    public function getStatusStrAttribute()
    {       
        $status = $this->attributes['status'];
        $statusStr =
        [
           0     => '待处理',
           1     => '处理中',
           2     => '已完成',
        ];

        return array_key_exists($status, $statusStr) ? $statusStr[$status] : "";
    }

    /**
     * 状态流程图片
     * @return string
     */
    public function getStatusFlowImageAttribute()
    {
        $status = $this->attributes['status'];
            switch($status)
            {
                case 0:
                    return 'statusflow_9';

                case 1:
                    return 'statusflow_10';

                case 2:
                    return 'statusflow_11';
            }

    }
    /**
     * 状态时间与名称
     * @return array
     */
    public function getStatusNameDateAttribute()
    {
        $status     = $this->attributes['status'];
        switch($status)	
        {
            case 0:
                return
                    [
                        ["name"=>"提交待处理", "date"=>$this->attributes["create_time"]],
                        ["name"=>"处理中", "date"=>0],
                        ["name"=>"已完成", "date"=>0],
                    ];
            case 1:
                return
                    [
                        ["name"=>"提交待处理", "date"=>$this->attributes["create_time"]],
                        ["name"=>"处理中", "date"=>$this->attributes["dispose_time"]],
                        ["name"=>"已完成", "date"=>0],
                    ];
            case 2:
                return
                    [
                        ["name"=>"提交待处理", "date"=>$this->attributes["create_time"]],
                        ["name"=>"处理中", "date"=>$this->attributes["dispose_time"]],
                        ["name"=>"已完成", "date"=>$this->attributes["finish_time"]],
                    ];

        }
        return [];
    } 
}