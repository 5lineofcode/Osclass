<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */
 
     class items_processing_ajax extends Item
     {
        private $items;
        private $result;
        private $toJSON;
        private $toDatatables;

        private $limit;
        private $start;
        private $total;
        private $search;
        private $order_by = array();
        private $stat;

        private $column_names = array(0=>'dt_pub_date', 1=>'s_title', 2=>'s_description', 3=>'s_category_name', 4=>'dt_pub_date');

        /* For Datatables */
        private $sOutput = null;
        private $sEcho = null;

        private $_get;

        function __construct($params) {

            parent::__construct() ;

            $this->_get = $params;
            $this->getDBParams();


            $mSearch = new Search(true);
            $mSearch->limit($this->start, $this->limit);
            $mSearch->order($this->order_by['column_name'], $this->order_by['type']);
            if(Params::getParam("catId")!="") {
                $mSearch->addCategory(Params::getParam("catId"));
            }
            if($this->search) {
                $mSearch->addConditions(sprintf("(d.s_title LIKE '%%%s%%' OR d.s_description LIKE '%%%s%%')", $this->search, $this->search));
            }
            
            switch($this->stat) {
                case "spam":
                    $mSearch->addConditions("s.`i_num_spam` > 0");
                    $mSearch->addConditions(sprintf("%st_item.pk_i_id = s.fk_i_item_id", DB_TABLE_PREFIX));
                    $mSearch->addTable(sprintf("%st_item_stats s", DB_TABLE_PREFIX));
                    break;
                case "duplicated":
                    $mSearch->addConditions("s.`i_num_duplicated` > 0");
                    $mSearch->addConditions(sprintf(" %st_item.pk_i_id = s.fk_i_item_id", DB_TABLE_PREFIX));
                    $mSearch->addTable(sprintf("%st_item_stats s", DB_TABLE_PREFIX));
                    break;
                case "bad":
                    $mSearch->addConditions("s.`i_num_bad_classified` > 0");
                    $mSearch->addConditions(sprintf(" %st_item.pk_i_id = s.fk_i_item_id", DB_TABLE_PREFIX));
                    $mSearch->addTable(sprintf("%st_item_stats s", DB_TABLE_PREFIX));
                    break;
                case "offensive":
                    $mSearch->addConditions("s.`i_num_offensive` > 0");
                    $mSearch->addConditions(sprintf(" %st_item.pk_i_id = s.fk_i_item_id", DB_TABLE_PREFIX));
                    $mSearch->addTable(sprintf("%st_item_stats s", DB_TABLE_PREFIX));
                    break;
                case "expired":
                    $mSearch->addConditions("s.`i_num_expired` > 0");
                    $mSearch->addConditions(sprintf(" %st_item.pk_i_id = s.fk_i_item_id", DB_TABLE_PREFIX));
                    $mSearch->addTable(sprintf("%st_item_stats s", DB_TABLE_PREFIX));
                    break;
                case "pending":
                    $conditions[] = "i.`b_active` = 0";
                    $mSearch->addConditions(sprintf("%st_item.b_active = 0", DB_TABLE_PREFIX));
                    break;
                case "enabled":
                    $conditions[] = "i.`b_enabled` = 1";
                    $mSearch->addConditions(sprintf("%st_item.b_enabled = 1", DB_TABLE_PREFIX));
                    break;
                case "disabled":
                    $mSearch->addConditions(sprintf("%st_item.b_enabled = 0", DB_TABLE_PREFIX));
                    break;
                default:
                    break;
            }
            
            
            
            
            $list_items = $mSearch->doSearch(true);
            $this->result = Item::newInstance()->extendCategoryName(Item::newInstance()->extendData($list_items));
            $this->filtered_total = $mSearch->count();
            $this->total = $this->total_items();

            $this->toDatatablesFormat();
            $this->dumpToDatatables();
        }

        function __destruct() {
            unset($this->_get);
        }

        private function getDBParams() {
            foreach($this->_get as $k=>$v) {
                if($k == 'iDisplayStart') $this->start = intval($v);
                if($k == 'iDisplayLength') $this->limit = intval($v);
                if($k == 'sEcho') $this->sEcho = intval($v);

                /* for sorting */
                if($k == 'iSortCol_0') $this->order_by['column_name'] = $this->column_names[$v];
                if($k == 'sSortDir_0') $this->order_by['type'] = $v;
                if($k == 'sSearch') $this->search = $v;
                if($k == 'stat') $this->stat = $v;
            }
        }

        /* START - format functions */
        private function toDatatablesFormat() {
            $this->sOutput = '{';
            $this->sOutput .= '"sEcho": '.($this->sEcho).', ';
            $this->sOutput .= '"iTotalRecords": '.($this->total).', ';
            $this->sOutput .= '"iTotalDisplayRecords": '.($this->filtered_total).', ';
            $this->sOutput .= '"aaData": [ ';

            if(count($this->result)>0) {
                $count = 0;
                foreach ($this->result as $aRow)
                {
                    
                    $this->sOutput .= "[";
                    $this->sOutput .= '"<input type=\'checkbox\' name=\'id[]\' value=\''.$aRow['pk_i_id'].'\' />",';
                    $this->sOutput .= '"'.addslashes(preg_replace('|\s+|',' ',$aRow['s_title'])).' <br/>';
                    $this->sOutput .= '<div id=\'datatable_wrapper\'><div id=\'datatables_quick_edit\' ';
                    if($count % 2) {
                        $this->sOutput .= ' class=\'even\' ';
                    }else{
                        $this->sOutput .= ' class=\'odd\' ';
                    }
                    $this->sOutput .= ' style=\'position:absolute;padding:4px;\'>';
                    $this->sOutput .= '<a href=\''.osc_admin_base_url(true).'?page=comments&action=list&amp;id='.$aRow['pk_i_id'].'\'>'.  __('View comments') .'</a>';
                    $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=media&action=list&amp;id='. $aRow['pk_i_id'] .'\'>'. __('View media') .'</a>';
                    if(isset($aRow['b_active']) && ($aRow['b_active'] == 1)) {
                        $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=status&amp;id='. $aRow['pk_i_id'] .'&amp;value=INACTIVE\'>'. __('Deactivate') .'</a>';
                    } else if (isset($aRow['b_active']) && ($aRow['b_active'] == 0)) {
                        $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=status&amp;id='. $aRow['pk_i_id'] .'&amp;value=ACTIVE\'>'. __('Activate') .'</a>';
                    }
                    if(isset($aRow['b_enabled']) && ($aRow['b_enabled'] == 1)) {
                        $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=status&amp;id='. $aRow['pk_i_id'] .'&amp;value=DISABLE\'>'. __('Disable') .'</a>';
                    } else if (isset($aRow['b_enabled']) && ($aRow['b_enabled'] == 0)) {
                        $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=status&amp;id='. $aRow['pk_i_id'] .'&amp;value=ENABLE\'>'. __('Enable') .'</a>';
                    }
                    if(isset($aRow['b_premium']) && $aRow['b_premium']) {
                        $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=status_premium&amp;id='. $aRow['pk_i_id'] .'&amp;value=0\'>'. __('Unmark as premium') .'</a>';
                    } else { //if (isset($aRow['b_premium']) && !$aRow['b_premium']) {
                        $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=status_premium&amp;id='. $aRow['pk_i_id'] .'&amp;value=1\'>'. __('Mark as premium') .'</a>';
                    }
                    $this->sOutput .= ' | <a href=\''.osc_admin_base_url(true).'?page=items&action=item_edit&amp;id='. $aRow['pk_i_id'] .'\'>'. __('Edit') .'</a>';
                                            $var = 'onclick=\"javascript:return confirm(\''.__('This action can not be undone. Are you sure you want to continue?').'\')\"';
                    $this->sOutput .= ' | <a '.$var.' href=\''.osc_admin_base_url(true).'?page=items&action=delete&amp;id[]='. $aRow['pk_i_id'] .'\'>'. __('Delete') .'</a>';

                    if($this->stat){
                        $this->sOutput .= ' | <a '.$var.' href=\''.osc_admin_base_url(true).'?page=items&action=clear_stat&amp;stat='.$this->stat.'&amp;id='. $aRow['pk_i_id'] .'\'>'. __('Clear') .' '.$this->stat.'</a></div>",';
                    } else {
                        $this->sOutput .= '</div></div>",';
                    }

                    /* if $_GET['stat'] */
                    if(isset($aRow['num_total'])) {
                        $this->sOutput .= '"'.addslashes(preg_replace('|\s+|',' ',$aRow['num_total'])).'",';
                    } else {
                        $description = mb_substr($aRow['s_description'], 0, 200, 'utf-8');
                        $this->sOutput .= '"'.addslashes(preg_replace('|\s+|',' ', $description)).'",';
                    }
                    /* END OF - if $_GET['stat'] */

                    $this->sOutput .= '"'.addslashes($aRow['s_category_name']).'",';
                    $this->sOutput .= '"'.addslashes($aRow['dt_pub_date']).'"';

                    if(end($this->result) == $aRow) {
                        $this->sOutput .= "]";

                    } else {
                        $this->sOutput .= "],";
                    }
                    $count++;
                }
            //$this->sOutput = substr_replace( $this->sOutput, "", -1 ); /* XXX: for some reason this line breaks everything... */
            }
            $this->sOutput .= ']}';

        }

        private function toJSON($result) {
            $this->toJSON = json_encode($result);
        }
        /* END - format functions */

        /* START - dump results */
        private function dumpResult() {
            $this->toJSON($this->result);
            echo $this->toJSON();
        }

        private function dumpToDatatables() {
            echo str_replace("\'", "'", $this->sOutput);
        }
        /* END - dump results */
     }
     
?>