 <div class="f-boxr send-user-fans">
    <table border="0" >
        <tr>
            <td>
            	<select id="users_1" multiple="multiple" style="min-width:200px; height:20em;">
	     		</select>
            </td>
            <td width="60" align="center" rowspan="2">
                <button type="button" class="btn btn-primary btn-sm" onclick="$.optionMove('users_2', 'users_1', 1);">
                    <span class="fa fa-2x fa-angle-double-left"> </span>
                </button>
                <br/><br/>
                <button type="button" class="btn btn-info btn-sm" onclick="$.optionMove('users_2', 'users_1');">
                    <span class="fa fa-2x fa-angle-left"> </span>
                </button>
                <br/><br/>
                <button type="button" class="btn btn-info btn-sm" onclick="$.optionMove('users_1', 'users_2');">
                    <span class="fa fa-2x fa-angle-right"> </span>
                </button>
                <br/><br/>
                <button type="button" class="btn btn-primary btn-sm" onclick="$.optionMove('users_1', 'users_2', 1);">
                    <span class="fa fa-2x fa-angle-double-right"> </span>
                </button>
                <input type="hidden" name="users" id="users" />
            </td>
            <td>
            	<select id="users_2" multiple="multiple" style="min-width:200px; height:20em;">
			     	<volist name='user' id='v'>
			        <option value="{$v.id}" class="bgccc">{$v.nickname}</option>
			     	</volist>
			    </select>
            </td>
        </tr>
    </table>
    <div class="input-group-addon">可选择粉丝</div>
</div>
<script type="text/javascript">
    jQuery(function($){
        $("#yz_form").submit(function(){
            var ids = new Array();  

            $("#users_1 option").each(function(){
                ids.push(this.value);
            }) 
            $("#users").val(ids);
        })
        $.optionMove = function(from, to, isAll){
            var from = $("#" + from);
            var to = $("#" + to);
            var list;
            if(isAll){
                list = $('option', from);
            }else{
                list = $('option:selected', from);
            }
            list.each(function(){
                if($('option[value="' + this.value + '"]', to).length > 0){
                    $(this).remove();
                } else {
                    $('option', to).attr('selected',false);
                    to.append(this);
                }
            });
        }

    	$('.uniform').change(function(){ 
    		var type = $(".uniform:checked").val();
    		$('.send-user-type').addClass("hidden");
    		if(type==0){
    			$('.send-user-fans').removeClass("hidden");
    		}
    	});
    });
</script>	 