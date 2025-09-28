<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogImageRequest;
use App\Models\BlogImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class BlogImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogImage::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%")
                  ->orWhere('original_filename', 'like', "%{$search}%");
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

        $images = $query->ordered()->paginate(20);

        $data = [
            'pageTitle' => 'Gerenciar Imagens do Blog',
            'pageDescription' => 'Lista de todas as imagens do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Imagens', 'url' => route('admin.blog-images.index')]
            ],
            'images' => $images,
            'search' => $request->search,
            'status' => $request->status,
            'language' => $request->language
        ];

        return view('admin.blog.images.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Nova Imagem do Blog',
            'pageDescription' => 'Fazer upload de uma nova imagem para o blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Imagens', 'url' => route('admin.blog-images.index')],
                ['title' => 'Nova Imagem', 'url' => route('admin.blog-images.create')]
            ]
        ];

        return view('admin.blog.images.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogImageRequest $request)
    {
        // Validation is handled by BlogImageRequest
        $validated = $request->validated();
        
        if (!$request->hasFile('image')) {
            return redirect()->back()
                ->with('error', 'Por favor, selecione uma imagem.')
                ->withInput();
        }

        try {
            $file = $request->file('image');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . str_replace(' ', '_', $originalFilename);
            
            // Store the file using config path
            $path = $file->storeAs(config('blog.images.path'), $filename, config('blog.images.disk'));
            
            // Get image dimensions (skip for testing with fake files)
            $width = null;
            $height = null;
            
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                $imageInfo = getimagesize($fullPath);
                $width = $imageInfo ? $imageInfo[0] : null;
                $height = $imageInfo ? $imageInfo[1] : null;
            }

            $data = [
                'title' => $validated['title'],
                'slug' => BlogImage::generateSlug($validated['title']),
                'description' => $validated['description'] ?? null,
                'alt_text' => $validated['alt_text'] ?: $validated['title'],
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'language' => $validated['language'],
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 1
            ];

            BlogImage::create($data);

            return redirect()->route('admin.blog-images.index')
                ->with('success', 'Imagem criada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao fazer upload da imagem: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogImage $blogImage)
    {
        $data = [
            'pageTitle' => 'Detalhes da Imagem: ' . $blogImage->title,
            'pageDescription' => 'Visualizar detalhes da imagem do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Imagens', 'url' => route('admin.blog-images.index')],
                ['title' => $blogImage->title, 'url' => route('admin.blog-images.show', $blogImage)]
            ],
            'image' => $blogImage
        ];

        return view('admin.blog.images.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogImage $blogImage)
    {
        $data = [
            'pageTitle' => 'Editar Imagem: ' . $blogImage->title,
            'pageDescription' => 'Editar informações da imagem do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Imagens', 'url' => route('admin.blog-images.index')],
                ['title' => 'Editar', 'url' => route('admin.blog-images.edit', $blogImage)]
            ],
            'image' => $blogImage
        ];

        return view('admin.blog.images.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogImageRequest $request, BlogImage $blogImage)
    {
        // Validation is handled by BlogImageRequest
        $validated = $request->validated();

        try {
            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'alt_text' => $validated['alt_text'] ?: $validated['title'],
                'language' => $validated['language'],
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 1
            ];

            // Generate new slug if title changed
            if ($data['title'] !== $blogImage->title) {
                $data['slug'] = BlogImage::generateSlug($data['title']);
            }

            // Handle new image upload
            if (isset($validated['image'])) {
                // Delete old image
                if (Storage::disk(config('blog.images.disk'))->exists($blogImage->path)) {
                    Storage::disk(config('blog.images.disk'))->delete($blogImage->path);
                }

                $file = $validated['image'];
                $originalFilename = $file->getClientOriginalName();
                $filename = time() . '_' . str_replace(' ', '_', $originalFilename);
                
                // Store the new file
                $path = $file->storeAs(config('blog.images.path'), $filename, config('blog.images.disk'));
                
                // Get image dimensions (skip for testing with fake files)
                $width = null;
                $height = null;
                
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    $imageInfo = getimagesize($fullPath);
                    $width = $imageInfo ? $imageInfo[0] : null;
                    $height = $imageInfo ? $imageInfo[1] : null;
                }

                $data = array_merge($data, [
                    'filename' => $filename,
                    'original_filename' => $originalFilename,
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'width' => $width,
                    'height' => $height,
                ]);
            }

            $blogImage->update($data);

            return redirect()->route('admin.blog-images.index')
                ->with('success', 'Imagem atualizada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar a imagem: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogImage $blogImage)
    {
        try {
            // Delete the file from storage
            if (Storage::disk(config('blog.images.disk'))->exists($blogImage->path)) {
                Storage::disk(config('blog.images.disk'))->delete($blogImage->path);
            }

            $blogImage->delete();

            return redirect()->route('admin.blog-images.index')
                ->with('success', 'Imagem excluída com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir a imagem: ' . $e->getMessage());
        }
    }
}
