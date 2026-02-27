@extends('layouts.admin')

@section('page-actions')
    <div class="d-flex gap-2">
        <x-admin.action-button-add
            permission="admin.page.create"
            :text="__('admin.add_page')"
        />
    </div>
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.page_list') }}</span>
        </div>
        <div id="page" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.id') }}</th>
                            <th>{{ __('admin.page_title') }}</th>
                            <th>{{ __('admin.friendly_url') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pages as $page)
                            <tr>
                                <td>{{ $page->id }}</td>
                                <td>{{ $page->title }}</td>
                                <td>{{ $page->slug ?? '-' }}</td>
                                <td>{{ $page->sort_order }}</td>
                                <td>
                                    <x-admin.status-badge :status="$page->status" />
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row
                                        :id="$page->id"
                                        baseName="page"
                                        :itemName="__('admin.pages')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.pages')])"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">{{ __('admin.no_pages') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$pages" />
        </div>
    </div>
@endsection
