<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogVideoRequest;
use App\Models\BlogVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogVideo::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
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

        $videos = $query->ordered()->paginate(20);

        $data = [
            'pageTitle' => 'Gerenciar Vídeos do Blog',
            'pageDescription' => 'Lista de todos os vídeos do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Vídeos', 'url' => route('admin.blog-videos.index')]
            ],
            'videos' => $videos,
            'search' => $request->search,
            'status' => $request->status,
            'language' => $request->language
        ];

        return view('admin.blog.videos.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Novo Vídeo do Blog',
            'pageDescription' => 'Fazer upload de um novo vídeo para o blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Vídeos', 'url' => route('admin.blog-videos.index')],
                ['title' => 'Novo Vídeo', 'url' => route('admin.blog-videos.create')]
            ]
        ];

        return view('admin.blog.videos.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogVideoRequest $request)
    {
        // Validation is handled by BlogVideoRequest
        $validated = $request->validated();
        
        if (!$request->hasFile('video')) {
            return redirect()->back()
                ->with('error', 'Por favor, selecione um vídeo.')
                ->withInput();
        }

        try {
            $file = $request->file('video');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . str_replace(' ', '_', $originalFilename);
            
            // Store the file using config path
            $path = $file->storeAs(config('blog.videos.path'), $filename, config('blog.videos.disk'));
            
            // Get video dimensions (skip for testing with fake files)
            $width = null;
            $height = null;
            $duration = null;
            
            $fullPath = storage_path('app/' . config('blog.videos.disk') . '/' . $path);
            if (file_exists($fullPath)) {
                // For videos, we would need FFmpeg or similar to get dimensions and duration
                // For now, we'll set default values or null for testing
                $width = null;
                $height = null;
                $duration = null;
            }

            $data = [
                'title' => $validated['title'],
                'slug' => BlogVideo::generateSlug($validated['title']),
                'description' => $validated['description'] ?? null,
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'duration' => $duration,
                'thumbnail_path' => null, // Will be handled separately if thumbnail is uploaded
                'language' => $validated['language'],
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 1
            ];

            // Handle thumbnail upload if provided
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailFilename = time() . '_thumb_' . str_replace(' ', '_', $thumbnailFile->getClientOriginalName());
                $thumbnailPath = $thumbnailFile->storeAs(config('blog.videos.thumbnail_path'), $thumbnailFilename, config('blog.videos.disk'));
                $data['thumbnail_path'] = $thumbnailPath;
            }

            BlogVideo::create($data);

            return redirect()->route('admin.blog-videos.index')
                ->with('success', 'Vídeo criado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao fazer upload do vídeo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogVideo $blogVideo)
    {
        $data = [
            'pageTitle' => 'Detalhes do Vídeo: ' . $blogVideo->title,
            'pageDescription' => 'Visualizar detalhes do vídeo do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Vídeos', 'url' => route('admin.blog-videos.index')],
                ['title' => $blogVideo->title, 'url' => route('admin.blog-videos.show', $blogVideo)]
            ],
            'video' => $blogVideo
        ];

        return view('admin.blog.videos.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogVideo $blogVideo)
    {
        $data = [
            'pageTitle' => 'Editar Vídeo: ' . $blogVideo->title,
            'pageDescription' => 'Editar informações do vídeo do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Blog', 'url' => '#'],
                ['title' => 'Vídeos', 'url' => route('admin.blog-videos.index')],
                ['title' => 'Editar', 'url' => route('admin.blog-videos.edit', $blogVideo)]
            ],
            'video' => $blogVideo
        ];

        return view('admin.blog.videos.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogVideoRequest $request, BlogVideo $blogVideo)
    {
        // Validation is handled by BlogVideoRequest
        $validated = $request->validated();

        try {
            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'language' => $validated['language'],
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 1
            ];

            // Generate new slug if title changed
            if ($data['title'] !== $blogVideo->title) {
                $data['slug'] = BlogVideo::generateSlug($data['title']);
            }

            // Handle new video upload
            if (isset($validated['video'])) {
                // Delete old video
                if (Storage::disk(config('blog.videos.disk'))->exists($blogVideo->path)) {
                    Storage::disk(config('blog.videos.disk'))->delete($blogVideo->path);
                }

                // Delete old thumbnail if exists
                if ($blogVideo->thumbnail_path && Storage::disk(config('blog.videos.disk'))->exists($blogVideo->thumbnail_path)) {
                    Storage::disk(config('blog.videos.disk'))->delete($blogVideo->thumbnail_path);
                }

                $file = $validated['video'];
                $originalFilename = $file->getClientOriginalName();
                $filename = time() . '_' . str_replace(' ', '_', $originalFilename);
                
                // Store the new file
                $path = $file->storeAs(config('blog.videos.path'), $filename, config('blog.videos.disk'));
                
                // Get video dimensions (skip for testing with fake files)
                $width = null;
                $height = null;
                $duration = null;
                
                $fullPath = storage_path('app/' . config('blog.videos.disk') . '/' . $path);
                if (file_exists($fullPath)) {
                    // For videos, we would need FFmpeg or similar to get dimensions and duration
                    // For now, we'll set default values or null for testing
                    $width = null;
                    $height = null;
                    $duration = null;
                }

                $data = array_merge($data, [
                    'filename' => $filename,
                    'original_filename' => $originalFilename,
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'width' => $width,
                    'height' => $height,
                    'duration' => $duration,
                    'thumbnail_path' => null, // Reset thumbnail path
                ]);
            }

            // Handle new thumbnail upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists and no new video was uploaded
                if (!isset($validated['video']) && $blogVideo->thumbnail_path && Storage::disk(config('blog.videos.disk'))->exists($blogVideo->thumbnail_path)) {
                    Storage::disk(config('blog.videos.disk'))->delete($blogVideo->thumbnail_path);
                }

                $thumbnailFile = $request->file('thumbnail');
                $thumbnailFilename = time() . '_thumb_' . str_replace(' ', '_', $thumbnailFile->getClientOriginalName());
                $thumbnailPath = $thumbnailFile->storeAs(config('blog.videos.thumbnail_path'), $thumbnailFilename, config('blog.videos.disk'));
                $data['thumbnail_path'] = $thumbnailPath;
            }

            $blogVideo->update($data);

            return redirect()->route('admin.blog-videos.index')
                ->with('success', 'Vídeo atualizado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar o vídeo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogVideo $blogVideo)
    {
        try {
            // Delete the video file from storage
            if (Storage::disk(config('blog.videos.disk'))->exists($blogVideo->path)) {
                Storage::disk(config('blog.videos.disk'))->delete($blogVideo->path);
            }

            // Delete the thumbnail file from storage
            if ($blogVideo->thumbnail_path && Storage::disk(config('blog.videos.disk'))->exists($blogVideo->thumbnail_path)) {
                Storage::disk(config('blog.videos.disk'))->delete($blogVideo->thumbnail_path);
            }

            $blogVideo->delete();

            return redirect()->route('admin.blog-videos.index')
                ->with('success', 'Vídeo excluído com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir o vídeo: ' . $e->getMessage());
        }
    }
}
