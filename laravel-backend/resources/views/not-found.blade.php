@extends('layouts.app')

@section('content')
    <section class="section-block" style="margin-top:2rem;text-align:center;">
        <div class="container">
            <h1 class="page-title">404 – Page not found</h1>
            <p class="page-copy">The page you are looking for does not exist or has been moved. Please return to the homepage or browse the product catalog.</p>
            <a href="/products" class="button-primary" style="margin-top:1.5rem;display:inline-block;">Browse Products</a>
        </div>
    </section>
@endsection
