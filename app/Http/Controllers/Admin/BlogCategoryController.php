<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogCategory::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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

        $categories = $query->ordered()->paginate(15);

        $data = [
            'pageTitle' => 'Gerenciar Categorias do Blog',
            'pageDescription' => 'Lista de todas as categorias do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Categorias', 'url' => route('admin.blog-categories.index')]
            ],
            'categories' => $categories,
            'search' => $request->search,
            'status' => $request->status
        ];

        return view('admin.blog.categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Nova Categoria do Blog',
            'pageDescription' => 'Criar uma nova categoria para o blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Categorias', 'url' => route('admin.blog-categories.index')],
                ['title' => 'Nova Categoria', 'url' => route('admin.blog-categories.create')]
            ]
        ];

        return view('admin.blog.categories.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
        $data['slug'] = BlogCategory::generateSlug($data['name']);
        $data['is_active'] = $request->has('is_active');

        BlogCategory::create($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogCategory $blogCategory)
    {
        $data = [
            'pageTitle' => 'Detalhes da Categoria',
            'pageDescription' => 'Visualizar detalhes da categoria do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Categorias', 'url' => route('admin.blog-categories.index')],
                ['title' => 'Detalhes', 'url' => route('admin.blog-categories.show', $blogCategory)]
            ],
            'category' => $blogCategory
        ];

        return view('admin.blog.categories.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogCategory $blogCategory)
    {
        $data = [
            'pageTitle' => 'Editar Categoria',
            'pageDescription' => 'Editar categoria do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Categorias', 'url' => route('admin.blog-categories.index')],
                ['title' => 'Editar', 'url' => route('admin.blog-categories.edit', $blogCategory)]
            ],
            'category' => $blogCategory
        ];

        return view('admin.blog.categories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validator = Validator::make($request->all(), [
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
        if ($data['name'] !== $blogCategory->name) {
            $data['slug'] = BlogCategory::generateSlug($data['name']);
        }
        
        $data['is_active'] = $request->has('is_active');

        $blogCategory->update($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogCategory $blogCategory)
    {
        // Check if category has subcategories
        if ($blogCategory->subcategories()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir uma categoria que possui subcategorias.');
        }

        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
