<?php

namespace App\Admin\Controllers;

use App\Model\logs;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Illuminate\Support\Facades\DB;	//

class LogsController extends Controller
{
	
   use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('操作纪录')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('操作纪录')
            ->description('检视')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('操作纪录')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('操作纪录')
            ->description('新建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new logs);
		
		//關閉導出
		$grid->disableExport();
		
		// 關閉選擇器
		$grid->disableRowSelector();
		
		//關閉新增按鈕
		$grid->disableCreateButton();
		//自訂
		
		$grid->disableColumnSelector();//关闭数据表格列选择器
		
		$grid->filter(function($filter){

			$filter->disableIdFilter();

			// 在这里添加字段过滤器
			$filter->like('user_id', '操作者')->select($this->getUsername());
			//$filter->like('method', '操作');
			

		});
		
		//禁用操作列
		$grid->disableActions();
		// 關閉操作按鈕
		/*
		$grid->actions(function ($actions) {
			
			$actions->disableEdit();
			
			$actions->disableView();
			
			$actions->disableDelete();
			
		});
		*/
		
		$grid->model()->orderBy('created_at','DESC');
		
		$user_name = [];
		$user_name = $this->getUsername();

		$grid->column('user_id', '操作者')->display(function ($user_id) use($user_name){
				
				if(isset($user_name[$user_id])){
					return $user_name[$user_id];
				}else{
					
					return '操作者已被删除';
					
				}
				//return $user_name[$user_id];
				
            });
		
		$grid->column('ip', 'IP位置');
		
		$action_name = [];
		$action_name = $this->getAction();
		
		$grid->column('method', '操作')->display(function ($method) use($action_name){
			
				if(isset($action_name[$this->id])){
					if($action_name[$this->id] == '登录'){
						return "<span class=\"label label-success\">{$action_name[$this->id]}</span>";
					}
					if($action_name[$this->id] == '删除'){
						return "<span class=\"label label-danger\">{$action_name[$this->id]}</span>";
					}
					if($action_name[$this->id] == '上传'){
						return "<span class=\"label label-warning\">{$action_name[$this->id]}</span>";
					}
					if($action_name[$this->id] == '修改'){
						return "<span class=\"label label-warning\">{$action_name[$this->id]}</span>";
					}
					if($action_name[$this->id] == '登出'){
						return "<span class=\"label label-success\">{$action_name[$this->id]}</span>";
					}
				}else{

					return "<span class=\"label label-primary\">检视</span>";
					
				}
			});
			
		$path_name = [];
		$path_name = $this-> getPath();
		
		$grid->column('path', '操作位置')->display(function ($path) use($path_name){
				if(isset($path_name[$this->id])){
					return$path_name[$this->id];
					}else{
						if($path =='admin'){
							return'首页';
						}
						if($path =='admin/auth/logout' || $path =='admin/auth/login'){
							return'登录登出页';
						}
						if($path =='admin/auth/setting' ){
							return'个人帐号设置';
						}
						return$path;
					}
			});
		
		$grid->column('created_at', '操作时间');
		
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(activity::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new activity);

        return $form;
    }
	
	protected function getUsername()
	{
		$users = "SELECT id , username  FROM admin_users";
		
		$user = DB::select($users);
		
		foreach($user as $k=>$v){
			$user_data[$v->id] = $v->username;
		}
		
		//print_r($user_data);exit;
		return $user_data;
	}
	protected function getAction()
	{
		//get action data for database except get
		$actions = "SELECT id , method  FROM admin_operation_log WHERE method NOT LIKE 'GET'";
		$action = DB::select($actions);
		
		$actions_logout = "SELECT id , path  FROM admin_operation_log WHERE `path`='admin/auth/logout'";
		$action_logout = DB::select($actions_logout);
		
		$actions_list = "SELECT id , path , input FROM admin_operation_log WHERE `path`='admin/listChange'";
		$action_list = DB::select($actions_list);
		
		$action_data = [];
		//all action
		foreach($action as $k=>$v){
			$action_check = $this->get_action($v->method);
			$action_data[$v->id] = $action_check;
		}
		//logout action
		foreach($action_logout as $k=>$v){
			$action_check = $this->get_action($v->path);
			$action_data[$v->id] = $action_check;
		}
		//action list
		foreach($action_list as $k=>$v){
			
			$action_check = $this->get_action(json_decode($v->input)->action);
			$action_data[$v->id] = $action_check;
		}
		//print_r($action_data);exit;
		return $action_data;
	}
	protected function get_action($method) {
		switch ($method) {
			case 'POST':
				$action = '上传';
				break;
			case 'PUT':
				$action = '修改';
				break;
			case 'DELETE':
				$action = '删除';
				break;
			case 'OPTIONS':
				$action = '登录';
				break;
			case 'admin/auth/logout':
				$action = '登出';
				break;
			case 'remove':
				$action = '删除';
				break;
			case 'add':
				$action = '上传';
				break;
			case 'edit':
				$action = '修改';
				break;
			default:
				$action = $method;
		}

		return $action;
	}
	// get action path
	protected function getPath()
	{
		
		//get data
		$paths = "SELECT id , path  FROM admin_operation_log ";
		$path = DB::select($paths);
		
		$new_path = [];
		
		foreach($path as $k=>$v){
			$new_path[$v->id]= explode('/',$path[$k]->path);
			//print_r($new_path[$k]);exit;
			$new_path[$v->id] = $this->get_path($new_path[$v->id]);
		}
		return($new_path);
	}	
	
	protected function get_path($method) {
		
		if(in_array('member',$method)){
			$method = '会员列表';
			return $method;
		}
		if(in_array('users',$method)){
			$method = '帐号管理';
			return $method;
		}
		if(in_array('roles',$method)){
			$method = '角色设定';
			return $method;
		}
		if(in_array('permissions',$method)){
			$method = '权限设定';
			return $method;
		}
		if(in_array('menu',$method)){
			$method = '菜单设定';
			return $method;
		}
		if(in_array('upload',$method)){
			$method = '会员资料导入';
			return $method;
		}
		if(in_array('operation',$method)){
			$method = '操作纪录';
			return $method;
		}
		if(in_array('listChange',$method)){
			$method = '首页-待办事项';
			return $method;
		}

		
	}
}

