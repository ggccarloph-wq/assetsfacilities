@extends('layouts.admin', ['title' => ($type ?? request('type', 'CAPEX')) === 'OPEX' ? 'Add OPEX Item' : 'Add CAPEX Item'])
@section('content')
<div class="module-head">
    <div>
        <h2 class="module-title">{{ ($type ?? request('type', 'CAPEX')) === 'OPEX' ? 'Add OPEX Item' : 'Add CAPEX Item' }}</h2>
        <div class="module-note">Fill in the sections below to create a new inventory record. Fields marked with an asterisk (<span class="text-danger">*</span>) are required.</div>
    </div>
    <a href="{{ route('items.index', ['type' => $type ?? request('type')]) }}" class="btn-soft small-btn"><i class="bi bi-arrow-left"></i> Back to list</a>
</div>

<form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
    @csrf
    @include('items.form', ['item' => $item ?? null])
    <div class="form-actionbar">
        <button class="btn-primaryx"><i class="bi bi-check2"></i> Save Item</button>
        <a href="{{ route('items.index', ['type' => $type ?? request('type')]) }}" class="btn btn-light small-btn" style="border:1px solid #c7cbd4">Cancel</a>
    </div>
</form>
@endsection
