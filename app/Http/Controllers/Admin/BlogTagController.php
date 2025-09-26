<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogTag::query();

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

        // Language filter
        if ($request->has('language') && $request->language) {
            $query->where('language', $request->language);
        }

        $tags = $query->ordered()->paginate(15);

        $data = [
            'pageTitle' => 'Gerenciar Tags do Blog',
            'pageDescription' => 'Lista de todas as tags do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Tags', 'url' => route('admin.blog-tags.index')]
            ],
            'tags' => $tags,
            'search' => $request->search,
            'status' => $request->status,
            'language' => $request->language
        ];

        return view('admin.blog.tags.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Nova Tag do Blog',
            'pageDescription' => 'Criar uma nova tag para o blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Tags', 'url' => route('admin.blog-tags.index')],
                ['title' => 'Nova Tag', 'url' => route('admin.blog-tags.create')]
            ]
        ];

        return view('admin.blog.tags.create', $data);
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
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Generate slug from name
        $data['slug'] = BlogTag::generateSlug($data['name']);
        $data['is_active'] = $request->has('is_active');
        
        // Set random color if not provided
        if (empty($data['color'])) {
            $data['color'] = BlogTag::getRandomColor();
        }

        BlogTag::create($data);

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Tag criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogTag $blogTag)
    {
        $data = [
            'pageTitle' => 'Detalhes da Tag',
            'pageDescription' => 'Visualizar detalhes da tag do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Tags', 'url' => route('admin.blog-tags.index')],
                ['title' => 'Detalhes', 'url' => route('admin.blog-tags.show', $blogTag)]
            ],
            'tag' => $blogTag
        ];

        return view('admin.blog.tags.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogTag $blogTag)
    {
        $data = [
            'pageTitle' => 'Editar Tag',
            'pageDescription' => 'Editar tag do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Tags', 'url' => route('admin.blog-tags.index')],
                ['title' => 'Editar', 'url' => route('admin.blog-tags.edit', $blogTag)]
            ],
            'tag' => $blogTag
        ];

        return view('admin.blog.tags.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogTag $blogTag)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|in:pt,en,es',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Generate new slug if name changed
        if ($data['name'] !== $blogTag->name) {
            $data['slug'] = BlogTag::generateSlug($data['name']);
        }
        
        $data['is_active'] = $request->has('is_active');
        
        // Keep current color if not provided
        if (empty($data['color'])) {
            $data['color'] = $blogTag->color;
        }

        $blogTag->update($data);

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Tag atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogTag $blogTag)
    {
        $blogTag->delete();

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Tag excluída com sucesso!');
    }
}
