@extends('layouts.app')

@section('title', 'Example Page - José Rafael Camejo')
@section('description', 'This is an example page demonstrating the template usage')

@push('styles')
    <!-- Page specific CSS -->
    <style>
        .example-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .example-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
@endpush

@section('content')
    <section class="example-section">
        <div class="container">
            <div class="example-content">
                <h1>Example Page</h1>
                <p>This is an example page that demonstrates how to use the base template.</p>
                <p>You can create new pages by extending the <code>layouts.app</code> template and defining your content in the <code>@section('content')</code> block.</p>
                
                <h2>Template Features:</h2>
                <ul style="text-align: left; display: inline-block;">
                    <li>Consistent header and footer across all pages</li>
                    <li>SEO-friendly title and meta description sections</li>
                    <li>Stackable CSS and JavaScript sections</li>
                    <li>Vue.js component support</li>
                    <li>Responsive design with modern styling</li>
                </ul>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <!-- Page specific JavaScript -->
    <script>
        console.log('Example page loaded successfully!');
    </script>
@endpush