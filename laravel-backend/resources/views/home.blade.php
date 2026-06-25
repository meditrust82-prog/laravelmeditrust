@extends('layouts.app')

@section('title', 'Home | Meditrust Nepal')

@section('content')
    @php $hp = $homepage['data'] ?? $homepage ?? null; @endphp

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div>
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 text-sm mb-4">{{ $hp['hero_badge'] ?? "Nepal's Trusted Surgical Equipment Partner" }}</div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight">{!! nl2br(e($hp['hero_title'] ?? 'Surgical Equipment\nBuilt for Excellence')) !!}</h1>
                    <p class="mt-4 text-white/90 max-w-2xl">{{ $hp['hero_subtitle'] ?? 'Supplying certified surgical instruments, operating room equipment, and medical devices to hospitals and clinics across Nepal — with expert support from day one.' }}</p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="/products" class="inline-flex items-center px-5 py-3 bg-white text-primary-900 rounded-xl font-semibold shadow hover:opacity-95">Browse Products</a>
                        <a href="/contact" class="inline-flex items-center px-5 py-3 border border-white/30 text-white rounded-xl font-semibold hover:bg-white/5">Request a Quote</a>
                        <a href="https://wa.me/9779818100515" target="_blank" class="inline-flex items-center px-4 py-3 bg-green-500 text-white rounded-xl font-semibold">WhatsApp</a>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold">500+</div>
                            <div class="text-sm text-white/80">Surgical Products</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">200+</div>
                            <div class="text-sm text-white/80">Hospitals Served</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">15+</div>
                            <div class="text-sm text-white/80">Years Experience</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">24/7</div>
                            <div class="text-sm text-white/80">Support Hours</div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 lg:mt-0">
                    <div class="bg-white rounded-2xl p-6 shadow-lg text-gray-900">
                        <h3 class="font-semibold text-lg">Featured Products</h3>
                        <p class="text-sm text-gray-600 mt-2">High-demand surgical equipment trusted by Nepal's leading hospitals.</p>
                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <a href="/products" class="block p-3 border rounded-lg hover:shadow-sm">View All Products →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Product Range / Categories --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Product Range</h2>
                <a href="/products" class="text-primary-600 font-medium">View all products</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $range = ['Surgical Instruments','Operating Room','Sterilization','Patient Monitoring','Anesthesia','Orthopedic','Laparoscopic','Diagnostic'];
                @endphp
                @foreach($range as $r)
                    <a href="/products?category={{ urlencode($r) }}" class="group bg-white border border-gray-100 rounded-xl p-4 hover:shadow-md transition">
                        <div class="text-sm text-primary-600 font-semibold mb-2">{{ $r }}</div>
                        <div class="text-sm text-gray-500">Explore {{ $r }} equipment and supplies.</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Brands and Why choose us --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Why Meditrust Nepal</h2>
                    <ul class="mt-4 space-y-3 text-gray-600">
                        <li>CE & ISO Certified products meeting international standards.</li>
                        <li>Expert consultation from qualified biomedical engineers.</li>
                        <li>24/7 technical support and nationwide servicing.</li>
                        <li>Competitive pricing and authorised brands.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500">Trusted Brands</h3>
                    <div class="mt-4 grid grid-cols-3 gap-4 items-center">
                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">Mindray</div>
                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">Philips</div>
                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">GE</div>
                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">Drager</div>
                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">Bionet</div>
                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">Stryker</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">What Our Clients Say</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white border p-6 rounded-xl shadow-sm">
                    <p class="text-sm text-gray-700">"Meditrust Nepal supplied and commissioned our entire ICU in under 2 weeks. Excellent after-sales support."</p>
                    <div class="mt-4 font-semibold">Dr. Ramesh Shrestha</div>
                    <div class="text-sm text-gray-500">ICU Director, Grande International Hospital</div>
                </div>
                <div class="bg-white border p-6 rounded-xl shadow-sm">
                    <p class="text-sm text-gray-700">"The equipment quality and documentation were outstanding. Highly recommend."</p>
                    <div class="mt-4 font-semibold">Ms. Sunita Pradhan</div>
                    <div class="text-sm text-gray-500">Biomedical Engineer, Patan Hospital</div>
                </div>
                <div class="bg-white border p-6 rounded-xl shadow-sm">
                    <p class="text-sm text-gray-700">"We sourced our complete OT setup from Meditrust Nepal. Fast delivery and professional service."</p>
                    <div class="mt-4 font-semibold">Dr. Anil Gurung</div>
                    <div class="text-sm text-gray-500">Director, Nobel Hospital</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="py-12 bg-primary-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-xl font-bold text-gray-900">Stay Ahead in Medical Equipment</h3>
            <p class="text-gray-600 mt-2">Join 500+ hospital procurement managers and biomedical engineers who get our monthly updates.</p>
            <form method="POST" action="/api/v1/notify/newsletter" class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                @csrf
                <input type="text" name="phone" placeholder="Your phone or Telegram number" required class="px-4 py-3 rounded-lg border border-gray-200 w-full sm:w-auto" />
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg font-semibold">Subscribe via Telegram →</button>
            </form>
            <p class="text-xs text-gray-500 mt-3">By subscribing, we'll reach you on WhatsApp. No spam, ever.</p>
        </div>
    </section>

@endsection
