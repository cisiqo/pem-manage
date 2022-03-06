<?php
namespace App\Admin\Controllers;

use Dcat\Admin\Traits\HasUploadedFile;

class FileController
{
    use HasUploadedFile;

    public function handle()
    {
        $disk = $this->disk('admin');
        $dir = 'files';

        // 判断是否是删除文件请求
        if ($this->isDeleteRequest()) {
            // 删除文件并响应
            return $this->deleteFileAndResponse($disk);
        }

        // 获取上传的文件
        $file = $this->file();
        // 获取上传文件名称
        $fileName = $file->getClientOriginalName();
        $path = "{$dir}/$fileName";

        if ($disk->exists($path)) {
            return $this->responseErrorMessage('文件已存在');
        }

        $result = $disk->putFileAs($dir, $file, $fileName);
        return $result
            ? $this->responseUploaded($path, $disk->url($path))
            : $this->responseErrorMessage('文件上传失败');
    }
}