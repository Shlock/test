<?php namespace YiZan\Template;

use Blade, Lang, Config, View;

class YiZanTemplate {
    static $mapIndex = 0;
    static $sections = [];
    private $_tagAttrs = [];

    public function init() {
        Blade::extend(function($view, $compiler) {
            $content = preg_replace_callback("/@yizan_include\(('|\")(.+?)\\1\)/is", array($this, 'formatInclude'), $view);
            $content = preg_replace_callback("/@yizan_section\(('|\")(.+?)\\1\)\r?\n(.*?)@yizan_stop/is", array($this, 'formatSection'), $content);
            $content = preg_replace_callback("/@yizan_yield\(('|\")(.+?)\\1\)\r?\n(.*?)@yizan_stop/is", array($this, 'formatYield'), $content);
            $content = preg_replace_callback("/@yizan_begin(.*)@yizan_end/is", array($this, 'formatTag'), $content);
            $content = str_replace(['<php>', '</php>'], ['<?php', '?>'], $content);
            return $content;
        });
    }

    public function formatInclude($match){
        $path = explode('.', $match[2]);
        $file = array_pop($path).'.blade.php';
        $path = base_path().'/resources/views/'.implode('/', $path).'/'.$file;
        return file_get_contents($path);
    }

    public function formatSection($match){
        self::$sections[$match[2]] = trim($match[3]);
        return '';
    }

    public function formatYield($match){
        return isset(self::$sections[$match[2]]) ? self::$sections[$match[2]] : trim($match[3]);
    }

    /**
     * 格式化自定义标签
     * @param  [type] $match [description]
     * @return [type]        [description]
     */
    private function formatTag($match) {
        return preg_replace_callback("/<yz:(.*?)(\s(?<attrs>.*?))?>(?<content>.*?)<\/yz:\\1>/is", array($this, 'parseTag'), $match[1]);
    }

    /**
     * 格式化自定义标签回调函数
     * @param  [type] $match [description]
     * @return [type]        [description]
     */
    private function parseTag($match) {
        $content 	= trim($match['content']);
        $tag 		= ucfirst($match[1]);
        $parse_tag 	= 'parse' . $tag;
        $pre_tag    = 'pre' . $tag;
        if (method_exists($this, $parse_tag) || method_exists($this, $pre_tag)) {
            $pre_tag = 'pre' . $tag;
            if (method_exists($this, $pre_tag)) {
                $content = $this->$pre_tag($content);
            }

            $attrs   = $this->parseAttrs(trim($match['attrs']), $content);

            /*格式化通用属性*/
            $attrs['attr']  = !isset($attrs['attr']) ?  '' : ' '.$attrs['attr'];
            $attrs['css']   = !isset($attrs['css']) ?  '' : ' '.$attrs['css'];
            $attrs['style'] = !isset($attrs['style']) ? '' : ' style="'.trim($attrs['style']).'"';
            $this->_tagAttrs[$tag] = $attrs;

            if (!empty($content) && preg_match("/<yz:(.*?)(?:\s.*?)?>.*?<\/yz:\\1>/is", $content) > 0) {
                $content = preg_replace_callback("/<yz:(.*?)(\s(?<attrs>.*?))?>(?<content>.*?)<\/yz:\\1>/is", array($this, 'parseTag'), $content);
            }

            if (method_exists($this, $parse_tag)) {
                $content = $this->$parse_tag($attrs, $content);
            }
        }
        return $content;
    }

    /**
     * 格式化标签属性
     * @param  string $attrs 属性字符串
     * @return array         属性数组
     */
    private function parseAttrs($attrsStr, &$content) {
        $attrs = [];
        //检测是否有xml格式属性
        if (!empty($content) && preg_match("/^\\s*<attrs>.*?<\/attrs>/is", $content, $xml) > 0) {
            $content = preg_replace("/^\\s*<attrs>.*?<\/attrs>/is", '', $content);
            $xml    = @simplexml_load_string($xml[0], 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml && $xml->count() > 0) {
                foreach ($xml->children() as $xml_attr) {
                    $val = trim(strval($xml_attr));
                    if ($val === '') {
                        continue;
                    }
                    $attrs[$xml_attr->getName()] = $val;
                }
            }
        }

        if (empty($attrsStr)) {
            return $attrs;
        }

        $xml	= '<tpl><tag '.$attrsStr.' /></tpl>';
        $xml    = @simplexml_load_string($xml);
        if (!$xml) {
            return $attrs;
        }
        $xml 	= (array)($xml->tag->attributes());
        $xml 	= array_diff_key($xml['@attributes'], $attrs);

        foreach($xml as $key => $val){
            $attrs[$key] = trim($val);
        }
        return $attrs;
    }

    /**
     * 获取标签属性
     * @param  [type] $tag [description]
     * @return [type]      [description]
     */
    private function getTagAttrs($tag) {
        return $this->_tagAttrs[$tag];
    }

    /**
     * 预约格式化标签
     * @param  [type] $content  [description]
     * @param  [type] $searchs  [description]
     * @param  [type] $replaces [description]
     * @return [type]           [description]
     */
    private function preTags($content, $searchs, $replaces) {
        return str_replace($searchs, $replaces, $content);
    }
    private function prePregTags($content, $searchs, $replaces) {
        return preg_replace($searchs, $replaces, $content);
    }

    /**LIST格式化BEGIN**/

    /**
     * 预格式化列表相关标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preList($content) {
        return $this->preTags($content, [
            '<search', '</search>',
            '<tabs', '</tabs>',
            '<btns', '</btns>',
            '<table', '</table>'
        ],[
            '<yz:listSearch', '</yz:listSearch>',
            '<yz:tabs', '</yz:tabs>',
            '<yz:listBtns', '</yz:listBtns>',
            '<yz:listTable', '</yz:listTable>'
        ]);
    }

    /**
     * 格式化列表标签
     * @param  array  $attrs   属性
     * @param  string $content 内容
     * @return string          格式化后的内容
     */
    private function parseList($attrs, $content = '') {
        $id          = !isset($attrs['id']) ? 'checkList' : $attrs['id'];
        $html .= '<div id="'.$id .'" class="'.$attrs['css'].'"'.$style.$attrs['attr'].'>
                    '.$content.'
                </div>';
        return $html;
    }

    /**
     * 预格式化列表搜索标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preListSearch($content) {
        return $this->preTags($content, [
            '<row', '</row>',
            '<item', '</item>',
            '<btn', '</btn>',
            '<linkbtn', '</linkbtn>'
        ], [
            '<yz:listSearchRow', '</yz:listSearchRow>',
            '<yz:fitem search="1"', '</yz:fitem>',
            '<yz:btn', '</yz:btn>',
            '<yz:linkBtn', '</yz:linkBtn>'
        ]);
    }

    /**
     * 格式化列表搜索标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function parseListSearch($attrs, $content = '') {
        $id         = !isset($attrs['id']) ? 'yzForm' : $attrs['id'];
        $action     = !isset($attrs['action']) ? ACTION_NAME : $attrs['action'];
        $method     = !isset($attrs['method']) ? 'post' : $attrs['method'];
        $target     = !isset($attrs['target']) ? '_self' : $attrs['target'];
        $url        = !isset($attrs['url']) ? u(CONTROLLER_NAME.'/'.$action) : $attrs['url'];
        $isajax     = !isset($attrs['ajax']) ? false : true;
        $form_css   = $isajax ? 'ajax-form' : '';

        $html = '<div class="u-ssct clearfix'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].'>
                    <?php $search_args = Input::all(); ?>
                    <form id="'.$id.'" class="'.$form_css.'" name="'.$id.'" method="'.$method.'" action="'.$url.'" target="'.$target.'">
                        '.$content.'
                    </form>
                </div>';
        return $html;
    }

    /**
     * 格式化列表搜索行标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function parseListSearchRow($attrs, $content = '') {
        $html .= '<div class="search-row clearfix'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].'>
                      '.$content.'
                </div>';
        return $html;
    }

    /**
     * 预格式化列表按钮集合标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preListBtns($content) {
        return $this->preTags($content, [
            '<btn', '</btn>',
            '<linkbtn', '</linkbtn>'
        ], [
            '<yz:btn', '</yz:btn>',
            '<yz:linkBtn', '</yz:linkBtn>'
        ]);
    }

    /**
     * 格式化列表搜索行标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function parseListBtns($attrs, $content = '') {
        $html .= '<div class="list-btns'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].'>
                      '.$content.'
                </div>';
        return $html;
    }

    /**
     * 预格式化列表表格标签
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preListTable($content) {
        return $this->preTags($content, [
            '<columns', '</columns>',
            '<rows', '</rows>'
        ], [
            '<yz:listColumns', '</yz:listColumns>',
            '<yz:listRows', '</yz:listRows>'
        ]);
    }

    /**
     * 格式化列表表格标签
     * @param  array  $attrs   属性
     * @param  string $content 内容
     * @return string          格式化后的内容
     */
    private function parseListTable($attrs, $content = '') {
        $id          = !isset($attrs['id']) ? 'checkListTable' : $attrs['id'];
        $relmodule   = !isset($attrs['relmodule']) ? '' : ' relmodule="'.$attrs['relmodule'].'"';
        $pager       = !isset($attrs['pager']) ? 'pager' : ($attrs['pager'] == 'no' ? '' : $attrs['pager']);

        $html .= '<div class="m-tab">
                      <table id="'.$id .'" class="'.$attrs['css'].'"'.$style.$attrs['attr'].$relmodule.'>
                        '.$content.'
                    </table>
                </div>';
        if ($pager !== '') {
            $html .= '@include(\'admin._layouts.'.$pager.'\')';
        }
        return $html;
    }

    /**
     * 格式化列表头部数据列
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseListHeader($attrs, $content = '') {
        $label      = $attrs['label'];

        $code       = $attrs['code'];
        $codes      = $this->formatFormItemName($code);
        $input_code = $codes['input_name'];

        $order      = !isset($attrs['order']) ? $input_code : $attrs['order'];
        $sort       = !isset($attrs['sort']) ? '' : ' sort="'.$attrs['sort'].'"';
        $width      = !isset($attrs['width']) ? '' : ' width="'.$attrs['width'].'"';
        if (!empty($sort)) {
            $attrs['css'] .= ' list-sort';
        }
        $html = '<td class="'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].$sort.$width.' order="'.$order.'" code="'.$input_code.'"><span>'.$label.'</span></td>';
        return $html;
    }

    /**
     * 格式化列表头部操作列
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseListHeaderActions($attrs, $content = '') {
        $width = !isset($attrs['width']) ? '' : ' width="'.$attrs['width'].'"';
        return '<td style="text-align:center;white-space:nowrap;"'.$width.'><span>操作</span></td>';
    }

    /**
     * 预格式化自定义行
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preListRows($content) {
        $list_attrs = $this->getTagAttrs('List');
        $datasource = !isset($list_attrs['datasource']) ? '$list' : $list_attrs['datasource'];
        $pk         = !isset($list_attrs['pk']) ? 'id' : $list_attrs['pk'];

        $table_attrs = $this->getTagAttrs('ListTable');

        preg_match("/<headers.*?>(?<headers>.*?)<\/headers>.*?<row.*?>(?<row>.+?)<\/row>/is", $content, $rows);
        preg_match("/<actions.*?>(.+?)<\/actions>/is", $rows['row'], $actions);

        $html       = '<thead>';
        if (isset($table_attrs['checkbox'])) {
            $html  .= '<td style="width:20px; text-align:center;">
                            <input type="checkbox" onclick="$.TableCheckHandler(this)">
                        </td>';
        }

        $html      .=  $this->preTags($rows['headers'], ['<header', '</header>'], ['<yz:listHeader', '</yz:listHeader>']);
        if ($actions) {
            $html  .=  $this->preTags($actions[0], ['<actions', '</actions>'], ['<yz:listHeaderActions', '</yz:listHeaderActions>']);
        }
        $html      .=  '</thead>';
        $html      .= '<tbody>
                        @foreach ('.$datasource.' as $list_index => $list_item)
                        <?php 
                            $list_item_css = $list_index % 2 == 0 ? " tr-even" : " tr-odd"; 
                            $list_item_pk  = $list_item["'.$pk.'"];
                        ?>';
        if ($actions) {
            $html  .=  $this->prePregTags($rows['row'], ['/<actions.*?>/', '/<\/actions>/', '/<action/', '/<\/action>/'], ['', '', '<yz:listAction', '</yz:listAction>']);
        } else {
            $html .=   $rows['row'];
        }
        $html .=        '
                    @endforeach
                </tbody>';
        return $html;
    }

    /**
     * 预格式化列表列
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preListColumns($content) {
        $list_attrs = $this->getTagAttrs('List');
        $datasource = !isset($list_attrs['datasource']) ? '$list' : $list_attrs['datasource'];
        $pk         = !isset($list_attrs['pk']) ? 'id' : $list_attrs['pk'];

        $table_attrs = $this->getTagAttrs('ListTable');

        $html       = '<thead>';
        if (isset($table_attrs['checkbox'])) {
            $html  .= '<td style="width:20px; text-align:center;">
                            <input type="checkbox" onclick="$.TableCheckHandler(this)">
                        </td>';
        }

        $html      .=  $this->preTags($content, [
                '<column', '</column>',
                '<actions', '</actions>'
            ], [
                '<yz:listHeader', '</yz:listHeader>',
                '<yz:listHeaderActions', '</yz:listHeaderActions>'
            ]).'
                    </thead>';

        $html      .= '<tbody>
                        @foreach ('.$datasource.' as $list_index => $list_item)
                        <?php 
                            $list_item_css = $list_index % 2 == 0 ? " tr-even" : " tr-odd"; 
                            $list_item_pk  = $list_item["'.$pk.'"];
                        ?>
                        <tr class="tr-{{ $list_item_pk }}{{$list_item_css}}" key="{{ $list_item_pk }}" primary="'.$pk.'">';
        if (isset($table_attrs['checkbox'])) {
            $html  .= '<td style="width:20px; text-align:center;">
                            <input type="checkbox" name="key" value="{{ $list_item_pk }}" @if ($list_item["checked_disabled"] == 1) disabled @endif />
                        </td>';
        }
        $html .=        $this->preTags($content, [
                '<column', '</column>',
                '<actions', '</actions>'
            ], [
                '<yz:listColumn', '</yz:listColumn>',
                '<yz:listActions', '</yz:listActions>'
            ]).'
                    </tr>
                    @endforeach
                </tbody>';
        return $html;
    }

    /**
     * 格式化列表的列标签
     * @param  array  $attrs   属性
     * @param  string $content 内容
     * @return string          格式化后的内容
     */
    private function parseListColumn($attrs, $content = '') {
        $align = !isset($attrs['align']) ? '' : 'text-align:'.$attrs['align'].';';
        $style = $attrs['style'];
        if (!empty($align)) {
            if(empty($style)){
                $style = ' style="'.$align.'"';
            } else {
                $style = str_replace('style="', 'style="'.$align, $style);
            }
        }

        $html = $content;
        $code       = $attrs['code'];
        $codes      = $this->formatFormItemName($code);
        $input_code = $codes['input_name'];
        $data_code  = $codes['data_name'];

        if (empty($html)) {
            if (!empty($attrs['type'])) {
                $html = '{!! YiZan\Utils\Format::'.$attrs['type'].'($list_item'.$data_code.', "'.$input_code.'", $list_item, '.var_export($attrs,true).') !!}';
            }else{
                $html = '{{ $list_item'.$data_code.' }}';
            }
        }
        $html = '<td class="'.$attrs['css'].'"'.$style.$attrs['attr'].' code="'.$input_code.'">'.$html.'</td>';
        return $html;
    }

    /**
     * 预格式化列表操作列
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    private function preListActions($content) {
        return $this->preTags($content, ['<action', '</action>'], ['<yz:listAction', '</yz:listAction>']);
    }

    /**
     * 格式化列表操作例集合
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseListActions($attrs, $content = '') {
        $align = !isset($attrs['align']) ? '' : 'text-align:'.$attrs['align'].';';
        $code = !isset($attrs['code']) ? '' : $attrs['code'].';';
        $style = $attrs['style'];
        if (!empty($align)) {
            if(empty($style)){
                $style = ' style="'.$align.'"';
            } else {
                $style = str_replace('style="', 'style="'.$align, $style);
            }
        }
        return '<td class="'.$attrs['css'].'"'.$style.$attrs['attr'].'>'.$content.'</td>';
    }

    /**
     * 格式化列表操作列
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseListAction($attrs, $content = '') {
        $label   = !isset($attrs['label']) ? '' : $attrs['label'];
        $type    = !isset($attrs['type']) ? '' : $attrs['type'];
        $url     = !isset($attrs['url']) ? '' : $attrs['url'];
        $click   = !isset($attrs['click']) ? '' : $attrs['click'];
        $tip     = !isset($attrs['tip']) ? '' : $attrs['tip'];
        $target  = !isset($attrs['target']) ? '_self' : $attrs['target'];
        $css     = !isset($attrs['css']) ? '' : $attrs['css'];
        $list_attrs = $this->getTagAttrs('List');
        $pk         = !isset($list_attrs['pk']) ? 'id' : $list_attrs['pk'];

        switch ($type) {
            case 'edit':
                $url    = empty($url) ? '{{ u(CONTROLLER_NAME."/edit",["id" => $list_item["'.$pk.'"]]) }}' : $url;
                $label  = empty($label) ? '编辑' : $label;
                break;
            case 'destroy':
                $url    = empty($url) ? '{{ u(CONTROLLER_NAME."/destroy",["id" => $list_item["'.$pk.'"]]) }}' : $url;
                $label  = empty($label) ? '删除' : $label;
                $tip    = empty($tip) ? '你确定要删除该数据吗？' : $tip;
                $click  = empty($click) ? '$.RemoveItem(this, \''.$url.'\', \''.$tip.'\')' : $click;
                break;
        }
        $url    = !empty($click) ? 'javascript:;' : $url;
        $click  = empty($click) ? '' : ' onclick="'.$click.'"';

        return '<a href="'.$url.'" class="'.$css.'" '.$click.$attrs['style'].$attrs['attr'].' data-pk="{{ $list_item["'.$pk.'"] }}" target="'.$target.'">'.$label.'</a>';
    }
    /**LIST格式化END**/

    private function parseLinkBtn($attrs, $content = '') {
        return $this->parseBtn($attrs, $content, false);
    }

    private function parseBtn($attrs, $content = '', $btnType = true) {
        $id      = !isset($attrs['id']) ? '' : ' id="'.$attrs['id'].'"';
        $type    = !isset($attrs['type']) ? '' : $attrs['type'];
        $label   = !isset($attrs['label']) ? '' : $attrs['label'];
        $url     = !isset($attrs['url']) ? '' : $attrs['url'];
        $click   = !isset($attrs['click']) ? '' : $attrs['click'];
        $tip     = !isset($attrs['tip']) ? '' : $attrs['tip'];
        $target  = !isset($attrs['target']) ? '_self' : $attrs['target'];
        $btnType = $btnType ? 'button' : $btnType;

        if (empty($content)) {
            switch($type){
                case 'submit':
                    $label    = empty($label) ? '提交' : $label;
                    $btnType  = 'submit';
                    break;
                case 'search':
                    $label    = empty($label) ? '搜索' : $label;
                    $btnType  = 'submit';
                    break;
                case 'add':
                    $label  = empty($label) ? '添加' : $label;
                    $url    = empty($url) ? '{{ u(CONTROLLER_NAME."/create") }}' : $url;
                    break;
                case 'refresh':
                    $label  = empty($label) ? '刷新' : $label;
                    $click  = empty($click) ? 'location.reload(true);' : $click;
                    break;
                case 'export':
                    $label  = empty($label) ? '导出' : $label;
                    $url    = empty($url) ? '{{ u(CONTROLLER_NAME."/export") }}' : $url;
                    break;
                case 'destroy':
                    $label  = empty($label) ? '删除' : $label;
                    $click  = empty($click) ? '$.RemoveList(this)' : $click;
                    break;
            }
        }
        if ($btnType !== false) {
            $click  = empty($click) ? '' : ' onclick="'.$click.'"';

            return '<button'.$id.' type="'.$btnType.'" class="btn mr5'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].$click.'>
                        '.$label.'
                    </button>';
        } else {
            $url = empty($click) ? $url : 'javascript:'.$click;
            return '<a'.$id.' href="'.$url.'" target="'.$target.'" class="btn mr5'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].$click.'>
                        '.$label.'
                    </a>';
        }
    }

    /**Tab格式化BEGIN**/
    private function preTabs($content) {
        return $this->preTags($content, [
            '<navs', '</navs>',
            '<panes', '</panes>',
        ], [
            '<yz:tabNavs', '</yz:tabNavs>',
            '<yz:tabPanes', '</yz:tabPanes>',
        ]);
    }

    private function parseTabs($attrs, $content = '') {
        $id 	= !isset($attrs['id']) ? '' : ' id="'.$attrs['id'].'"';
        $html 	= '<div'.$id.' class="tabs'.$attrs['css'].'"'.$attrs['style'].'>
					'.$content.'
				</div>';
        return $html;
    }

    private function preTabNavs($content) {
        return $this->preTags($content, ['<nav', '</nav>'], ['<yz:tabNav', '</yz:tabNav>']);
    }

    private function parseTabNavs($attrs, $content = '') {
        $id 	= !isset($attrs['id']) ? '' : ' id="'.$attrs['id'].'"';
        $html 	= '<div'.$id.' class="tab-navs u-spnav u-orderlstnav'.$attrs['css'].'"'.$attrs['style'].'>
					<ul class="clearfix"> 
						'.$content.'
					</ul>
				</div>';

        return $html;
    }

    private function parseTabNav($attrs, $content = '') {
        $id 	 = !isset($attrs['id']) ? '' : ' id="'.$attrs['id'].'"';
        $selectd = !isset($attrs['selectd']) ? '' : ' on';
        $url  	 = !isset($attrs['url']) ? 'javascript:;' : $attrs['url'];
        $target  = !isset($attrs['target']) ? '_self' : $attrs['target'];

        $html = '<li'.$id.' class="tab-nav'.$attrs['css'].$selectd.'">
    				<a href="'.$url.'" target="'.$target.'">'.$attrs['label'].'</a>
    			</li>';

        return $html;
    }

    private function preTabPanes($content) {
        return $this->preTags($content, ['<pane', '</pane>'], ['<yz:tabPane', '</yz:tabPane>']);
    }

    private function parseTabPanes($attrs, $content = '') {
        $id 	= !isset($attrs['id']) ? '' : ' id="'.$attrs['id'].'"';
        $html 	= '<div'.$id.' class="tab-panes'.$attrs['css'].'"'.$attrs['style'].'>
					'.$content.'
				</div>';
        return $html;
    }

    private function parseTabPane($attrs, $content = '') {
        $id 	 = !isset($attrs['id']) ? '' : ' id="'.$attrs['id'].'"';
        $selectd = !isset($attrs['selectd']) ? '' : ' on';

        $html = '<div'.$id.' class="tab-pane '.$attrs['css'].$selectd.'">
    				'.$content.'
    			</div>';

        return $html;
    }
    /**Tab格式化END**/

    private function preForm($content) {
        return $this->preTags($content, [
            '<item', '</item>',
            '<btn', '</btn>',
            '<linkbtn', '</linkbtn>',
        ], [
            '<yz:fitem', '</yz:fitem>',
            '<yz:btn', '</yz:btn>',
            '<yz:linkBtn', '</yz:linkBtn>'
        ]);
    }

    /**
     * 格式化表单
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseForm($attrs, $content = '') {
        $id 		 = !isset($attrs['id']) ? 'yzForm' : $attrs['id'];
        $datasource  = !isset($attrs['datasource']) ? 'data' : $attrs['datasource']; //列表显示的数据源
        $pk 		 = !isset($attrs['pk']) ? 'id' : $attrs['pk'];//主键名，默认为id
        $enctype	 = !isset($attrs['file']) ? 'application/x-www-form-urlencoded' : 'multipart/form-data';
        $style 		 = !isset($attrs['style']) ? '' : ' style="'.trim($attrs['style']).'"';
        $css 		 = !isset($attrs['css']) ?  '' : ' '.$attrs['css'];
        $ajaxform 	 = !isset($attrs['noajax']) ? 'ajax-form' : '';
        $isbtn 		 = !isset($attrs['nobtn']) ? true : false;
        $action		 = !isset($attrs['action']) ? 'store' : $attrs['action'];
        $method		 = !isset($attrs['method']) ? 'post' : $attrs['method'];
        $target		 = !isset($attrs['target']) ? '_self' : $attrs['target'];
        $titlewidth  = !isset($attrs['titlewidth']) ? '' : ' style="width:'.$attrs['titlewidth'].'"';
        $url         = !isset($attrs['url']) ? u(CONTROLLER_NAME.'/'.$action) : $attrs['url'];

        $html = '<div class="m-spboxlst'.$css.'"'.$style.'>
					<form id="'.$id.'" name="'.$id.'" class="validate-form '.$ajaxform.'" method="'.$method.'" action="'.$url.'" enctype="'.$enctype.'" target="'.$target.'">
						'.$content;
        if ($isbtn) {
            $html .= '		<div class="u-fitem clearfix">
                            <span class="f-tt"'.$titlewidth.'>
                                &nbsp;
                            </span>
                            <div class="f-boxr">
                                  <button type="submit" class="u-addspbtn">'.Lang::get('admin.form_sumit').'</button>
                            </div>
                        </div>';
        }
        $html .= '		<input type="hidden" value="{{ $'.$datasource.'[\''.$pk.'\'] }}" name="'.$pk.'" />
					</form>
				</div>';

        return $html;
    }

    private function formatFormItemName($name) {
        $names 		= explode('.', $name);
        $name 		= array_shift($names);
        $datasource	= '';
        //是否为变量
        $is_var     = strpos($name, '&$') === 0;
        if ($is_var) {//为变量时清除
            $name   = substr($name,1);
        }

        $result = [];
        if(strpos($name, '$') === 0){
            $name 		= substr($name,1);
            $input_name = $datasource = $name;
            $data_name 	= '';
        } else {
            $data_name 	= "['".$name."']";
            $input_name = $name;
        }

        foreach ($names as $name) {
            if (!empty($name)) {
                $data_name .= "['".$name."']";
            }
            $input_name .= "[".$name."]";
        }

        if ($is_var) {
            $data_name = '{{ $ }}';
        }
        return ['is_var'=> $is_var,'data_name' => $data_name, 'input_name' => $input_name, 'datasource' => $datasource];
    }

    /**
     * 格式化表单项
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseFitem($attrs, $content = '') {
        $datasource = !isset($attrs['datasource']) ? '' : $attrs['datasource'];
        $name 		= $attrs['name'];
        $type 		= !isset($attrs['type']) ? 'input' : $attrs['type'];
        $append     = isset($attrs['append']);
        if (empty($datasource)) {
            $datasource = !isset($attrs['search']) ? 'data' : 'search_args';
        }

        $names 		= $this->formatFormItemName($name);
        $input_name = $names['input_name'];
        $data_name 	= $names['data_name'];
        $datasource = empty($names['datasource']) ? $datasource : $names['datasource'];
        $id 		= !isset($attrs['id']) ? str_replace(['[', ']'], ['-', ''], $input_name) : $attrs['id'];

        //格式化内容
        if (isset($attrs['val'])) {
            $data = '<?php if(isset($'.$datasource.$data_name.')):?>{{$'.$datasource.$data_name.'}}<?php else: ?>'.$attrs['val'].'<?php endif; ?>';
        }else{
            $data = '{{$'.$datasource.$data_name.'}}';
        }
        if($type == 'hidden'){
            return '<input type="hidden" id="'.$id.'" name="'.$input_name.'" value="'.$data.'"/>';
        }

        $pid 		 = !isset($attrs['pid']) ? $id.'-form-item' : $attrs['pid'];
        $pstyle 	 = !isset($attrs['pstyle']) ? '' : ' style="'.trim($attrs['pstyle']).'"';
        $pcss 		 = !isset($attrs['pcss']) ?  '' : ' '.$attrs['pcss'];
        $required 	 = !isset($attrs['required']) ? false : true;
        $remove 	 = !isset($attrs['remove']) ? false : true;
        $label 		 = !isset($attrs['label']) ?  '' : ' '.$attrs['label'];
        $btip        = !isset($attrs['btip']) ?  '' : '<p>'.$attrs['btip'].'</p>';
        $tip 		 = !isset($attrs['tip']) ?  '' : '&nbsp;<span>'.$attrs['tip'].'</span>';
        if($required){
            $required_html = '&nbsp;<span style="color: #ff0000">(* 必填)</span>';
        }
        if (empty($content) || $append) {
            $html = '';
            switch ($type) {
                case 'textarea':
                    $html .= '<textarea name="'.$input_name.'" id="'.$id.'" class="u-ttarea'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].'>'.$data.'</textarea>'.$required_html;
                    break;
                case 'imagebtn':
                    $html .= '<a href="javascript:;" id="'.$id.'-btn" data-rel="'.$id.'"'.$attrs['style'].$attrs['attr'].' class="img-update-btn tjggbtn fl'.$attrs['css'].'">'.Lang::get('admin.upload_image').'</a>
	            		<input type="hidden" data-tip-rel="#'.$id.'-btn" name="'.$input_name.'" id="'.$id.'" value="'.$data.'"/>'.$required_html;
                    break;
                case 'image':
                    $html .= '<ul class="m-tpyllst clearfix">
	            				<li id="'.$id.'-box"'.$attrs['style'].$attrs['attr'].' class="'.$attrs['css'].'">
	            				@if ( !empty($'.$datasource.$data_name.') )
	            					<a id="img-preview-'.$id.'" class="m-tpyllst-img" href="'.$data.'" target="_blank"><img src="'.$data.'" alt=""></a>
	            				@else
                                    <a id="img-preview-'.$id.'" class="m-tpyllst-img" href="javascript:;" target="_self"><img src="" alt="" style="display:none;"></a>
	            				@endif
                                <a class="m-tpyllst-btn img-update-btn" href="javascript:;" data-rel="'.$id.'">
                                    <i class="fa fa-plus"></i> '.Lang::get('admin.upload_image').'
                                </a>
	            				<input type="hidden" data-tip-rel="#'.$id.'-box" name="'.$input_name.'" id="'.$id.'" value="'.$data.'"/>'.$required_html.'
	            				</li>
							</ul>';
                    break;
                case 'text':
                    $html .= '<span name="'.$input_name.'" id="'.$id.'" class="'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].'>'.$data.'</span>';
                    break;
                case 'password':
                    $html .= '<input type="text" name="'.$input_name.'" id="'.$id.'" class="u-ipttext'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].' disableautocomplete autocomplete="off" />'.$required_html;
                    break;
                case 'date':
                    $data = '{{ YiZan\Utils\Time::toDate($'.$datasource.$data_name.',\'Y-m-d\') }}';
                    $html .= '<input type="text" name="'.$input_name.'" id="'.$id.'" class="date u-ipttext'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].' value="'.$data.'" />'.$required_html;
                    break;
                case 'datetime':
                    $data = '{{ YiZan\Utils\Time::toDate($'.$datasource.$data_name.') }}';
                    $html .= '<input type="text" name="'.$input_name.'" id="'.$id.'" class="datetime u-ipttext'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].' value="'.$data.'" />'.$required_html;
                    break;
                case 'dateyear':
                    $data = '{{ YiZan\Utils\Time::toDate($'.$datasource.$data_name.',\'Y-m-d\') }}';
                    $html .= '<input type="text" name="'.$input_name.'" id="'.$id.'" class="dateyear u-ipttext'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].' value="'.$data.'" />'.$required_html;
                    break;
                default:
                    $html .= '<input type="text" name="'.$input_name.'" id="'.$id.'" class="u-ipttext'.$attrs['css'].'"'.$attrs['style'].$attrs['attr'].' value="'.$data.'" />'.$required_html;
                    break;
            }
            if ($append) {
                $html .= $content;
            }
        } else {
            $html = $content;
        }

        $list_attrs = $this->getTagAttrs('Form');
        $titlewidth = !isset($list_attrs['titlewidth']) ? '' : ' style="width:'.$list_attrs['titlewidth'].'"';
        
        return '<div id="'.$pid.'" class="u-fitem clearfix '.$pcss.'"'.$pstyle.'>
		            <span class="f-tt"'.$titlewidth.'>
		                '.$label.':
		            </span>
		            <div class="f-boxr">
		                  '.$html.$tip.$btip.'
		            </div>
		        </div>';
    }

    private function parseImageList($attrs, $content = '') {
        static $imageListIndex = 0;
        $imageListIndex++;

        $images = !isset($attrs['images']) ? '[]' : $attrs['images'];
        $id     = !isset($attrs['id']) ? 'image-list-'.$imageListIndex : $attrs['id'];
        $name   = $attrs['name'];
        $name   = $this->formatFormItemName($name);
        $name   = $name['input_name'];
        $required    = !isset($attrs['required']) ? false : true;
        if($required){
            $required_html = '&nbsp;<span style="color: #ff0000">(* 必传)</span>';
        }
        $html .= '<?php $images = '.$images.'; ?>
                <ul id="'.$id.'" class="m-tpyllst image-list clearfix" data-input-name="'.$name.'">
                    @foreach($images as $image)
                    <li class="image-box">
                        <a class="m-tpyllst-img image-item" href="{{ $image }}" target="_blank"><img src="{{ $image }}" alt=""></a>
                        <a class="m-tpyllst-btn image-update-btn" href="javascript:;">
                            <i class="fa fa-plus"></i> '.Lang::get('admin.upload_image').'
                        </a>
                        <a href="javascript:;" class="image-delete fa fa-times"></a>
                        <input type="hidden" name="'.$name.'" value="{{ $image }}"/>
                    </li>
                    @endforeach
                    <li class="image-add-box">
                        <a class="m-tpyllst-btn image-add-btn" href="javascript:;">
                            <i class="fa fa-plus"></i> '.Lang::get('admin.upload_image').'
                        </a>
                    </li>
                    '.$required_html.'
                </ul>';
        return $html;
    }

    public function parseRegion($attrs, $content = '') {
        $html = '';
        static $is_init = false;
        if(!$is_init){
            $html .= '<script type="text/javascript" src="'.asset('js/city.js').'"></script>';
            $is_init = true;
        }
        $pname = !isset($attrs['pname']) ? 'provinceId' : $attrs['pname'];
        $cname = !isset($attrs['cname']) ? 'cityId' : $attrs['cname'];
        $aname = !isset($attrs['aname']) ? 'areaId' : $attrs['aname'];
        $isReg = !isset($attrs['isReg']) ? 0 : 1;

        $pval  = !isset($attrs['pval']) ? '' : $attrs['pval'];
        $cval  = !isset($attrs['cval']) ? '' : $attrs['cval'];
        $aval  = !isset($attrs['aval']) ? '' : $attrs['aval'];
        $istip = !isset($attrs['showtip']) ? 0 : 1;

        $pnames = $this->formatFormItemName($pname);
        $pname  = $pnames['input_name'];
        $pid    = str_replace(['[', ']'], ['-', ''], $pname);

        $cnames = $this->formatFormItemName($cname);
        $cname  = $cnames['input_name'];
        $cid    = str_replace(['[', ']'], ['-', ''], $cname);

        $anames = $this->formatFormItemName($aname);
        $aname  = $anames['input_name'];
        $aid    = str_replace(['[', ']'], ['-', ''], $aname);

        if(empty($pname)){
            return '';
        }

        if(empty($cname)){
            $html .= '<select id="'.$pid.'" name="'.$pname.'" data-val="'.$this->formatValue($pval).'" data-showtip="'.$istip.'" class="sle province_city" style="width:auto; display:inline-block;"></select>';
        }else{
            $html .= '<select id="'.$pid.'" name="'.$pname.'" data-val="'.$this->formatValue($pval).'" data-showtip="'.$istip.'" data-city="'.$cid.'" class="sle province_city" style="width:auto; display:inline-block;"></select>';
            if(empty($aname)){
                $html .= '<select id="'.$cname.'" name="'.$cname.'" data-val="'.$this->formatValue($cval).'" class="sle" style="width:auto; display:inline-block;"></select>';
            }else{
                $html .= '<select id="'.$cname.'" name="'.$cname.'" data-val="'.$this->formatValue($cval).'" data-area="'.$aid.'" class="sle" style="width:auto; display:inline-block;"></select>';
                $html .= '<select id="'.$aname.'" name="'.$aname.'" data-val="'.$this->formatValue($aval).'" class="sle" style="width:auto; display:inline-block;"></select>';
            }
        }
        if($isReg){
            $html .= '<script type="text/javascript">var ZY_CITYS;jQuery(function($){$.get("{{u("Public/getOpenCitys")}}",{},function(result){ZY_CITYS = result;$.RegionBind("'.$pid.'");},"json");});</script>';
        } else {
            $html .= '<script type="text/javascript">jQuery(function($){$.RegionBind("'.$pid.'");});</script>';
        }
        return $html;
    }

    /**
     * 格式化Select标签
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseSelect($attrs, $content = '') {
        $name  		 = $attrs['name'];
        $options	 = !isset($attrs['options']) ? '' : $attrs['options'];
        $texts 		 = !isset($attrs['texts']) ? '' : $attrs['texts'];
        $css 		 = !isset($attrs['css']) ?  '' : ' '.$attrs['css'];
        $multiple 	 = !isset($attrs['multiple']) ? '' : ' multiple="multiple"';
        $size 		 = !isset($attrs['size']) ? '' : ' size="'.$attrs['size'].'"';
        $textfield	 = !isset($attrs['textfield']) ? '' : $attrs['textfield'];
        $valuefield	 = !isset($attrs['valuefield']) ? '' : $attrs['valuefield'];
        $first		 = !isset($attrs['first']) ? '' : $attrs['first'];
        $firstvalue	 = !isset($attrs['firstvalue']) ? '' : $attrs['firstvalue'];
        $selected	 = !isset($attrs['selected']) ? '' : $attrs['selected'];
        if (!empty($multiple)) {
            $css .= ' multiple-select';
        }

        $names 		= $this->formatFormItemName($name);
        $input_name = $names['input_name'];
        $data_name 	= $names['data_name'];
        $id 		= !isset($attrs['id']) ? str_replace(['[', ']'], ['-', ''], $input_name) : $attrs['id'];

        if (false === strpos($options, '$')) {
            $options = explode(',', $options);
        }

        if (!empty($texts) && false === strpos($texts, '$')) {
            $texts = explode(',', $texts);
        }

        if (false === strpos($selected, '$')) {
            $selected = explode(',', $selected);
        }

        $html = '<select id="'.$id.'" name="'.$input_name.'"'.$multiple.$size.$attrs['attr'].' class="sle '.$css.'" '.$style.'>';
        if ($first !== '') {
            $html .= '<option value="'.$firstvalue.'" >'.$first.'</option>';
        }

        if (is_array($options)) {//如果是数组,则附值到变量
            $html .= '<?php $select_options = '.var_export($options, true).'; ?>';
        } else {
            $html .= '<?php $select_options = '.$options.'; ?>';
        }

        if (is_array($selected)) {//如果是数组,则附值到变量
            $html .= '<?php $selecteds = '.var_export($selected, true).'; ?>';
        } else {
            $html .= '<?php $selecteds = is_array('.$selected.') ? '.$selected.' : ['.$selected.']; ?>';
        }

        if (!empty($texts)) {
            if (is_array($texts)) {//如果是数组,则附值到变量
                $html .= '<?php $select_texts = '.var_export($texts, true).'; ?>';
            } else {
                $html .= '<?php $select_texts = '.$texts.'; ?>';
            }

            $html .= '<?php  foreach($select_options as $options_key => $options_val):
                    $selected = in_array($options_val, $selecteds) ? " selected" : ""; ?>
					<option<?php echo $selected; ?> value="<?php echo $options_val; ?>"><?php echo $select_texts[$options_key]; ?></option>
					<?php endforeach; ?>';
        } else {
            if (!empty($valuefield) && !empty($textfield)) {
                $html .= '<?php  foreach($select_options as $options_key => $options_val): ?>';
                if (!empty($valuefield)) {
                    $html .= '<?php $options_key = $options_val[\''.$valuefield.'\']; ?>';
                }
                if (!empty($textfield)) {
                    $html .= '<?php $options_val = $options_val[\''.$textfield.'\']; ?>';
                }

                $html .= '<?php $selected = in_array($options_key, $selecteds) ? " selected" : ""; ?>
	                	<option<?php echo $selected; ?> value="<?php echo $options_key; ?>"><?php echo $options_val; ?></option>
						<?php endforeach; ?>';
            } else {
                $html .= '<?php  for($options_index = 0; $options_index < count($select_options); $options_index++):
					$selected = in_array($options_index, $selecteds) ? " selected" : ""; ?>
					<option<?php echo $selected; ?> value="<?php echo $options_index; ?>"><?php echo $select_options[$options_index]; ?></option>
					<?php endfor; ?>';
            }
        }
        return $html.'</select>';
    }

    /**
     * 格式化CheckBox标签
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseCheckbox($attrs, $content = '') {
        $name        = $attrs['name'];
        $options     = !isset($attrs['options']) ? '' : $attrs['options'];
        $texts       = !isset($attrs['texts']) ? '' : $attrs['texts'];
        $css         = !isset($attrs['css']) ?  '' : ' '.$attrs['css'];
        $textfield   = !isset($attrs['textfield']) ? '' : $attrs['textfield'];
        $valuefield  = !isset($attrs['valuefield']) ? '' : $attrs['valuefield'];
        $checked     = !isset($attrs['checked']) ? '' : $attrs['checked'];

        $names      = $this->formatFormItemName($name);
        $input_name = $names['input_name'];

        if (false === strpos($options, '$')) {
            $options = explode(',', $options);
        }

        if (!empty($texts) && false === strpos($texts, '$')) {
            $texts = explode(',', $texts);
        }

        if (false === strpos($checked, '$')) {
            $checked = explode(',', $checked);
        }

        $html = '';

        if (is_array($options)) {//如果是数组,则附值到变量
            $html .= '<?php $checkbox_options = '.var_export($options, true).'; ?>';
        } else {
            $html .= '<?php $checkbox_options = '.$options.'; ?>';
        }

        if (is_array($checked)) {//如果是数组,则附值到变量
            $html .= '<?php $checkeds = '.var_export($checked, true).'; ?>';
        } else {
            $html .= '<?php $checkeds = is_array('.$checked.') ? '.$checked.' : ['.$checked.']; ?>';
        }

        if (!empty($texts)) {
            if (is_array($texts)) {//如果是数组,则附值到变量
                $html .= '<?php $checkbox_texts = '.var_export($texts, true).'; ?>';
            } else {
                $html .= '<?php $checkbox_texts = '.$texts.'; ?>';
            }

            $html .= '<?php  foreach($checkbox_options as $options_key => $options_val):
                    $checked = in_array($options_val, $checkeds) ? " checked" : ""; ?>
                    <label>
                        <input type="checkbox" class="uniform'.$css.'" name="'.$input_name.'" value="<?php echo $options_val; ?>" <?php echo $checked; ?>/>
                        <span><?php echo $checkbox_texts[$options_key]; ?></span>
                    </label>
                    <span>&nbsp;&nbsp;</span>
                    <?php endforeach; ?>';
        } else {
            if (!empty($valuefield) && !empty($textfield)) {
                $html .= '<?php  foreach($checkbox_options as $options_key => $options_val): ?>';
                if (!empty($valuefield)) {
                    $html .= '<?php $options_key = $options_val[\''.$valuefield.'\']; ?>';
                }
                if (!empty($textfield)) {
                    $html .= '<?php $options_val = $options_val[\''.$textfield.'\']; ?>';
                }

                $html .= '<?php $checked = in_array($options_key, $checkeds) ? " checked" : ""; ?>
                        <label>
                            <input type="checkbox" class="uniform'.$css.'" name="'.$input_name.'" value="<?php echo $options_key; ?>" <?php echo $checked; ?>/>
                            <span><?php echo $options_val; ?></span>
                        </label>
                        <span>&nbsp;&nbsp;</span>
                        <?php endforeach; ?>';
            } else {
                $html .= '<?php  for($options_index = 0; $options_index < count($checkbox_options); $options_index++): 
                    $checked = in_array($options_index, $checkeds) ? " checked" : ""; ?>
                    <label>
                        <input type="checkbox" class="uniform'.$css.'" name="'.$input_name.'" value="<?php echo $options_index; ?>" <?php echo $checked; ?>/>
                        <span><?php echo $checkbox_options[$options_index]; ?></span>
                    </label>
                    <span>&nbsp;&nbsp;</span>
                    <?php endfor; ?>';
            }
        }
        return $html;
    }
    /**
     * 格式化Radio标签
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseRadio($attrs, $content = '') {
        $name        = $attrs['name'];
        $options     = !isset($attrs['options']) ? '' : $attrs['options'];
        $texts       = !isset($attrs['texts']) ? '' : $attrs['texts'];
        $css         = !isset($attrs['css']) ?  '' : ' '.$attrs['css'];
        $textfield   = !isset($attrs['textfield']) ? '' : $attrs['textfield'];
        $valuefield  = !isset($attrs['valuefield']) ? '' : $attrs['valuefield'];
        $checked     = !isset($attrs['checked']) ? '' : $attrs['checked'];
        $default     = !isset($attrs['default']) ? '' : $attrs['default'];
        $names      = $this->formatFormItemName($name);
        $input_name = $names['input_name'];
        if (false === strpos($options, '$')) {
            $options = explode(',', $options);
        }

        if (!empty($texts) && false === strpos($texts, '$')) {
            $texts = explode(',', $texts);
        }

        $html = '';

        if (is_array($options)) {//如果是数组,则附值到变量
            $html .= '<?php $radio_options = '.var_export($options, true).'; ?>';
        } else {
            $html .= '<?php $radio_options = '.$options.'; ?>';
        }

        if (false === strpos($default, '$')) {//如果不是变量
            $html .= '<?php $default = \''.$default.'\'; ?>';
        } else {
            $html .= '<?php $default = '.$default.'; ?>';
        }

        if (false === strpos($checked, '$')) {//如果不是变量
            $html .= '<?php $checked = \''.$checked.'\'; ?>';
        } else {
            $html .= '<?php $checked = isset('.$checked.') ? '.$checked.' : $default; ?>';
        }

        if (!empty($texts)) {
            if (is_array($texts)) {//如果是数组,则附值到变量
                $html .= '<?php $radio_texts = '.var_export($texts, true).'; ?>';
            } else {
                $html .= '<?php $radio_texts = '.$texts.'; ?>';
            }

            $html .= '<?php  foreach($radio_options as $options_key => $options_val): 
                    $checked_attr = $options_val == $checked ? " checked" : ""; ?>
                    <label>
                        <input type="radio" class="uniform'.$css.'" name="'.$input_name.'" value="<?php echo $options_val; ?>" <?php echo $checked_attr; ?>/>
                        <span><?php echo $radio_texts[$options_key]; ?></span>
                    </label>
                    <span>&nbsp;&nbsp;</span>
                    <?php endforeach; ?>';
        } else {
            if (!empty($valuefield) && !empty($textfield)) {
                $html .= '<?php  foreach($radio_options as $options_key => $options_val): ?>';
                if (!empty($valuefield)) {
                    $html .= '<?php $options_key = $options_val[\''.$valuefield.'\']; ?>';
                }
                if (!empty($textfield)) {
                    $html .= '<?php $options_val = $options_val[\''.$textfield.'\']; ?>';
                }

                $html .= '<?php $checked_attr = $options_key == $checked ? " checked" : ""; ?>
                        <label>
                            <input type="radio" class="uniform'.$css.'" name="'.$input_name.'" value="<?php echo $options_key; ?>" <?php echo $checked_attr; ?>/>
                            <span><?php echo $options_val; ?></span>
                        </label>
                        <span>&nbsp;&nbsp;</span>
                        <?php endforeach; ?>';
            } else {
                $html .= '<?php  for($options_index = 0; $options_index < count($radio_options); $options_index++): 
                    $checked_attr = $options_index == $checked ? " checked" : ""; ?>
                    <label>
                        <input type="radio" class="uniform'.$css.'" name="'.$input_name.'" value="<?php echo $options_index; ?>" <?php echo $checked_attr; ?>/>
                        <span><?php echo $radio_options[$options_index]; ?></span>
                    </label>
                    <span>&nbsp;&nbsp;</span>
                    <?php endfor; ?>';
            }
        }
        return $html;
    }

    /**
     * 格式化Editor标签
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseEditor($attrs, $content = '') {
        $id     = !empty($attrs['id']) ? $attrs['id'].'_editor' : $attrs['name'].'_editor';
        $name   = $attrs['name'];
        $width  = !empty($attrs['width'])?$attrs['width']: '540px';
        $height = !empty($attrs['height'])?$attrs['height'] :'280px';
        $value  = $attrs['value'];
        $type   = !empty($attrs['type']) ? $attrs['type'] :'"default"';

        $default = "width:'{$width}',minWidth:'{$width}',height:'{$height}'";
        $required    = !isset($attrs['required']) ? false : true;
        if($required){
            $required_html = '&nbsp;<span style="color: #ff0000">(* 必填)</span>';
        }
        $content = trim($content);
        if(empty($content)){
            $content = 'resizeType:1';
            if($type == 'simple'){
                $content .= "allowPreviewEmoticons:false,allowImageUpload:false,allowFileManager:false,
                items:['fontname','fontsize','|','forecolor','hilitecolor','bold','italic','underline',
                    'removeformat','|','justifyleft','justifycenter','justifyright','insertorderedlist','insertunorderedlist','|', 'link']";
            }elseif($type == 'wx'){
                $content .= ",allowPreviewEmoticons:false,allowImageUpload:false,allowFileManager:false,
                items:['wxemoticons','wxlink','unlink']";
            }else{
                $content .= ",allowFileManager:false,items: [
'source', 'undo', 'redo', 'plainpaste', 'plainpaste', 'wordpaste', 'clearhtml', 'quickformat', 'selectall', 'fullscreen', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'hr',
'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'table', 'insertorderedlist',
'insertunorderedlist', '|', 'emoticons', 'image', 'link', 'unlink']";
            }
        }
        $html = '<div id="'.$name.'-tip">'.$required_html.'<textarea data-tip-rel="#'.$name.'-tip" id="'.$id.'" class="'.$css.' " name="'.$name.'">'.$value.'</textarea></div>
            <script>
            var '.$id.' = KindEditor.create("#'.$id.'",{
                '.$default.','.$content.'
            });
            </script>';
        return $html;
    }

    private function formatValue($value) {
        if (strpos($value, '$') !== false) {
            return '{{ '.$value.' }}';
        } else {
            return $value;
        }
    }

    /**
     * 格式化Map标签 地图定位
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseMap($attrs, $content = '') {
        self::$mapIndex++;

        $html         = '';
        $label        = !isset($attrs['label']) ? '地址' : $attrs['label'];
        $point_name   = !isset($attrs['pointName']) ? 'mapPoint' : $attrs['pointName'];
        $point_val    = !isset($attrs['pointVal']) ? '' : $attrs['pointVal'];
        $address_name = !isset($attrs['addressName']) ? 'address' : $attrs['addressName'];
        $address_val  = !isset($attrs['addressVal']) ? '' : $attrs['addressVal'];

        $point_name   = $this->formatFormItemName($point_name);
        $point_name   = $point_name['input_name'];
        $address_name = $this->formatFormItemName($address_name);
        $address_name = $address_name['input_name'];

        if (self::$mapIndex == 1) {
            $key   = Config::get('app.qq_map.key');
            $html .= '<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key='.$key.'&libraries=geometry"></script>';
        }

        $html .= '<input type="text" name="'.$address_name.'" id="map-address-'.self::$mapIndex.'" class="u-ipttext" value="'.$this->formatValue($address_val).'">
            <button type="button" id="map-search-'.self::$mapIndex.'" class="btn"><i class="fa fa-map-marker"></i> 定位</button>
            <div class="blank5"></div>
            <div id="map-container-'.self::$mapIndex.'" style="width:540px; height:300px; border:1px solid #ccc;"></div>
            <input type="hidden" name="'.$point_name.'" id="map-point-'.self::$mapIndex.'" value="'.$this->formatValue($point_val).'"/>
            <script type="text/javascript">
            var qqGeocoder'.self::$mapIndex.',qqMap'.self::$mapIndex.',qqMarker'.self::$mapIndex.' = null;
            var defaultMapPoint'.self::$mapIndex.' = "'.$this->formatValue($attrs['pointVal']).'";
            jQuery(function($){
                $(window).load(function(){
                    var mapCenter'.self::$mapIndex.';
                    if(defaultMapPoint'.self::$mapIndex.' == ""){
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng(39.916527,116.397128);
                    } else {
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng('.$this->formatValue($attrs['pointVal']).');
                    }
                    qqMap'.self::$mapIndex.' = new qq.maps.Map(document.getElementById("map-container-'.self::$mapIndex.'"),{
                        center: mapCenter'.self::$mapIndex.',
                        zoom: 13
                    });
                    qqMarker'.self::$mapIndex.' = new qq.maps.Marker({
                        map:qqMap'.self::$mapIndex.',
                        draggable:true,
                        position: mapCenter'.self::$mapIndex.'
                    });

                    qq.maps.event.addListener(qqMarker'.self::$mapIndex.', "dragend", function(event) {
                        $("#map-point-'.self::$mapIndex.'").val(event.latLng.getLat() + "," + event.latLng.getLng());
                    });
                    qq.maps.event.addListener(qqMap'.self::$mapIndex.', "click", function(event) {
                        qqMarker'.self::$mapIndex.'.setPosition(event.latLng);
                        $("#map-point-'.self::$mapIndex.'").val(event.latLng.getLat() + "," + event.latLng.getLng());
                    });

                    if(defaultMapPoint'.self::$mapIndex.' == ""){
                        var cityLocation'.self::$mapIndex.' = new qq.maps.CityService({
                            complete : function(result){
                                qqMap'.self::$mapIndex.'.setCenter(result.detail.latLng);
                                qqMarker'.self::$mapIndex.'.setPosition(result.detail.latLng);
                                $("#map-point-'.self::$mapIndex.'").val(result.detail.latLng.getLat() + "," + result.detail.latLng.getLng());
                            }
                        });
                        cityLocation'.self::$mapIndex.'.searchLocalCity();
                    }
                    qqGeocoder'.self::$mapIndex.' = new qq.maps.Geocoder({
                        complete : function(result){
                            qqMap'.self::$mapIndex.'.setCenter(result.detail.location);
                            qqMarker'.self::$mapIndex.'.setPosition(result.detail.location);
                            $("#map-point-'.self::$mapIndex.'").val(result.detail.location.getLat() + "," + result.detail.location.getLng());
                        }
                    });

                    $("#map-search-'.self::$mapIndex.'").click(function(){
                        if($.trim($("#map-address-'.self::$mapIndex.'").val()) != ""){
                            qqGeocoder'.self::$mapIndex.'.getLocation($("#map-address-'.self::$mapIndex.'").val());
                        }
                    }); 
                })
            })
            </script>';
        return $html;
    }

    /**
     * 格式化Map标签 地图定位
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseMapArea($attrs, $content = '') {
        self::$mapIndex++;

        $html         = '';
        $label        = !isset($attrs['label']) ? '地址' : $attrs['label'];
        $point_name   = !isset($attrs['pointName']) ? 'mapPoint' : $attrs['pointName'];
        $point_val    = !isset($attrs['pointVal']) ? '' : $attrs['pointVal'];
        $address_name = !isset($attrs['addressName']) ? 'address' : $attrs['addressName'];
        $address_val  = !isset($attrs['addressVal']) ? '' : $attrs['addressVal'];
        $pos_name     = !isset($attrs['posName']) ? 'mapPos' : $attrs['posName'];
        $pos_val      = !isset($attrs['posVal']) ? '' : $attrs['posVal'];
        $width      = !isset($attrs['width']) ? '540px' : $attrs['width'];
        $css      = !isset($attrs['css']) ? '' : $attrs['css'];
        $placeholder      = !isset($attrs['placeholder']) ? '' : $attrs['placeholder'];
        $point_name   = $this->formatFormItemName($point_name);
        $point_name   = $point_name['input_name'];
        $address_name = $this->formatFormItemName($address_name);
        $address_name = $address_name['input_name'];
        $pos_name     = $this->formatFormItemName($pos_name);
        $pos_name     = $pos_name['input_name'];

        if (self::$mapIndex == 1) {
            $key   = Config::get('app.qq_map.key');
            $html .= '<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key='.$key.'&libraries=geometry"></script>';
        }

        $html .= '<input type="text" name="'.$address_name.'" id="map-address-'.self::$mapIndex.'" placeholder="'.$this->formatValue($placeholder).'" class="u-ipttext" value="'.$this->formatValue($address_val).'">
            <button type="button" id="map-search-'.self::$mapIndex.'" class="btn '.$css.'"><i class="fa fa-map-marker"></i> 定位</button>
            <button type="button" id="map-refresh-pos-'.self::$mapIndex.'" class="btn '.$css.'"><i class="fa fa-refresh"></i> 重置范围</button>
            <div class="blank5"></div>
            <div id="map-container-'.self::$mapIndex.'" style="width:'.$width.'; height:300px; border:1px solid #ccc;"></div>
            <input type="hidden" name="'.$point_name.'" id="map-point-'.self::$mapIndex.'" value="'.$this->formatValue($point_val).'"/>
            <input type="hidden" name="'.$pos_name.'" id="map-pos-'.self::$mapIndex.'" value="'.$this->formatValue($pos_val).'"/>
            <script type="text/javascript">
            var qqGeocoder'.self::$mapIndex.',qqMap'.self::$mapIndex.',qqMarker'.self::$mapIndex.',qqPolygon'.self::$mapIndex.' = null,qqLatLngs'.self::$mapIndex.' = null;
            var defaultMapPoint'.self::$mapIndex.' = "'.$this->formatValue($attrs['pointVal']).'";
            jQuery(function($){
                $(window).load(function(){
                    var mapCenter'.self::$mapIndex.';
                    if(defaultMapPoint'.self::$mapIndex.' == ""){
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng(39.916527,116.397128);
                    } else {
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng('.$this->formatValue($attrs['pointVal']).');
                    }           
                    qqMap'.self::$mapIndex.' = new qq.maps.Map(document.getElementById("map-container-'.self::$mapIndex.'"),{
                        center: mapCenter'.self::$mapIndex.',
                        zoom: 13
                    });
                    qqMarker'.self::$mapIndex.' = new qq.maps.Marker({
                        map:qqMap'.self::$mapIndex.',
                        draggable:true,
                        position: mapCenter'.self::$mapIndex.'
                    });

                    $("#map-refresh-pos-'.self::$mapIndex.'").click(function(){
                        var latLng = mapCenter'.self::$mapIndex.';
                        var tmpLng = qq.maps.geometry.spherical.computeOffset(latLng, 500, 0);
                        //西北角
                        var wnLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, -90);
                        //东北角
                        var enLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, 90);
                        //东南角
                        var esLatLng = qq.maps.geometry.spherical.computeOffset(enLatLng, 1000, 180);
                        //西南角
                        var nwLatLng = qq.maps.geometry.spherical.computeOffset(wnLatLng, 1000, 180);
                        qqLatLngs'.self::$mapIndex.' = [wnLatLng,enLatLng,esLatLng,nwLatLng];
                        qqPolygon'.self::$mapIndex.'.setPath(qqLatLngs'.self::$mapIndex.');
                    });

                    $.createPolygon'.self::$mapIndex.' = function(latLng){
                        if(!qqPolygon'.self::$mapIndex.'){
                            var tmpLng = qq.maps.geometry.spherical.computeOffset(latLng, 500, 0);
                            //西北角
                            var wnLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, -90);
                            //东北角
                            var enLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, 90);
                            //东南角
                            var esLatLng = qq.maps.geometry.spherical.computeOffset(enLatLng, 1000, 180);
                            //西南角
                            var nwLatLng = qq.maps.geometry.spherical.computeOffset(wnLatLng, 1000, 180);
                            qqLatLngs'.self::$mapIndex.' = [wnLatLng,enLatLng,esLatLng,nwLatLng];

                            qqPolygon'.self::$mapIndex.' = new qq.maps.Polygon({
                                map:qqMap'.self::$mapIndex.',
                                editable:true,
                                visible:true,
                                path:qqLatLngs'.self::$mapIndex.'
                            });
                        } else {
                            var heading = qq.maps.geometry.spherical.computeHeading(mapCenter'.self::$mapIndex.', latLng);
                            var distance = qq.maps.geometry.spherical.computeDistanceBetween(mapCenter'.self::$mapIndex.', latLng);
                            qqLatLngs'.self::$mapIndex.' = new Array();
                            qqPolygon'.self::$mapIndex.'.getPath().forEach(function(element, index){
                                qqLatLngs'.self::$mapIndex.'.push(qq.maps.geometry.spherical.computeOffset(element, distance, heading));
                            });
                            qqPolygon'.self::$mapIndex.'.setPath(qqLatLngs'.self::$mapIndex.');
                        }
                        mapCenter'.self::$mapIndex.' = latLng;
                    }

                    qq.maps.event.addListener(qqMarker'.self::$mapIndex.', "dragend", function(event) {
                        $("#map-point-'.self::$mapIndex.'").val(event.latLng.getLat() + "," + event.latLng.getLng());
                        $.createPolygon'.self::$mapIndex.'(event.latLng);
                    });
                    qq.maps.event.addListener(qqMap'.self::$mapIndex.', "click", function(event) {
                        qqMarker'.self::$mapIndex.'.setPosition(event.latLng);
                        $.createPolygon'.self::$mapIndex.'(event.latLng);
                        $("#map-point-'.self::$mapIndex.'").val(event.latLng.getLat() + "," + event.latLng.getLng());
                    });

                    if(defaultMapPoint'.self::$mapIndex.' == ""){
                        var cityLocation'.self::$mapIndex.' = new qq.maps.CityService({
                            complete : function(result){
                                qqMap'.self::$mapIndex.'.setCenter(result.detail.latLng);
                                qqMarker'.self::$mapIndex.'.setPosition(result.detail.latLng);
                                $.createPolygon'.self::$mapIndex.'(result.detail.latLng);
                                $("#map-point-'.self::$mapIndex.'").val(result.detail.latLng.getLat() + "," + result.detail.latLng.getLng());
                            }
                        });
                        cityLocation'.self::$mapIndex.'.searchLocalCity();
                    } else {
                        var mapPos'.self::$mapIndex.' = "'.$this->formatValue($attrs['posVal']).'".split("|");
                        var mpLatLng'.self::$mapIndex.';
                        qqLatLngs'.self::$mapIndex.' = new Array();
                        for(var mpIndex = 0; mpIndex < mapPos'.self::$mapIndex.'.length; mpIndex++){
                            mpLatLng'.self::$mapIndex.' = mapPos'.self::$mapIndex.'[mpIndex].split(",");
                            qqLatLngs'.self::$mapIndex.'.push(new qq.maps.LatLng(mpLatLng'.self::$mapIndex.'[0],mpLatLng'.self::$mapIndex.'[1]));
                        }
                        qqPolygon'.self::$mapIndex.' = new qq.maps.Polygon({
                            map:qqMap'.self::$mapIndex.',
                            editable:true,
                            visible:true,
                            path:qqLatLngs'.self::$mapIndex.'
                        });
                    }
                    
                    qqGeocoder'.self::$mapIndex.' = new qq.maps.Geocoder({
                        complete : function(result){
                            qqMap'.self::$mapIndex.'.setCenter(result.detail.location);
                            qqMarker'.self::$mapIndex.'.setPosition(result.detail.location);
                            $.createPolygon'.self::$mapIndex.'(result.detail.location);
                            $("#map-point-'.self::$mapIndex.'").val(result.detail.location.getLat() + "," + result.detail.location.getLng());
                        }
                    });

                    $("#map-search-'.self::$mapIndex.'").click(function(){
                        if($.trim($("#map-address-'.self::$mapIndex.'").val()) != ""){
                            qqGeocoder'.self::$mapIndex.'.getLocation($("#map-address-'.self::$mapIndex.'").val());
                        }
                    });

                    YZ.AJAX_FROM_SYNC.push(function(){
                        var maplatLngs = new Array();
                        qqPolygon'.self::$mapIndex.'.getPath().forEach(function(element, index){
                            maplatLngs.push(element.getLat() + "," + element.getLng());
                        });
                        $("#map-pos-'.self::$mapIndex.'").val(maplatLngs.join("|"));
                    });
                })
            })
            </script>';
        return $html;
    }

    /**
     * 格式化Map标签 地图定位显示
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseMapPoint($attrs, $content = '') {
        self::$mapIndex++;
        $html         = '';
        $point_val    = !isset($attrs['pointVal']) ? '' : $attrs['pointVal'];
        $width        = !isset($attrs['width']) ? '100%' : $attrs['width'];
        $height       = !isset($attrs['height']) ? '100%' : $attrs['height'];

        if (self::$mapIndex == 1) {
            $key   = Config::get('app.qq_map.key');
            $html .= '<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key='.$key.'&libraries=geometry"></script>';
        }

        $html .= '<div id="map-container-'.self::$mapIndex.'" style="width:'.$width.'; height:'.$height.';"></div>
            <script type="text/javascript">
            var qqMap'.self::$mapIndex.',qqMarker'.self::$mapIndex.' = null;
            var defaultMapPoint'.self::$mapIndex.' = "'.$this->formatValue($attrs['pointVal']).'";
            jQuery(function($){
                $(window).load(function(){
                    var mapCenter'.self::$mapIndex.';
                    if(defaultMapPoint'.self::$mapIndex.' == ""){
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng(39.916527,116.397128);
                    } else {
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng('.$this->formatValue($attrs['pointVal']).');
                    }
                    qqMap'.self::$mapIndex.' = new qq.maps.Map(document.getElementById("map-container-'.self::$mapIndex.'"),{
                        center: mapCenter'.self::$mapIndex.',
                        zoom: 13
                    });
                    qqMarker'.self::$mapIndex.' = new qq.maps.Marker({
                        map:qqMap'.self::$mapIndex.',
                        draggable:false,
                        position: mapCenter'.self::$mapIndex.'
                    }); 
                })
            })
            </script>';
        return $html;
    }

    private function parseMapPointArea($attrs, $content = '') {
        self::$mapIndex++;

        $html         = '';
        $point_val    = !isset($attrs['pointVal']) ? '' : $attrs['pointVal'];
        $pos_val      = !isset($attrs['posVal']) ? '' : $attrs['posVal'];
        $width        = !isset($attrs['width']) ? '100%' : $attrs['width'];
        $height       = !isset($attrs['height']) ? '100%' : $attrs['height'];

        if (self::$mapIndex == 1) {
            $key   = Config::get('app.qq_map.key');
            $html .= '<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key='.$key.'&libraries=geometry"></script>';
        }

        $html .= '<div id="map-container-'.self::$mapIndex.'" style="width:'.$width.'; height:'.$height.';"></div>
            <script type="text/javascript">
            var qqMap'.self::$mapIndex.',qqMarker'.self::$mapIndex.',qqPolygon'.self::$mapIndex.' = null,qqLatLngs'.self::$mapIndex.' = null;
            var defaultMapPoint'.self::$mapIndex.' = "'.$this->formatValue($attrs['pointVal']).'";
            jQuery(function($){
                $(window).load(function(){
                    var mapCenter'.self::$mapIndex.';
                    if(defaultMapPoint'.self::$mapIndex.' == ""){
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng(39.916527,116.397128);
                    } else {
                        mapCenter'.self::$mapIndex.' = new qq.maps.LatLng('.$this->formatValue($attrs['pointVal']).');
                    }           
                    qqMap'.self::$mapIndex.' = new qq.maps.Map(document.getElementById("map-container-'.self::$mapIndex.'"),{
                        center: mapCenter'.self::$mapIndex.',
                        zoom: 13
                    });
                    qqMarker'.self::$mapIndex.' = new qq.maps.Marker({
                        map:qqMap'.self::$mapIndex.',
                        draggable:true,
                        position: mapCenter'.self::$mapIndex.'
                    });

                    $.createPolygon'.self::$mapIndex.' = function(latLng){
                        if(!qqPolygon'.self::$mapIndex.'){
                            var tmpLng = qq.maps.geometry.spherical.computeOffset(latLng, 500, 0);
                            //西北角
                            var wnLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, -90);
                            //东北角
                            var enLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, 90);
                            //东南角
                            var esLatLng = qq.maps.geometry.spherical.computeOffset(enLatLng, 1000, 180);
                            //西南角
                            var nwLatLng = qq.maps.geometry.spherical.computeOffset(wnLatLng, 1000, 180);
                            qqLatLngs'.self::$mapIndex.' = [wnLatLng,enLatLng,esLatLng,nwLatLng];

                            qqPolygon'.self::$mapIndex.' = new qq.maps.Polygon({
                                map:qqMap'.self::$mapIndex.',
                                editable:true,
                                visible:true,
                                path:qqLatLngs'.self::$mapIndex.'
                            });
                        } else {
                            var heading = qq.maps.geometry.spherical.computeHeading(mapCenter'.self::$mapIndex.', latLng);
                            var distance = qq.maps.geometry.spherical.computeDistanceBetween(mapCenter'.self::$mapIndex.', latLng);
                            qqLatLngs'.self::$mapIndex.' = new Array();
                            qqPolygon'.self::$mapIndex.'.getPath().forEach(function(element, index){
                                qqLatLngs'.self::$mapIndex.'.push(qq.maps.geometry.spherical.computeOffset(element, distance, heading));
                            });
                            qqPolygon'.self::$mapIndex.'.setPath(qqLatLngs'.self::$mapIndex.');
                        }
                        mapCenter'.self::$mapIndex.' = latLng;
                    }

                    if(defaultMapPoint'.self::$mapIndex.' != ""){
                        var mapPos'.self::$mapIndex.' = "'.$this->formatValue($attrs['posVal']).'".split("|");
                        var mpLatLng'.self::$mapIndex.';
                        qqLatLngs'.self::$mapIndex.' = new Array();
                        for(var mpIndex = 0; mpIndex < mapPos'.self::$mapIndex.'.length; mpIndex++){
                            mpLatLng'.self::$mapIndex.' = mapPos'.self::$mapIndex.'[mpIndex].split(",");
                            qqLatLngs'.self::$mapIndex.'.push(new qq.maps.LatLng(mpLatLng'.self::$mapIndex.'[0],mpLatLng'.self::$mapIndex.'[1]));
                        }
                        qqPolygon'.self::$mapIndex.' = new qq.maps.Polygon({
                            map:qqMap'.self::$mapIndex.',
                            editable:true,
                            visible:true,
                            path:qqLatLngs'.self::$mapIndex.'
                        });
                    }
                })
            })
            </script>';
        return $html;
    }

    
    private function parseImageFrom($attrs, $content = '') {
        static $imageFromIndex = 0;
        $imageFromIndex++;

        $html = '';
        if ($imageFromIndex == 1) {
            $html .= '<canvas id="image-form-canvas"></canvas>
                <link rel="stylesheet" type="text/css" href="'.asset('static/cropper/cropper.css').'">
                <script charset="utf-8" src="' . asset('static/cropper/cropper.js') . '"></script>
                <script>
                    var isImageFormCropperInit = false;
                    var imageFormCropperSubmitFunc = null;
                    var imageFormCropperClearFunc = null;
                    var isImageFormCropperUpload = false;
                    jQuery(function($){
                        $.getIsImageFormCropper = function(){
                            try{
                                $("#image-form-canvas").get(0).getContext("2d");
                            } catch(e) {
                                return false;
                            }

                            if(window.FileReader){
                                return true;
                            } else {
                                return false;
                            }
                        }

                        $.setImageFormCropperUploadStatus = function(type) {
                            switch (type) {
                                case 1: 
                                    isImageFormCropperUpload = true;
                                    $("#image-from-cropper-preview-box > span").hide(); 
                                    $("#image-from-cropper-save-btn").removeClass("disabled");
                                    break;
                                case 2:
                                    isImageFormCropperUpload = false;
                                    $("#image-from-cropper-preview-box > span").html("图片上传中...").show();
                                    $("#image-from-cropper-save-btn").addClass("disabled"); 
                                    break;
                                case 3:
                                case 4:
                                    isImageFormCropperUpload = true;
                                    $("#image-from-cropper-preview-box > span").hide();
                                    $("#image-from-cropper-save-btn").removeClass("disabled"); 
                                    $("#image-from-cropper-box").addClass("none");
                                    break;
                                
                                default: 
                                    isImageFormCropperUpload = false;
                                    $("#image-from-cropper-preview-box > span").html("图片加载中...").show();
                                    $("#image-from-cropper-save-btn").addClass("disabled"); 
                                    break;
                            } 
                        }

                        $("#image-from-cropper-save-btn").click(function(event){ 
                            if(!isImageFormCropperUpload){
                                return;
                            }
                            $.setImageFormCropperUploadStatus(2);
                            var maxWidth = $("#image-from-cropper-save-btn").data("maxwidth");
                            var imageData = $("#image-from-cropper-preview").cropper("getData");
                            var width = maxWidth > imageData.width ? imageData.width : maxWidth;
                            var canvas = $("#image-from-cropper-preview").cropper("getCroppedCanvas", {
                                "width": width
                            });
                            if (imageFormCropperSubmitFunc != null) {
                                imageFormCropperSubmitFunc.call(this, canvas);
                            }
                        });

                        $("#image-from-cropper-clear-btn").click(function(event) {  
                            $("#image-from-cropper-box").addClass("none"); 
                            if (imageFormCropperClearFunc != null) {
                                imageFormCropperClearFunc.call(this);
                            }
                        });
                    });
                </script>
            ';
        }

        $image   = !isset($attrs['image']) ? '' : $attrs['image'];
        $id      = !isset($attrs['id']) ? 'image-from-val-'.$imageFromIndex : $attrs['id'];
        $name    = $attrs['name'];
        $toimg   = $attrs['toimg'];
        $loading = $attrs['loading'];
        $maxwidth = !isset($attrs['maxwidth']) ? 0 : $attrs['maxwidth'];
        $maxhight = !isset($attrs['maxhight']) ? 0 : $attrs['maxhight'];
        $iscropper = !isset($attrs['iscropper']) ? 0 : $attrs['iscropper'];

        if($iscropper == 1){
            $action = 'wap_action';
        } else {
            $action = 'action';
        }

        $image_args_name = '$image_form_args'.$imageFromIndex;
        $html  .= '<?php
            '.$image_args_name.' = \YiZan\Utils\Image::getFormArgs();
        ?>';

        $html  .= '<form id="image-form-'.$imageFromIndex.'" action="{{ '.$image_args_name.'[\''.$action.'\'] }}" method="post" enctype="multipart/form-data" target="image-form-iframe-'.$imageFromIndex.'">
            @foreach('.$image_args_name.'[\'args\'] as $image_form_arg_key => $image_form_arg_val)
            <input type="hidden" name="{{ $image_form_arg_key }}" class="form-hidden" value="{{ $image_form_arg_val }}" />
            @endforeach
        ';
        $html .= '<input type="hidden" name="{{ '.$image_args_name.'[\'save_path\'][\'name\'] }}" class="form-hidden" value="{{ '.$image_args_name.'[\'save_path\'][\'path\'] }}" />
            <input type="hidden" id="'.$this->formatValue($id).'" name="' . $this->formatValue($name) . '" class="form-hidden" value="' . $this->formatValue($image) . '" />
            <input type="hidden" id="image-form-iscanvas-'.$imageFromIndex.'" name="iscanvas" value="0"  />
            <input type="file" id="image-form-file-'.$imageFromIndex.'" name="{{ '.$image_args_name.'[\'file_name\'] }}" style="border:none; background:transparent; width:1000px; height:500px;" accept="image/*" />';
        $html .= '</form>
                <div id="image-form-hidden-'.$imageFromIndex.'" style="display:none;">
                    <input type="hidden" id="image-form-canvas-'.$imageFromIndex.'" name="{{ '.$image_args_name.'[\'file_name\'] }}"  />
                </div>
                <iframe id="image-form-iframe-'.$imageFromIndex.'" name="image-form-iframe-'.$imageFromIndex.'" style="display:none;"></iframe>
                <script>
                    var isCropper'.$imageFromIndex.' = '.$iscropper.';
                    var cropperMaxWidth'.$imageFromIndex.' = '.($maxwidth > 0 ? $maxwidth : 640).';
                    var imageFormFileHtml'.$imageFromIndex.' = \'<input type="file" id="image-form-file-'.$imageFromIndex.'" name="{{ '.$image_args_name.'[\'file_name\'] }}" style="border:none; background:transparent; width:1000px; height:500px;" accept="image/*" />\';
                    jQuery(function($){
                        $(document).on("change", "#image-form-file-'.$imageFromIndex.'", function(){
                            $.submitFormToIframe = function(type) {
                                $("#image-form-file-'.$imageFromIndex.'").get(0).disabled = true;
                                $("#image-form-iscanvas-'.$imageFromIndex.'").val(type);
                                $("#'.$this->formatValue($loading).'").show();
                                $("#image-form-iframe-'.$imageFromIndex.'").one("load", function(){
                                    try{
                                        var iframeDocument = this.contentDocument || this.contentWindow.document;
                                        var result = $.trim(iframeDocument.body.innerHTML);
                                        result = result == "" ? {"status":false} : JSON.parse(result);
                                        if (result && result.status) {
                                            var date = new Date();
                                            $("#'.$this->formatValue($toimg).'").attr("src","{!! formatImage('.$image_args_name.'[\'image_url\'], 100, 100, 1) !!}?" + date.getTime());
                                            $("#'.$this->formatValue($id).'").val("{{ '.$image_args_name.'[\'image_url\'] }}");
                                            $("#image-form-'.$imageFromIndex.'").trigger("uploadsucc");  
                                            $("#image-from-cropper-box").addClass("none"); 
                                            if (imageFormCropperClearFunc != null) {
                                                imageFormCropperClearFunc.call(this);
                                            }
                                            $.setImageFormCropperUploadStatus(3);
                                        } else {
                                            $.showError("上传图片失败");
                                            $.setImageFormCropperUploadStatus(4);
                                        }
                                    }catch(e){
                                        $.showError("上传图片失败");
                                        $.setImageFormCropperUploadStatus(4);
                                    } 
                                    
                                    $("#'.$this->formatValue($loading).'").hide();
                                    if (type == 1) {
                                        var canvasInput = $("#image-form-canvas-'.$imageFromIndex.'");
                                        canvasInput.val("");
                                        $("#image-form-hidden-'.$imageFromIndex.'").append(canvasInput);
                                    }
                                    $("#image-form-file-'.$imageFromIndex.'").remove();
                                    $("#image-form-'.$imageFromIndex.'").append(imageFormFileHtml'.$imageFromIndex.');
                                });
                                $("#image-form-'.$imageFromIndex.'").submit();
                            }

                            imageFormCropperSubmitFunc = null;
                            imageFormCropperClearFunc = null;
                            if($(this).val() != "" && typeof(this.files) !== "undefined") {
                                var file = this.files[0];
                                if ($.getIsImageFormCropper() && isCropper'.$imageFromIndex.' == 1) {
                                    $("#image-from-cropper-box").removeClass("none");
                                    $.setImageFormCropperUploadStatus(0);
                                    $("#image-from-cropper-save-btn").data("maxwidth", cropperMaxWidth'.$imageFromIndex.');
                                    var reader = new FileReader(); 
                                    reader.readAsDataURL(file);
                                    $(reader).load(function(){
                                        $("#image-from-cropper-preview").cropper("destroy");
                                        $("#image-from-cropper-preview").get(0).src = this.result;
                                        
                                        $("#image-from-cropper-preview").cropper({'.(($maxwidth > 0 && $maxhight > 0) ? '
                                              aspectRatio: '.$maxwidth.' / '.$maxhight.',
                                              autoCropArea: 1,
                                              minCropBoxWidth:'.$maxwidth.',
                                              minCropBoxHeight:'.$maxhight.',' : '').'
                                              strict: false,
                                              guides: false,
                                              highlight: false,
                                              dragCrop: false,
                                              cropBoxMovable: false,
                                              cropBoxResizable: false
                                        });

                                        $("#image-from-cropper-preview").on("built.cropper", function () {
                                            $.setImageFormCropperUploadStatus(1);
                                            $("#image-from-cropper-preview").cropper("crop");
                                            return true;
                                        });

                                        isImageFormCropperInit = true;
                                        imageFormCropperSubmitFunc = function(canvas) {
                                            var canvasInput = $("#image-form-canvas-'.$imageFromIndex.'");
                                            canvasInput.val(canvas.toDataURL());
                                            $("#image-form-'.$imageFromIndex.'").append(canvasInput);
                                            var fileInput = $("#image-form-file-'.$imageFromIndex.'");
                                            $("#image-form-hidden-'.$imageFromIndex.'").append(fileInput)
                                            $.submitFormToIframe(1);
                                        }

                                        imageFormCropperClearFunc = function() { 
                                            $.setImageFormCropperUploadStatus(3);
                                            $("#image-form-file-'.$imageFromIndex.'").remove();
                                            $("#image-form-'.$imageFromIndex.'").append(imageFormFileHtml'.$imageFromIndex.');
                                        }
                                    });
                                    return false;
                                } else if(file.size > 5242881){
                                    $.showError("请选择小于 5M 的图片");
                                    event.preventDefault();
                                    event.stopPropagation();
                                    event.stopImmediatePropagation();
                                    return false;
                                }
                            }
                            $.submitFormToIframe(0);
                        })
                    });
                </script>';
        return $html;
    }
    /**
     * 格式化Color颜色
     * @param  [type] $attrs   [description]
     * @param  string $content [description]
     * @return [type]          [description]
     */
    private function parseColor($attrs, $content = '') {
        $name   = $attrs['name'];
        $value  = $attrs['val'];
        $html .= '<input class="u-ipttext" name="'.$name.'" value="'.$value.'" type="text" id="cp1" style="cursor:pointer"/>';
        $html .= ' ';
        $html .= '<script src="{{ asset("js/jquery.colorpicker.js") }}"></script>';
        $html .= ' ';
        $html .= '  <script>
                    jQuery(function($){
                       $("#cp1").colorpicker({
                            fillcolor:true,
                            success:function(o,color){
                                $(o).css("color",color);
                            }
                        });
                    })
                </script>';
        return $html;
    }
}