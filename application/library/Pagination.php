<?php

class Pagination {
	/** /1 */
	const TYPE_PARAM = 1;
	/** &p=1 */
	const TYPE_ENTITY = 2;
	/** /page/1 */
	const TYPE_DETAIL = 3;

    /**
     * 分页函数
     *
     * @param string $url
     * @param int $perPage
     * @param int $currentPage
     * @param int $totalItems
     * @param int $delta
     * @param string $target
     * @return string
     */
    public static function createPage($url, $perPage, $currentPage, $totalItems, $delta = 2, $target = '_self',$isSearchIpt = false)
    {
        $t_high = ceil($totalItems / $perPage) == 0 ? 1 : ceil($totalItems / $perPage);
        $high = $currentPage + $delta;
        $low = $currentPage - $delta;
        if ($high > $t_high)	{
            $high = $t_high;
            $low = $t_high - 2 * $delta;
        }
        if ($low < 1) {
            $low = 1;
            $high = $low + 2 * $delta;
            if($high > $t_high) $high = $t_high;
        }
        $offset = ($currentPage - 1) * $perPage + 1;
        if ($offset < 0) $offset = 0;
        $end = $offset + $perPage - 1;
        if($end > $totalItems) $end = $totalItems;

        $ret_string = '<div class="fl">显示： <select class="numpage" name="numPerPage" id="numPerPage">';

        if($perPage == 10){
            $ret_string .= '<option value="10" selected="selected">10</option>';
        }else{
            $ret_string .= '<option value="10">10</option>';
        }
        if($perPage == 20){
            $ret_string .= '<option value="20" selected="selected">20</option>';
        }else{
            $ret_string .= '<option value="20">20</option>';
        }
        if($perPage == 50){
            $ret_string .= '<option value="50" selected="selected">50</option>';
        }else{
            $ret_string .= '<option value="50">50</option>';
        }
        if($perPage == 100){
            $ret_string .= '<option value="100" selected="selected">100</option>';
        }else{
            $ret_string .= '<option value="100">100</option>';
        }
        if($perPage == 200){
            $ret_string .= '<option value="200" selected="selected">200</option>';
        }else{
            $ret_string .= '<option value="200">200</option>';
        }

        $ret_string .= '</select> 条， ';
        $ret_string .= " 共<span class='topage'>{$totalItems}</span>条</div>";

        if($currentPage > 1)
        {
            $ret_string .= '<a href=\'' . str_replace('%d', 1, $url) . "' target='{$target}'>首页</a>";
            $ret_string .= '<a href=\'' . str_replace('%d', $currentPage - 1, $url) . "' target='{$target}' style='margin-right: -2px;'>上一页</a>";
        }
        else {
            $ret_string .= '<span class="cd">首页</span>';
            $ret_string .= "<span class='cd' style='margin-right: -2px;'>上一页</span>";
        }
        $links = array();
        for (;$low <= $high; $low++)
        {
            if($low != $currentPage) $links[] = '<a href=\'' . str_replace('%d', $low, $url) . '\' target=\'' . $target . '\'>' . $low . '</a>';
            else $links[] = "<span class='curr'>{$low}</span>";
        }
        $links = implode('', $links);
        $ret_string .= "\r\n" . $links;

        if($currentPage < $t_high){
            $ret_string .= '<a href=\'' . str_replace('%d', $currentPage + 1, $url) . "' target='{$target}'>下一页</a>";
            $ret_string .= '<a href=\'' . str_replace('%d', $t_high, $url) . '\' target=\'' . $target . '\'>尾页</a>';
        }
        else{
            $ret_string .= '<span class="cd">下一页</span>';
            $ret_string .= '<span class="cd">尾页</span>';
        }
        if($isSearchIpt)
        {
            $ret_string .= '　第 <input type="text" id="searchNum" class="ipt" /> 页';
            $url_str =
            $ret_string .= ' <input type="button" class="plr10p br ptb3p white back-gray b0" value="跳转" onClick="location.href= \'' .$url .'\'.replace(\'%d\',$(\'#searchNum\').val());" />';
        }
        return $ret_string . '';
    }
}
