<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Layout\Content;
use App\Admin\Repositories\Pem;
use App\Models\Group;
use App\Models\Pem as PemModel;
use Dcat\Admin\Contracts\UploadField as UploadFieldInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dcat\Admin\Admin;

class PemController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('Pem文件')
            ->body($this->grid());
    }

    protected function grid()
    {
        return new Grid(new Pem(), function (Grid $grid) {
            $grid->quickSearch(function ($model, $query) {
                $model->where('discription', 'like', "%{$query}%");
            })->placeholder('证书用途...');
            
            $grid->showColumnSelector();
            $grid->number();
            $grid->column('group', "项目组")->display(function($groupId) {
                return Group::find($groupId)->name;
            });
            
            $grid->column('env', "环境");
            $grid->column('env', "环境")->filter(
                Grid\Column\Filter\Equal::make()
            );

            $grid->column('discription', "证书用途");
            $grid->column('email', "负责人邮箱");
            $grid->column('date', '过期时间')->sortable();
            $grid->column('created_at');
            $grid->column('updated_at');

            $grid->column('download', '下载Pem文件')->display(function ($pem) {
                $downloadUrl = route('dcat.admin.pem.download', ['id' => $this->id]);
                return '<a href="'. $downloadUrl .'"">下载</a>';
            });

            $grid->actions(function ($actions) {
                $actions->disableView();
            });

        });
    }

    public function create(Content $content)
    {
        return $content
            ->body($this->form());
    }

    protected function form()
    {
        $form = new Form(new Pem());
        $groups = $this->createGroups();
        $form->select('group', '项目组')->options($groups)->required();
        $form->select('env', '环境')->options(['通用' => '通用', 'UAT' => 'UAT', 'PRO' => 'PRO'])->required();
        $form->text('discription', '证书用途')->required();
        $form->email('email', '负责人邮箱')->required();
        $form->file('path', '上传pem文件')->accept('pem')->url('pem/uploadfile')->autoUpload(true)->required();
        $form->hidden('date');

        $form->saving(function (Form $form) {
            $path = $form->path;
            if (!empty($path)) {
                $file = public_path('uploads/').$path;
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    // 解析pem文件
                    $pem = openssl_x509_parse($content);
                    // 获取证书过期时间
                    $form->date = date('Y-m-d', $pem['validTo_time_t']);
                }
            }
        });

        $form->disableViewButton();
        return $form;
    }

    protected function createGroups() {
        return Group::all()->pluck('name', 'id');
    }

    // 下载pem文件
    public function download($id) {
        $pem = PemModel::find($id);
        if (!$pem) {
            admin_toastr('证书不存在', 'error');
            return redirect('/admin/pem');
        }
        $path = public_path('uploads/').$pem->path;
        if (!file_exists($path)) {
            admin_toastr('证书不存在', 'error');
            return redirect('/admin/pem');
        }
        $headers = [
            'Content-Type' => 'application/octet-stream'
        ];

        return response()->download($path, null, $headers);
    }

    public function destroy($id) {
        $pem = PemModel::find($id);
        $path = $pem->path;
        if (file_exists($path)) {
            @unlink($path);
        }
        return $this->form()->destroy($id);
    }
}
