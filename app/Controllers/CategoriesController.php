<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CategoriesController extends BaseController
{
    public function index()
    {
        return view('categories/index', [
            'categories' => (new CategoryModel())->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function create()
    {
        return view('categories/form', [
            'category' => ['name' => '', 'description' => '', 'status' => 'active'],
            'errors' => [],
            'action' => site_url('categories'),
            'title' => 'Thêm danh mục',
        ]);
    }

    public function store()
    {
        $model = new CategoryModel();
        $data = $this->request->getPost(['name', 'description', 'status']);
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[1000]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (! $this->validate($rules)) {
            return view('categories/form', [
                'category' => $data,
                'errors' => $this->validator->getErrors(),
                'action' => site_url('categories'),
                'title' => 'Thêm danh mục',
            ]);
        }

        $model->insert($data);

        return redirect()->to(site_url('categories'))->with('success', 'Đã thêm danh mục.');
    }

    public function edit(int $id)
    {
        $category = (new CategoryModel())->find($id);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('categories/form', [
            'category' => $category,
            'errors' => [],
            'action' => site_url("categories/{$id}"),
            'title' => 'Chỉnh sửa danh mục',
        ]);
    }

    public function update(int $id)
    {
        $model = new CategoryModel();
        $category = $model->find($id);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost(['name', 'description', 'status']);
        $rules = [
            'name' => "required|min_length[2]|max_length[100]|is_unique[categories.name,id,{$id}]",
            'description' => 'permit_empty|max_length[1000]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (! $this->validate($rules)) {
            return view('categories/form', [
                'category' => array_merge($category, $data),
                'errors' => $this->validator->getErrors(),
                'action' => site_url("categories/{$id}"),
                'title' => 'Chỉnh sửa danh mục',
            ]);
        }

        $model->update($id, $data);

        return redirect()->to(site_url('categories'))->with('success', 'Đã cập nhật danh mục.');
    }

    public function delete(int $id)
    {
        $model = new CategoryModel();
        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($model->hasBooks($id)) {
            return redirect()->to(site_url('categories'))
                ->with('error', 'Không thể xóa thể loại đang có sách.');
        }

        $model->delete($id);

        return redirect()->to(site_url('categories'))->with('success', 'Đã xóa danh mục.');
    }
}
