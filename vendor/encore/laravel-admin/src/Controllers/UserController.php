<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.list'))
            ->body($this->grid()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());
		
		$grid->disableExport();

		$grid->disableRowSelector();
		
		$grid->disableColumnSelector();//关闭数据表格列选择器
		
        //$grid->id('ID')->sortable();
        $grid->username(trans('admin.username'));
        $grid->name(trans('admin.name'));
        $grid->roles(trans('admin.roles'))->pluck('name')->label();
		$grid->ip('IP')->pluck('ip')->label();
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
			$actions->disableView();
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->id('ID');
        $show->username(trans('admin.username'));
        $show->name(trans('admin.name'));
        $show->roles(trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->permissions(trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
		
		
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        //$form->display('id', 'ID');

		$form->tools(function (Form\Tools $tools) {
			
			//$tools->disableList();	//列表
			$tools->disableDelete();	//刪除
			$tools->disableView();	//查看
						
		});
		
		//關閉底部按鈕
		$form->footer(function ($footer) {

			// 去掉`重置`按钮
			//$footer->disableReset();

			// 去掉`提交`按钮
			//$footer->disableSubmit();

			// 去掉`查看`checkbox
			$footer->disableViewCheck();

			// 去掉`继续编辑`checkbox
			$footer->disableEditingCheck();

			// 去掉`继续创建`checkbox
			$footer->disableCreatingCheck();

		});
		
        if (request()->isMethod('POST')) {
            $userTable = config('admin.database.users_table');
            $userNameRules = "required|unique:{$userTable}";
        } else {
            $userNameRules = 'required';
        }

        $form->text('username', '帐号')->rules($userNameRules);
        $form->text('name', '昵称')->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'))->required();
		/**/
        //$form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));	//額外權限

		//ip限制
		
		$form->table('ip','IP白名单', function ($table) {
			$table->text('ip','');
		});
		/**/
		/*
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
		*/
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });
	
        return $form;
    }
	
	
	
}
