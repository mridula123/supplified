<?php
    class AIOTree extends CWidget
    {
        /** * DATA * **/
        public $data=array();
        /** sample format array(
         *                  '1'=>array('parentid'=>'','text'=>'PARENT'),
         *                  '2'=>array('parentid'=>'2','text'=>'CHILD'),
         *                 )
         **/
        
        /** * MODEL     * **/
        public $model;
        public $attribute='attr';//default
        
        /** TYPES OF TREE **/
        public $type='label';//radio, checkbox
        
        /** only for checkbox **/
        public $selectParent=false;
        
        /**      * HEADER     **/
         
        /** header value show or hide **/
        public $headerShow=true;
        /** header text **/
        public $header='';
        
        
        /** TREE CONTROL COLLAPSE EXPAND **/
        public $label_collapse='';
        public $label_expand='';
        
        /** set controll params for collapse and expand**/
        public $controlShow=true;
        public $controlTag='div';
        public $controlClass='';
        public $controlId='aiotree_control';
        public $controlStyle='';
        public $controlLabel=array();//collapse, expand
        public $controlDivider='|';
        public $controlHtmlOptions=array();
        
        /** set parent class **/
        public $parentShow=true;
        public $parentTag='div';
        public $parentClass='';
        public $parentId='';
        public $parentStyle='';
        public $parentHtmlOptions=array();
        
        /** TREE UL CLASS AND STYLE **/
        public $TreeClass='';
        public $TreeStyle='';
        public $TreeId='';//required
        public $TreeHtmlOptions=array();
        
        /** LI CALSS AND STYLE ID **/
        public $liListClass='';
        public $liListId='';
        public $liListStyle='';
        public $liHtmlOptions=array();

        
        public function init(){
            if(trim($this->TreeId)=='')
                $this->TreeId=rand(100000,999996);            
        }
        
        public function run(){
            $this->include_js();
            $this->parent_start();
            $this->tree_header();
            $this->aiotreecontrol();
            $this->aiotree_list();
            $this->parent_close();
        }

        public function aiotree_list(){
            /** ul open **/
            echo '<ul id="'.$this->TreeId.'"';
            $this->display_html_attr($this->TreeClass,'class');
            $this->display_html_attr($this->TreeStyle,'style');
            $this->TreeHtmlOptions['id']='';
            $this->display_html_attr($this->TreeHtmlOptions);                        
            echo '>';            
            $this->display_list();
            
            /** ul end**/
            echo '</ul>';
        }
        
        public function display_list()
        {

            $listdata=array();
            $parentlist=array();
            foreach($this->data as $key=>$row){
                if($row['parentid']==0){
                    $parentlist[]=$key;
                }else{
                    $listdata[$row['parentid']][]=$key;
                }
            }

            foreach($parentlist as $parentid){
                $this->maketree($listdata,$parentid);
            }
        }
    
        public function maketree($listdata,$id)
        {
            if(isset($listdata[$id]) && is_array($listdata[$id])){                
                $this->echo_li($id);
                echo '<ul>';                
                
                foreach($listdata[$id] as $data){            
                    $this->maketree($listdata,$data);
                }
                
                echo '</ul>';
            }else {
                $this->echo_li($id);
            }
        }
        
        public function echo_li($id){
            echo '<li>';
            if($this->type=='label' || $this->type==''){
                echo '<label';
                $this->display_html_attr($this->liListClass,'class');
                $this->display_html_attr($this->liListId,'id');
                $this->display_html_attr($this->liListStyle,'style');
                $this->display_html_attr($this->liHtmlOptions);                
                echo '>';
                echo $this->data[$id]['text'];
                echo '</label>';    
            }
            else if($this->type=='radio' || $this->type=='checkbox')
            {
                $classname='aiotree';
                if(isset($this->model) && is_object($this->model)){
                    $classname=get_class($this->model);
                }
                echo'<input type="'.$this->type.'" value="'.$id.'"';
                echo ' name="'.$classname.'['.$this->attribute.'][]" ';
                $this->display_html_attr($this->liListClass,'class');
                $this->display_html_attr($this->liListId,'id');
                $this->display_html_attr($this->liListStyle,'style');
                $this->display_html_attr($this->liHtmlOptions);
                echo '>';            
                
                echo $this->data[$id]['text'];
            }
        }
            
        public function include_js(){
            $cs = Yii::app()->getClientScript();
            $cs->registerCoreScript('jquery');
            $cs->registerCoreScript('jquery.ui');
            $cs->registerCoreScript('treeview');
            $cs->registerCssFile($this->publish_tree_css()."/jquery.treeview.css");
            $this->register_script_tree_1();
        }
        
        public function publish_tree_css(){
            $tree_Css=dirname(__FILE__).DIRECTORY_SEPARATOR.'css';
            $url=Yii::app()->getAssetManager()->publish($tree_Css);
            return $url;
        }
        
        public function register_script_tree_1(){
            Yii::app()->clientScript->registerScript($this->TreeId."-Js",'
 			    $("#'.$this->TreeId.'").treeview({
				    collapsed: false,
				    animated: "medium",
				    control:"#'.$this->controlId.'",
				    persist: "location"
                });            
            ');
            if($this->selectParent==true)
                $this->check_uncheck_parent();
        }
        
        public function check_uncheck_parent(){
            Yii::app()->clientScript->registerScript("AIOTree_rs_2".$this->TreeId,"             
                $('#".$this->TreeId." input[type=\"checkbox\"]').each (
                    function () {
                        $(this).bind('click change', function (){
                        if($(this).is(':checked')) {                        
                            $(this).parents('ul').siblings('input[type=\"checkbox\"]').attr('checked', 'checked');
                        } else {
                            $(this).siblings('ul').find('input[type=\"checkbox\"]').removeAttr('checked', 'checked');
                        }
                    });
                    }
                ); 
            ");
        }
        
        /** parent tag show and close **/        
        public function parent_start(){
            if($this->parentShow==true){
                echo '<'.$this->parentTag;
                $this->display_html_attr($this->parentClass,'class');
                $this->display_html_attr($this->parentId,'id');
                $this->display_html_attr($this->parentStyle,'style');
                $this->display_html_attr($this->parentHtmlOptions);
                echo '>';                
            }
        }
        
        public function parent_close(){
            if($this->parentShow==true){
                echo '</'.$this->parentTag.'>';
            }
        }
        /** parent tag show and close **/

        /** Tree header **/
        public function tree_header(){
            if($this->headerShow==true)
                echo '<div class="aiotree_header">'.$this->header.'</div>';
        }
            
        /** control of collapse and expand details **/
        public function aiotreecontrol(){
            if($this->controlShow==true){
                /** control tag start **/
                echo '<'.$this->controlTag;
                $this->display_html_attr($this->controlClass,'class');
                $this->display_html_attr($this->controlId,'id');
                $this->display_html_attr($this->controlStyle,'style');
                $this->display_html_attr($this->controlHtmlOptions);
                echo '>';
                
                /** collapse and expand **/
                echo '<a href="?#" id="aiotree_collapse" class="aiotree_collapse">';
                echo isset($this->controlLabel['collapse'])?$this->controlLabel['collapse']:$this->label_collapse;
                echo '</a>';
                echo $this->controlDivider;
                echo '<a href="?#" id="aiotree_expand" class="aiotree_expand">';
                                echo isset($this->controlLabel['expand'])?$this->controlLabel['expand']:$this->label_expand;
                echo '</a>';
                
                /** control tag close **/
                echo '</'.$this->controlTag.'>';
                }
            //echo '<span id="aiotree_control"><a href="?#">Collapse All</a> | <a href="?#">Expand All</a></span>';
        }
        
        /** display id, class, style and more **/
        public function display_html_attr($params,$attr=''){
            if(is_string($params) && trim($params)!='')
                echo " ".$attr."='".$params."' ";
            else if(is_array($params)){
                foreach($params as $attr=>$value)
                    echo " ".$attr."='".$value."' ";
            }
        }        
    }
?>