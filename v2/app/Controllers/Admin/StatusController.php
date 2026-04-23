<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Find\Status;

class StatusController extends BaseController
{
    public function indexStatus()
    {
        $model = new Status();
        $data['statusList'] = $model->findAll();
        return view('Admin/status/index', $data);
    }

    public function createStatus()
    {
        if ($this->request->getMethod() === 'post') {
            $model = new Status();
            $model->insert($this->request->getPost());
            return redirect()->to('/admin/status');
        }
        return view('Admin/status/create');
    }

    public function editStatus($id)
    {
        $model = new Status();
        $status = $model->find($id);
        if ($this->request->getMethod() === 'post') {
            $model->update($id, $this->request->getPost());
            return redirect()->to('/admin/status');
        }
        return view('Admin/status/edit', ['status' => $status]);
    }

    public function deleteStatus($id)
    {
        $model = new Status();
        $model->delete($id);
        return redirect()->to('/admin/status');
    }
}
