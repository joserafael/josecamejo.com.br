<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogSubcategory::with('category');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('description', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('blog_category_id', $request->category_id);
        }

        $subcategories = $query->ordered()->paginate(15);
        $categories = BlogCategory::active()->ordered()->get();

        $data = [
            'pageTitle' => 'Gerenciar Subcategorias do Blog',
            'pageDescription' => 'Lista de todas as subcategorias do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Subcategorias', 'url' => route('admin.blog-subcategories.index')]
            ],
            'subcategories' => $subcategories,
            'categories' => $categories,
            'search' => $request->search,
            'status' => $request->status,
            'category_id' => $request->category_id
        ];

        return view('admin.blog.subcategories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = BlogCategory::active()->ordered()->get();

        $data = [
            'pageTitle' => 'Nova Subcategoria do Blog',
            'pageDescription' => 'Criar uma nova subcategoria para o blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Subcategorias', 'url' => route('admin.blog-subcategories.index')],
                ['title' => 'Nova Subcategoria', 'url' => route('admin.blog-subcategories.create')]
            ],
            'categories' => $categories,
            'selectedCategoryId' => $request->get('category_id')
        ];

        return view('admin.blog.subcategories.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|in:pt,en,es',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Generate slug from name
        $data['slug'] = BlogSubcategory::generateSlug($data['name']);
        $data['is_active'] = $request->has('is_active');

        BlogSubcategory::create($data);

        return redirect()->route('admin.blog-subcategories.index')
            ->with('success', 'Subcategoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogSubcategory $blogSubcategory)
    {
        $blogSubcategory->load('category');

        $data = [
            'pageTitle' => 'Detalhes da Subcategoria',
            'pageDescription' => 'Visualizar detalhes da subcategoria do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Subcategorias', 'url' => route('admin.blog-subcategories.index')],
                ['title' => 'Detalhes', 'url' => route('admin.blog-subcategories.show', $blogSubcategory)]
            ],
            'subcategory' => $blogSubcategory
        ];

        return view('admin.blog.subcategories.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogSubcategory $blogSubcategory)
    {
        $categories = BlogCategory::active()->ordered()->get();
        $blogSubcategory->load('category');

        $data = [
            'pageTitle' => 'Editar Subcategoria',
            'pageDescription' => 'Editar subcategoria do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Subcategorias', 'url' => route('admin.blog-subcategories.index')],
                ['title' => 'Editar', 'url' => route('admin.blog-subcategories.edit', $blogSubcategory)]
            ],
            'subcategory' => $blogSubcategory,
            'categories' => $categories
        ];

        return view('admin.blog.subcategories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogSubcategory $blogSubcategory)
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|in:pt,en,es',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Generate new slug if name changed
        if ($data['name'] !== $blogSubcategory->name) {
            $data['slug'] = BlogSubcategory::generateSlug($data['name']);
        }
        
        $data['is_active'] = $request->has('is_active');

        $blogSubcategory->update($data);

        return redirect()->route('admin.blog-subcategories.index')
            ->with('success', 'Subcategoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogSubcategory $blogSubcategory)
    {
        $blogSubcategory->delete();

        return redirect()->route('admin.blog-subcategories.index')
            ->with('success', 'Subcategoria excluída com sucesso!');
    }
}
