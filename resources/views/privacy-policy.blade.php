@extends('layouts.privacy')

@section('title', __('messages.privacy_policy_title') . ' - José Rafael Camejo')
@section('description', __('messages.privacy_policy_description'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/privacy-policy.css') }}">
@endpush

@section('content')
<div class="privacy-policy">
    <h1>{{ __('messages.privacy_policy_title') }}</h1>
    
    <div class="last-updated">
        <p>{{ __('messages.last_updated') }}: {{ date('d/m/Y') }}</p>
    </div>
    
    <h2>1. {{ __('messages.introduction') }}</h2>
    <p>{{ __('messages.introduction_text') }}</p>
    
    <h2>2. {{ __('messages.information_collection') }}</h2>
    <p>{{ __('messages.information_collection_text') }}</p>
    <ul>
        <li>{{ __('messages.personal_info') }}</li>
        <li>{{ __('messages.message_content') }}</li>
        <li>{{ __('messages.technical_info') }}</li>
    </ul>
    
    <h2>3. {{ __('messages.information_use') }}</h2>
    <p>{{ __('messages.information_use_text') }}</p>
    <ul>
        <li>{{ __('messages.respond_messages') }}</li>
        <li>{{ __('messages.improve_services') }}</li>
        <li>{{ __('messages.comply_legal') }}</li>
    </ul>
    
    <h2>4. {{ __('messages.information_sharing') }}</h2>
    <p>{{ __('messages.information_sharing_text') }}</p>
    <ul>
        <li>{{ __('messages.legal_compliance') }}</li>
        <li>{{ __('messages.service_providers') }}</li>
    </ul>
    
    <h2>5. {{ __('messages.data_security') }}</h2>
    <p>{{ __('messages.data_security_text') }}</p>
    
    <h2>6. {{ __('messages.cookies') }}</h2>
    <p>{{ __('messages.cookies_text') }}</p>
    
    <h2>7. {{ __('messages.your_rights') }}</h2>
    <p>{{ __('messages.your_rights_text') }}</p>
    <ul>
        <li>{{ __('messages.access_data') }}</li>
        <li>{{ __('messages.correct_data') }}</li>
        <li>{{ __('messages.delete_data') }}</li>
        <li>{{ __('messages.data_portability') }}</li>
    </ul>
    
    <h2>8. {{ __('messages.contact_info') }}</h2>
    <div class="contact-info">
        <p>{{ __('messages.contact_info_text') }}</p>
        <p>
            <strong>{{ __('messages.email') }}:</strong> contato@josecamejo.com.br<br>
            <strong>{{ __('messages.website') }}:</strong> <a href="{{ route('home') }}">josecamejo.com.br</a>
        </p>
    </div>
    
    <h2>9. {{ __('messages.changes_policy') }}</h2>
    <p>{{ __('messages.changes_policy_text') }}</p>
</div>
@endsection