@extends('layouts.admin')

@section('title', 'Edit Import Template - ' . $template->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <!-- Enhanced Header with Breadcrumb -->
        <div class="bg-white shadow-sm border-b">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <!-- Breadcrumb Navigation -->
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('outbound.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('outbound.imports.dashboard') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Imports</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('outbound.imports.templates') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Templates</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-1 text-gray-800 font-medium md:ml-2">{{ $template->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                @if($template->file_type === 'csv')
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif($template->file_type === 'xlsx')
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $template->name }}</h1>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="text-sm text-gray-600">{{ $template->source_system }}</span>
                                <span class="text-gray-400">•</span>
                                @if($template->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Inactive
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ strtoupper($template->file_type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <form method="POST" action="{{ route('outbound.imports.templates.toggle', $template) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                @if($template->is_active)
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Deactivate
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Activate
                                @endif
                            </button>
                        </form>
                        <a href="{{ route('outbound.imports.templates') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back to Templates
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress/Navigation Bar -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="px-4 sm:px-6 lg:px-8">
                <nav class="flex space-x-8 py-4" aria-label="Tabs">
                    <a href="#overview" class="tab-link text-blue-600 border-blue-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Overview
                    </a>
                    @if($availableHeaders->isNotEmpty() || $previewData)
                    <a href="#preview" class="tab-link text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Data Preview
                    </a>
                    @endif
                    <a href="#mapping" class="tab-link text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Field Mapping
                        @php
                        $requiredFields = ['load_reference', 'order_number'];
                        $currentMapping = old('column_mapping', $template->column_mapping ?? []);
                        $missingRequired = collect($requiredFields)->filter(fn($field) => empty($currentMapping[$field]));
                        @endphp
                        @if($missingRequired->isNotEmpty())
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $missingRequired->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="#settings" class="tab-link text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">

            <!-- Overview Tab -->
            <div id="overview" class="tab-content">
                <!-- Template Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-gray-900">{{ $template->files_processed ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Files Processed</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-gray-900">{{ count($template->column_mapping ?? []) }}</div>
                                <div class="text-sm text-gray-500">Mapped Fields</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $template->last_used_at ? $template->last_used_at->diffForHumans() : 'Never' }}
                                </div>
                                <div class="text-sm text-gray-500">Last Used</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $template->created_at->diffForHumans() }}
                                </div>
                                <div class="text-sm text-gray-500">Created</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Template Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Template Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $template->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Source System</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $template->source_system }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">File Type</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ strtoupper($template->file_type) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Processing Mode</dt>
                                <dd class="mt-1">
                                    @if($template->auto_process)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Auto-process
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Manual review
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Duplicate Handling</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $template->duplicate_handling) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @if($template->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Inactive
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if($template->description)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                                <dd class="text-sm text-gray-900">{{ $template->description }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Data Preview Tab -->
            @if($availableHeaders->isNotEmpty() || $previewData)
            <div id="preview" class="tab-content hidden">
                <!-- Available Headers -->
                @if($availableHeaders->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Available Column Headers</h2>
                        <p class="text-sm text-gray-600">Headers found in recent uploads using this template</p>
                        @if($recentUploads->count() > 1)
                            <p class="text-xs text-gray-500 mt-1">Based on {{ $recentUploads->count() }} recent files</p>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($availableHeaders as $header)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors border border-transparent hover:border-blue-200" 
                                     onclick="copyToClipboard('{{ addslashes($header) }}')">
                                    <span class="text-sm font-mono text-gray-800 flex-1">{{ $header }}</span>
                                    <svg class="w-4 h-4 text-gray-400 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-900">Quick Copy Headers</h4>
                                    <p class="text-sm text-blue-700 mt-1">Click any header above to copy it to your clipboard, then paste it into the mapping fields in the "Field Mapping" tab.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Required Fields Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Required Field Status</h2>
                        <p class="text-sm text-gray-600">Essential fields that must be mapped for successful processing</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                            $requiredFields = [
                                'load_reference' => 'Load Reference',
                                'order_number' => 'Order Number'
                            ];
                            $currentMapping = old('column_mapping', $template->column_mapping ?? []);
                            @endphp
                            @foreach($requiredFields as $key => $label)
                                <div class="flex items-center justify-between p-4 {{ !empty($currentMapping[$key]) ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-lg">
                                    <div class="flex items-center">
                                        @if(!empty($currentMapping[$key]))
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                        <div>
                                            <h4 class="font-medium {{ !empty($currentMapping[$key]) ? 'text-green-900' : 'text-red-900' }}">{{ $label }}</h4>
                                            <p class="text-sm {{ !empty($currentMapping[$key]) ? 'text-green-700' : 'text-red-700' }}">
                                                @if(!empty($currentMapping[$key]))
                                                    Mapped to: <span class="font-mono bg-white px-2 py-1 rounded border">{{ $currentMapping[$key] }}</span>
                                                @else
                                                    Not mapped - this field is required
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if(!empty($currentMapping[$key]))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Complete
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Missing
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>

                <!-- File Preview -->
                @if($previewData && $selectedUpload)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Sample Data Preview</h2>
                                <p class="text-sm text-gray-600">
                                    Sample from: <strong>{{ $selectedUpload->original_filename }}</strong> 
                                    (uploaded {{ $selectedUpload->uploaded_at->diffForHumans() }})
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ min(count($previewData), 100) }} records
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @php $headers = array_keys($previewData[0] ?? []); @endphp
                                        @foreach($headers as $header)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200 last:border-r-0">
                                                <div class="flex items-center space-x-1">
                                                    <span>{{ $header }}</span>
                                                    <button onclick="copyToClipboard('{{ addslashes($header) }}')" 
                                                            class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-blue-600 transition-all">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach(array_slice($previewData, 0, 10) as $index => $row)
                                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 group">
                                            @foreach($headers as $header)
                                                <td class="px-4 py-3 whitespace-nowrap text-gray-900 border-r border-gray-200 last:border-r-0 max-w-xs truncate">
                                                    <span title="{{ $row[$header] ?? '' }}">{{ $row[$header] ?? '' }}</span>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(count($previewData) > 10)
                                <div class="mt-4 text-center p-4 bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200">
                                    <p class="text-sm text-gray-600">
                                        📊 Showing first 10 rows of {{ count($previewData) }} total preview records
                                        @if(count($previewData) >= 100)
                                            (limited to 100 for performance)
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Field Mapping Tab -->
            <div id="mapping" class="tab-content hidden">
                <form method="POST" action="{{ route('outbound.imports.templates.update', $template) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Quick Mapping Tools -->
                    @if($availableHeaders->isNotEmpty())
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6 mb-8">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900 mb-2">Quick Mapping Assistant</h3>
                                <p class="text-blue-700 mb-4">Available headers from your uploaded files. Click to copy and paste into mapping fields below.</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($availableHeaders->take(8) as $header)
                                        <button type="button" 
                                                onclick="copyToClipboard('{{ addslashes($header) }}')"
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white text-blue-800 border border-blue-200 hover:bg-blue-50 transition-colors">
                                            <span class="font-mono">{{ $header }}</span>
                                            <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    @endforeach
                                    @if($availableHeaders->count() > 8)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200">
                                            +{{ $availableHeaders->count() - 8 }} more in Data Preview tab
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                    <p class="text-sm text-gray-600">Update template details and settings</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $template->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="source_system" class="block text-sm font-medium text-gray-700 mb-2">Source System</label>
                        <input type="text" 
                               id="source_system" 
                               name="source_system" 
                               value="{{ $template->source_system }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">Source system cannot be changed after creation</p>
                    </div>

                    <div>
                        <label for="file_type" class="block text-sm font-medium text-gray-700 mb-2">File Type</label>
                        <input type="text" 
                               value="{{ strtoupper($template->file_type) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">File type cannot be changed after creation</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Template is active</label>
                        <span class="text-xs text-gray-500">(Inactive templates cannot be used for new imports)</span>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('description', $template->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- File Format Settings -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">File Format Settings</h2>
                    <p class="text-sm text-gray-600">Configure how the file should be parsed</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @if(in_array($template->file_type, ['csv', 'txt']))
                            <div>
                                <label for="delimiter" class="block text-sm font-medium text-gray-700 mb-2">Column Delimiter</label>
                                <select id="delimiter" 
                                        name="delimiter" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="," {{ old('delimiter', $template->delimiter) === ',' ? 'selected' : '' }}>Comma (,)</option>
                                    <option value=";" {{ old('delimiter', $template->delimiter) === ';' ? 'selected' : '' }}>Semicolon (;)</option>
                                    <option value="|" {{ old('delimiter', $template->delimiter) === '|' ? 'selected' : '' }}>Pipe (|)</option>
                                    <option value="\t" {{ old('delimiter', $template->delimiter) === "\t" ? 'selected' : '' }}>Tab</option>
                                </select>
                            </div>

                            <div>
                                <label for="text_qualifier" class="block text-sm font-medium text-gray-700 mb-2">Text Qualifier</label>
                                <select id="text_qualifier" 
                                        name="text_qualifier" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="\"" {{ old('text_qualifier', $template->text_qualifier) === '"' ? 'selected' : '' }}>Double Quote (")</option>
                                    <option value="'" {{ old('text_qualifier', $template->text_qualifier) === "'" ? 'selected' : '' }}>Single Quote (')</option>
                                    <option value="" {{ old('text_qualifier', $template->text_qualifier) === '' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        @endif

                        <div>
                            <label for="header_row" class="block text-sm font-medium text-gray-700 mb-2">Header Row</label>
                            <input type="number" 
                                   id="header_row" 
                                   name="header_row" 
                                   value="{{ old('header_row', $template->header_row) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Set to 0 if no header row</p>
                        </div>

                        <div>
                            <label for="data_start_row" class="block text-sm font-medium text-gray-700 mb-2">Data Start Row</label>
                            <input type="number" 
                                   id="data_start_row" 
                                   name="data_start_row" 
                                   value="{{ old('data_start_row', $template->data_start_row) }}"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Row where actual data begins</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="encoding" class="block text-sm font-medium text-gray-700 mb-2">File Encoding</label>
                            <select id="encoding" 
                                    name="encoding" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="UTF-8" {{ old('encoding', $template->encoding) === 'UTF-8' ? 'selected' : '' }}>UTF-8</option>
                                <option value="ISO-8859-1" {{ old('encoding', $template->encoding) === 'ISO-8859-1' ? 'selected' : '' }}>ISO-8859-1 (Latin-1)</option>
                                <option value="Windows-1252" {{ old('encoding', $template->encoding) === 'Windows-1252' ? 'selected' : '' }}>Windows-1252</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Headers and File Preview -->
            @if($availableHeaders->isNotEmpty() || $previewData)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Available Headers -->
                @if($availableHeaders->isNotEmpty())
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Available Column Headers</h2>
                        <p class="text-sm text-gray-600">Headers found in recent uploads using this template</p>
                        @if($recentUploads->count() > 1)
                            <p class="text-xs text-gray-500 mt-1">Based on {{ $recentUploads->count() }} recent files</p>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($availableHeaders as $header)
                                <div class="flex items-center p-2 bg-gray-50 rounded-md cursor-pointer hover:bg-gray-100" 
                                     onclick="copyToClipboard('{{ addslashes($header) }}')">
                                    <span class="text-sm font-mono text-gray-800 flex-1">{{ $header }}</span>
                                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700">
                                💡 <strong>Tip:</strong> Click any header to copy it, then paste into the mapping fields below.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Required Fields Checklist -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Required Field Mapping</h2>
                        <p class="text-sm text-gray-600">Essential fields that must be mapped for successful processing</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @php
                            $requiredFields = [
                                'load_reference' => 'Load Reference',
                                'order_number' => 'Order Number'
                            ];
                            $currentMapping = old('column_mapping', $template->column_mapping ?? []);
                            @endphp
                            @foreach($requiredFields as $key => $label)
                                <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-red-900">{{ $label }}</h4>
                                        <p class="text-sm text-red-700">Currently mapped to: 
                                            @if(!empty($currentMapping[$key]))
                                                <span class="font-mono bg-red-100 px-2 py-1 rounded">{{ $currentMapping[$key] }}</span>
                                            @else
                                                <span class="text-red-500 italic">Not mapped</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if(!empty($currentMapping[$key]))
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- File Preview -->
            @if($previewData && $selectedUpload)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Sample Data Preview</h2>
                    <p class="text-sm text-gray-600">
                        Sample from: <strong>{{ $selectedUpload->original_filename }}</strong> 
                        (uploaded {{ $selectedUpload->uploaded_at->diffForHumans() }})
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Showing first {{ min(count($previewData), 100) }} records</p>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @php $headers = array_keys($previewData[0] ?? []); @endphp
                                    @foreach($headers as $header)
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(array_slice($previewData, 0, 10) as $index => $row)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        @foreach($headers as $header)
                                            <td class="px-3 py-2 whitespace-nowrap text-gray-900">
                                                {{ $row[$header] ?? '' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($previewData) > 10)
                            <div class="mt-4 text-center p-3 bg-gray-50 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    Showing first 10 rows of {{ count($previewData) }} total preview records
                                    @if(count($previewData) >= 100)
                                        (limited to 100 for performance)
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Column Mapping -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Column Mapping</h2>
                    <p class="text-sm text-gray-600">Update field mappings. Use column names from your file or column numbers (A, B, C...)</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @php
                        $standardFields = [
                            'load_reference' => 'Load Reference',
                            'order_number' => 'Order Number',
                            'customer_reference' => 'Customer Reference',
                            'delivery_date' => 'Delivery Date',
                            'delivery_time' => 'Delivery Time',
                            'delivery_address' => 'Delivery Address',
                            'delivery_postcode' => 'Delivery Postcode',
                            'delivery_city' => 'Delivery City',
                            'product_code' => 'Product Code',
                            'quantity' => 'Quantity',
                            'weight' => 'Weight',
                            'volume' => 'Volume',
                            'special_instructions' => 'Special Instructions',
                            'priority' => 'Priority Level',
                            'carrier' => 'Carrier',
                            'driver_name' => 'Driver Name',
                            'driver_phone' => 'Driver Phone',
                            'vehicle_registration' => 'Vehicle Registration',
                            'trailer_reference' => 'Trailer Reference'
                        ];
                        $currentMapping = old('column_mapping', $template->column_mapping ?? []);
                        @endphp

                        @foreach($standardFields as $key => $label)
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 w-48">
                                    <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                                    @if(in_array($key, ['load_reference', 'order_number']))
                                        <span class="text-xs text-red-500">(Required)</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           name="column_mapping[{{ $key }}]" 
                                           value="{{ $currentMapping[$key] ?? '' }}"
                                           placeholder="Column name or number (e.g., 'Load_Ref' or 'A')"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div class="flex-shrink-0">
                                    @if(in_array($key, ['load_reference', 'order_number']))
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Required</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Optional</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Mapping Guidelines</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Changes to mappings will affect future file imports only</li>
                                        <li>Test your changes with a small sample file first</li>
                                        <li>Required fields must be mapped for successful processing</li>
                                        <li>Consider deactivating the template before making major changes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Options -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Processing Options</h2>
                    <p class="text-sm text-gray-600">Configure how files using this template should be processed</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               id="auto_process" 
                               name="auto_process" 
                               value="1"
                               {{ old('auto_process', $template->auto_process) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="auto_process" class="text-sm font-medium text-gray-700">Auto-process files</label>
                        <span class="text-xs text-gray-500">(If unchecked, files will require manual review before processing)</span>
                    </div>

                    <div>
                        <label for="duplicate_handling" class="block text-sm font-medium text-gray-700 mb-2">Duplicate Handling</label>
                        <select id="duplicate_handling" 
                                name="duplicate_handling" 
                                class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="skip" {{ old('duplicate_handling', $template->duplicate_handling) === 'skip' ? 'selected' : '' }}>Skip duplicates</option>
                            <option value="overwrite" {{ old('duplicate_handling', $template->duplicate_handling) === 'overwrite' ? 'selected' : '' }}>Overwrite existing</option>
                            <option value="create_new" {{ old('duplicate_handling', $template->duplicate_handling) === 'create_new' ? 'selected' : '' }}>Create new record</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">How to handle records with duplicate Load References</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between">
                <div>
                    @if($template->files_processed > 0)
                        <p class="text-sm text-gray-600">
                            ⚠️ This template has processed {{ $template->files_processed }} files. 
                            Changes will only affect future imports.
                        </p>
                    @endif
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('outbound.imports.templates') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                        Update Template
                    </button>
                </div>
            </div>
                </form>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content hidden">
                <form method="POST" action="{{ route('outbound.imports.templates.update', $template) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Template Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Template Settings</h2>
                            <p class="text-sm text-gray-600">Configure template behavior and processing options</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $template->name) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror" 
                                           required>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center space-x-3 pt-8">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Template is active</label>
                                    <div class="ml-2">
                                        @if($template->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Currently Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                Currently Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Optional description of this template and when to use it..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description', $template->description) }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">Help other users understand when and how to use this template</p>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Configuration -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Processing Configuration</h2>
                            <p class="text-sm text-gray-600">Configure how files using this template should be processed</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Auto-process setting -->
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200 p-6">
                                <div class="flex items-start">
                                    <input type="checkbox" 
                                           id="auto_process" 
                                           name="auto_process" 
                                           value="1"
                                           {{ old('auto_process', $template->auto_process) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mt-1">
                                    <div class="ml-4">
                                        <label for="auto_process" class="text-base font-medium text-gray-900 cursor-pointer">Auto-process files</label>
                                        <p class="text-sm text-gray-600 mt-1">
                                            When enabled, files will be processed immediately after upload without requiring manual review.
                                        </p>
                                        <div class="mt-3">
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="text-green-700">Recommended for trusted sources with consistent file formats</span>
                                            </div>
                                            <div class="flex items-center text-sm mt-1">
                                                <svg class="w-4 h-4 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                </svg>
                                                <span class="text-amber-700">If unchecked, files will require manual review before processing</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicate handling -->
                            <div>
                                <label for="duplicate_handling" class="block text-sm font-medium text-gray-700 mb-3">Duplicate Record Handling</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="relative">
                                        <input type="radio" 
                                               id="duplicate_skip" 
                                               name="duplicate_handling" 
                                               value="skip" 
                                               {{ old('duplicate_handling', $template->duplicate_handling) === 'skip' ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label for="duplicate_skip" class="ml-3 cursor-pointer">
                                            <div class="text-sm font-medium text-gray-900">Skip duplicates</div>
                                            <div class="text-sm text-gray-500">Ignore records with existing Load References</div>
                                        </label>
                                    </div>
                                    <div class="relative">
                                        <input type="radio" 
                                               id="duplicate_overwrite" 
                                               name="duplicate_handling" 
                                               value="overwrite" 
                                               {{ old('duplicate_handling', $template->duplicate_handling) === 'overwrite' ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label for="duplicate_overwrite" class="ml-3 cursor-pointer">
                                            <div class="text-sm font-medium text-gray-900">Overwrite existing</div>
                                            <div class="text-sm text-gray-500">Replace existing records with new data</div>
                                        </label>
                                    </div>
                                    <div class="relative">
                                        <input type="radio" 
                                               id="duplicate_create" 
                                               name="duplicate_handling" 
                                               value="create_new" 
                                               {{ old('duplicate_handling', $template->duplicate_handling) === 'create_new' ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label for="duplicate_create" class="ml-3 cursor-pointer">
                                            <div class="text-sm font-medium text-gray-900">Create new record</div>
                                            <div class="text-sm text-gray-500">Allow duplicate Load References</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Format Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">File Format Settings</h2>
                            <p class="text-sm text-gray-600">Configure how files should be parsed and read</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                @if(in_array($template->file_type, ['csv', 'txt']))
                                    <div>
                                        <label for="delimiter" class="block text-sm font-medium text-gray-700 mb-2">Column Delimiter</label>
                                        <select id="delimiter" 
                                                name="delimiter" 
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="," {{ old('delimiter', $template->delimiter) === ',' ? 'selected' : '' }}>Comma (,)</option>
                                            <option value=";" {{ old('delimiter', $template->delimiter) === ';' ? 'selected' : '' }}>Semicolon (;)</option>
                                            <option value="|" {{ old('delimiter', $template->delimiter) === '|' ? 'selected' : '' }}>Pipe (|)</option>
                                            <option value="\t" {{ old('delimiter', $template->delimiter) === "\t" ? 'selected' : '' }}>Tab</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="text_qualifier" class="block text-sm font-medium text-gray-700 mb-2">Text Qualifier</label>
                                        <select id="text_qualifier" 
                                                name="text_qualifier" 
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="\"" {{ old('text_qualifier', $template->text_qualifier) === '"' ? 'selected' : '' }}>Double Quote (")</option>
                                            <option value="'" {{ old('text_qualifier', $template->text_qualifier) === "'" ? 'selected' : '' }}>Single Quote (')</option>
                                            <option value="" {{ old('text_qualifier', $template->text_qualifier) === '' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                @endif

                                <div>
                                    <label for="header_row" class="block text-sm font-medium text-gray-700 mb-2">Header Row</label>
                                    <input type="number" 
                                           id="header_row" 
                                           name="header_row" 
                                           value="{{ old('header_row', $template->header_row) }}"
                                           min="0"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Row number containing column headers (0 if no headers)</p>
                                </div>

                                <div>
                                    <label for="data_start_row" class="block text-sm font-medium text-gray-700 mb-2">Data Start Row</label>
                                    <input type="number" 
                                           id="data_start_row" 
                                           name="data_start_row" 
                                           value="{{ old('data_start_row', $template->data_start_row) }}"
                                           min="1"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">First row containing actual data</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="encoding" class="block text-sm font-medium text-gray-700 mb-2">File Encoding</label>
                                    <select id="encoding" 
                                            name="encoding" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="UTF-8" {{ old('encoding', $template->encoding) === 'UTF-8' ? 'selected' : '' }}>UTF-8 (Recommended)</option>
                                        <option value="ISO-8859-1" {{ old('encoding', $template->encoding) === 'ISO-8859-1' ? 'selected' : '' }}>ISO-8859-1 (Latin-1)</option>
                                        <option value="Windows-1252" {{ old('encoding', $template->encoding) === 'Windows-1252' ? 'selected' : '' }}>Windows-1252</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                @if($template->files_processed > 0)
                                    <div class="flex items-center text-sm text-amber-700 bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        This template has processed {{ $template->files_processed }} files. Changes will only affect future imports.
                                    </div>
                                @endif
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('outbound.imports.templates') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Copy header name to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create a temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        notification.textContent = `Copied: ${text}`;
        document.body.appendChild(notification);
        
        // Remove notification after 2 seconds
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback - show alert
        alert(`Column header: ${text}`);
    });
}

// Auto-fill mapping fields when clicking on available headers
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to column mapping inputs for better UX
    const mappingInputs = document.querySelectorAll('input[name^="column_mapping"]');
    mappingInputs.forEach(input => {
        input.addEventListener('focus', function() {
            // Highlight available headers when focusing on a mapping input
            const headerElements = document.querySelectorAll('[onclick^="copyToClipboard"]');
            headerElements.forEach(el => {
                el.style.backgroundColor = '#dbeafe'; // light blue
            });
        });
        
        input.addEventListener('blur', function() {
            // Remove highlighting when losing focus
            const headerElements = document.querySelectorAll('[onclick^="copyToClipboard"]');
            headerElements.forEach(el => {
                el.style.backgroundColor = '#f9fafb'; // original gray
            });
        });
    });
    
    // Tab functionality
    const tabs = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active classes from all tabs
            tabs.forEach(t => {
                t.classList.remove('text-blue-600', 'border-blue-600');
                t.classList.add('text-gray-500', 'border-transparent');
            });
            
            // Add active class to clicked tab
            this.classList.remove('text-gray-500', 'border-transparent');
            this.classList.add('text-blue-600', 'border-blue-600');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target tab content
            const targetId = this.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
    
    // Handle URL hash for direct tab access
    function showTabFromHash() {
        const hash = window.location.hash.substring(1);
        if (hash) {
            const targetTab = document.querySelector(`[href="#${hash}"]`);
            if (targetTab) {
                targetTab.click();
            }
        }
    }
    
    // Show tab based on URL hash on page load
    showTabFromHash();
    
    // Handle browser back/forward
    window.addEventListener('hashchange', showTabFromHash);
});
</script>
@endsection