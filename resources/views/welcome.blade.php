<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>PDFTools — Every tool you need for PDFs</title>
    <meta name="description"
        content="Free online PDF tools to merge, split, compress, convert, rotate and edit PDF files.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: '#e5322d',
                    brandDark: '#c9211c'
                }
            }
        }
    };
    </script>
    <style>
    * {
        box-sizing: border-box
    }

    html {
        scrollbar-width: none
    }

    body {
        font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif
    }

    .hero {
        background: radial-gradient(circle at 75% 20%, rgba(229, 50, 45, .12), transparent 35%), linear-gradient(180deg, #fff, #fff8f7)
    }

    .card,
    .tool-card,
    .payment-card {
        transition: .3s ease
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 45px rgba(15, 23, 42, .12);
        border-color: #fecaca
    }

    .tool-card:hover {
        transform: translateY(-3px);
        background: #fff5f5;
        border-color: #fecaca;
        color: #e5322d
    }

    .payment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 60px rgba(229, 50, 45, .16)
    }

    .float {
        animation: float 4s ease-in-out infinite
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-7px)
        }
    }

    .payment-glow {
        position: relative
    }

    .payment-glow:before {
        content: "";
        position: absolute;
        inset: -1px;
        border-radius: 24px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(229, 50, 45, .5), rgba(255, 255, 255, .9), rgba(229, 50, 45, .2));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none
    }

    @media(max-width:640px) {
        .float {
            animation: none
        }

        .card:hover,
        .payment-card:hover {
            transform: none
        }
    }


.demo-popup-overlay.show {
    display: flex;
    animation: adFadeIn .25s ease;
}

.demo-popup {
    width: 100%;
    max-width: 420px;
    position: relative;
    background: white;
    padding: 30px 24px;
    border-radius: 22px;
    text-align: center;
    box-shadow: 0 25px 80px rgba(0, 0, 0, .25);
    animation: adPopIn .3s ease;
}



    /* Responsive layout fixes for phones, tablets and desktop screens */
    html, body {
        width: 100%;
        max-width: 100%;
        overflow-x: clip;
    }

    img, svg, video, canvas, iframe {
        max-width: 100%;
    }

    section, header, footer, main, nav, .card, .payment-card {
        min-width: 0;
    }

    h1, h2, h3, p, a, span, button {
        overflow-wrap: anywhere;
    }

    #toolsPanel {
        max-width: calc(100vw - 2rem);
    }

    @media (max-width: 640px) {
        #home h1 {
            font-size: clamp(1.65rem, 7vw, 2rem);
            line-height: 1.18;
            letter-spacing: -0.025em;
        }

        #home p {
            font-size: 0.875rem;
            line-height: 1.65;
        }

        #home .float {
            width: 100%;
            max-width: 100%;
            padding: 0.75rem;
        }

        #tools h2, #pricing h2, #workflows h2, #about h2 {
            font-size: clamp(1.5rem, 6vw, 1.875rem);
            line-height: 1.2;
        }

        .payment-card {
            padding: 0.875rem;
        }

        .payment-card .text-5xl {
            font-size: 2.5rem;
            line-height: 1;
        }

        footer .border-t {
            align-items: center;
        }
    }

    @media (min-width: 641px) and (max-width: 1023px) {
        #home h1 {
            font-size: clamp(2.5rem, 5vw, 3.25rem);
            line-height: 1.12;
        }

        #home .float {
            max-width: 28rem;
        }
    }

    </style>
</head>

<body class="bg-white text-slate-900">


    @php
    $tools=[
    ['merge','📚','Merge PDF','Combine multiple PDF files into one document.','organize'],
    ['split','✂️','Split PDF','Separate pages into individual PDF files.','organize'],
    ['compress','🗜️','Compress PDF','Reduce PDF file size while keeping quality.','edit'],
    ['pdf.to.word','📝','PDF to Word','Convert PDF files into editable Word documents.','convert'],
    ['word.to.pdf','📄','Word to PDF','Convert Word documents into PDF files.','convert'],
    ['pdf.to.jpg','🖼️','PDF to JPG','Convert PDF pages into JPG images.','convert'],
    ['jpg.to.pdf','🌄','JPG to PDF','Convert images into professional PDF files.','convert'],
    ['rotate','🔄','Rotate PDF','Rotate PDF pages by 90, 180 or 270 degrees.','edit'],
    ['watermark','💧','Watermark PDF','Add text or image watermarks to your PDF.','security'],
    ['extract','📃','Extract Pages','Extract selected pages from your PDF.','organize'],
    ['addsignature','✍️','Add Signature','Sign your PDF documents easily.','edit'],
    ['protect.pdf','🛡️','Protect PDF','Protect PDF files with a password.','security']
    ];
    $canManagePayments = auth()->check() &&
        strtolower(trim((string) auth()->user()->email)) ===
        strtolower(trim((string) config('app.admin_email')));
    @endphp

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-3 sm:px-6">
            <div class="h-16 flex items-center justify-between">
                <a href="{{route('home')}}" class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-brand text-white flex items-center justify-center text-xs font-black"> PDF</div>
                    <div class="text-lg font-black">PDF<span class="text-brand">Tools</span></div>
                </a>

                <nav class="hidden lg:flex items-center gap-7 text-sm font-bold">
                    <a href="#home" class="hover:text-brand transition">Home</a>
                    <div class="relative">

                        <button id="toolsBtn" class="flex items-center gap-1 hover:text-brand transition">All Tools <span class="text-xs"></span></button>
                        <div id="toolsPanel" class="hidden absolute top-full left-1/2 -translate-x-1/2 mt-4 w-[540px] bg-white border border-slate-200 rounded-2xl shadow-2xl p-4">
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($tools as $t)
                                <a href="{{route($t[0])}}"  class="tool-card flex flex-col items-center justify-center gap-1 min-h-[72px] rounded-xl border border-slate-100 text-center">
                                    <span class="text-xl">{{$t[1]}}</span><span class="text-[10px] font-bold leading-tight">{{$t[2]}}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <a href="#workflows" class="hover:text-brand transition">Workflows</a>
                    <a href="#pricing" class="hover:text-brand transition">Premium</a>
                    <a href="#about" class="hover:text-brand transition">About</a>
                    @if($canManagePayments)
                    <a href="{{ route('admin.payments') }}" class="text-brand transition">Admin Payments</a>
                    @endif
                </nav>

                <div class="flex items-center gap-2">
                    @guest
                    <a href="{{route('login')}}" class="hidden sm:block px-3 py-2 text-sm font-bold hover:text-brand">Login</a>
                    <a href="{{route('register')}}" class="px-4 py-2 bg-brand text-white rounded-lg text-sm font-bold hover:bg-brandDark">Register</a>
                    @endguest

                    @auth
                    <div class="relative">
                        <button id="profileBtn" class="w-10 h-10 rounded-full bg-brand text-white font-bold">
                            {{strtoupper(substr(Auth::user()->name,0,1))}}
                        </button>
                        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-60 bg-white border rounded-xl shadow-xl p-4">
                            <p class="font-bold">{{Auth::user()->name}}</p>
                            <p class="text-xs text-slate-500 truncate">{{Auth::user()->email}}</p>
                            <form action="{{route('logout')}}" method="POST" class="mt-3">@csrf
                                <button class="w-full py-2 rounded-lg bg-red-500 text-white text-sm font-bold">Logout</button>
                            </form>
                        </div>
                    </div>
                    @endauth
                    <button id="menuBtn" type="button" aria-label="Open navigation" aria-controls="mobileMenu"
                        aria-expanded="false" class="lg:hidden w-10 h-10 border rounded-lg text-lg">☰</button>
                </div>
            </div>
        </div>

        <div id="mobileMenu" class="hidden max-h-[calc(100vh-4rem)] overflow-y-auto border-t bg-white lg:hidden">
            <div class="p-3 sm:p-4 space-y-2">
                <a href="#home" class="block px-3 py-2 rounded-lg font-bold hover:bg-red-50">Home</a>
                <button id="mobileToolsBtn" type="button" aria-controls="mobileTools" aria-expanded="false" class="w-full flex items-center justify-between px-3 py-2 rounded-lg font-bold hover:bg-red-50">All Tools <span></span></button>
                <div id="mobileTools" class="hidden p-2 bg-slate-50 rounded-xl">
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($tools as $t)
                        <a href="{{route($t[0])}}" class="flex min-w-0 items-center gap-2 p-2 bg-white border rounded-lg text-xs font-bold hover:text-brand hover:border-red-200">
                        <span class="shrink-0 text-base">{{$t[1]}}</span><span class="min-w-0">{{$t[2]}}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                <a href="#workflows" class="block px-3 py-2 rounded-lg font-bold hover:bg-red-50">Workflows</a>
                <a href="#pricing" class="block px-3 py-2 rounded-lg font-bold hover:bg-red-50">Premium</a>
                <a href="#about" class="block px-3 py-2 rounded-lg font-bold hover:bg-red-50">About</a>
                @if($canManagePayments)
                <a href="{{ route('admin.payments') }}" class="block px-3 py-2 rounded-lg font-bold text-brand hover:bg-red-50">Admin Payments</a>
                @endif
                @guest<a href="{{route('login')}}" class="block px-3 py-2 rounded-lg font-bold">Login</a>@endguest
            </div>
        </div>
    </header>

    <main>
        <section id="home" class="hero py-6 sm:py-16 overflow-hidden">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 grid lg:grid-cols-2 gap-6 lg:gap-14 items-center">
                <div class="text-center lg:text-left">
                    <span class="inline-flex px-3 py-2 rounded-full bg-red-50 border border-red-100 text-xs font-bold text-brand">✦ Free Online PDF Tools</span>
                    <h1 class="mt-5 text-3xl sm:text-5xl lg:text-6xl font-black leading-tight">Every tool you need to work with <span class="text-brand">PDFs in one place</span></h1>
                    <p class="mt-5 text-sm sm:text-lg text-slate-500 leading-7">Every tool you need to use PDFs, at your
                        fingertips. All are 100% FREE and easy to use! Merge, split, compress, convert, rotate and
                        watermark PDFs with just a few clicks.</p>
                    <div class="mt-5 flex flex-wrap justify-center lg:justify-start gap-4 text-xs font-bold text-slate-500">
                        <span>✓ Free</span><span>✓ Secure</span><span>✓ Fast</span><span>✓ Mobile Friendly</span></div>
                    <a href="#tools" class="inline-block mt-7 px-6 py-3 bg-brand text-white rounded-xl font-bold hover:bg-brandDark transition">Explore PDF Tools</a>
                </div>

                <div class="relative flex justify-center py-5 sm:py-10 px-4 sm:px-12">
                    <div class="tool-card absolute top-0 left-0 hidden sm:left-2 md:block bg-white border rounded-xl px-3 py-2 shadow-lg text-[10px] font-bold text-red-600"> 📑 Merge PDF</div>
                    <div class="tool-card absolute top-8 right-0 hidden md:block bg-white border rounded-xl px-3 py-2 shadow-lg text-[10px] font-bold text-blue-600"> ✂️ Split PDF</div>
                    <div class="tool-card absolute bottom-6 left-0 hidden sm:left-3 md:block bg-white border rounded-xl px-3 py-2 shadow-lg text-[10px] font-bold text-emerald-600">🗜️ Compress</div>
                    <div class="tool-card absolute bottom-0 right-1 hidden sm:right-5 md:block bg-white border rounded-xl px-3 py-2 shadow-lg text-[10px] font-bold text-purple-600"> 🔄 Rotate PDF</div>
                    <div class="float w-full max-w-sm bg-white border border-slate-200 rounded-2xl p-3 sm:p-5 shadow-2xl">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-500 text-white flex items-center justify-center"> 📄</div>
                                <div class="min-w-0">
                                    <h3 class="truncate font-black text-sm">Project-Report.pdf</h3>
                                    <p class="text-xs text-slate-500">2.4 MB • 12 Pages</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-bold">PDF</span>
                        </div>
                        <div class="bg-slate-50 border rounded-xl p-3">
                            <div class="h-2.5 w-2/3 bg-slate-800 rounded mb-3"></div>
                            <div class="space-y-1.5">
                                <div class="h-1.5 bg-slate-200 rounded"></div>
                                <div class="h-1.5 w-5/6 bg-slate-200 rounded"></div>
                                <div class="h-1.5 w-4/5 bg-slate-200 rounded"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-4">
                                <div class="h-10 bg-red-100 rounded-lg"></div>
                                <div class="h-10 bg-white border rounded-lg"></div>
                                <div class="h-10 bg-red-100 rounded-lg"></div>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3"><span
                                class="px-2 py-1 bg-red-50 text-red-600 rounded-md text-[10px] font-bold">Merge</span><span
                                class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-[10px] font-bold">Convert</span><span
                                class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-bold">Compress</span>
                        </div>
                        <div class="border-t mt-3 pt-3 flex justify-between text-xs font-bold"><span
                                class="text-slate-500">PDFTools</span><span class="text-emerald-600">● Ready</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tools" class="py-7 sm:py-20">
            <div class="max-w-7xl mx-auto px-3 sm:px-6">
                <p class="text-xs font-black tracking-widest text-brand uppercase">PDF Tools</p>
                <h2 class="mt-2 text-3xl font-black">All the tools you need</h2>
                <div class="flex gap-2 overflow-x-auto py-2 sm:py-5">
                    @foreach(['all'=>'All','organize'=>'Organize PDF','convert'=>'Convert PDF','edit'=>'Edit
                    PDF','security'=>'PDF Security'] as $key=>$name)
                    <button data-filter="{{$key}}"
                        class="filter shrink-0 px-4 py-2 rounded-full border text-sm font-bold {{$key==='all'?'bg-brand text-white':''}}">{{$name}}</button>
                    @endforeach
                </div>
                <div id="toolGrid" class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                    @foreach($tools as $t)
                    <a href="{{route($t[0])}}" data-category="{{$t[4]}}"
                        class="card border border-slate-200 rounded-2xl p-4 sm:p-5 bg-white">
                        <div class="text-3xl">{{$t[1]}}</div>
                        <h3 class="mt-4 font-black text-lg">{{$t[2]}}</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-6">{{$t[3]}}</p>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>
          
        <section id="pricing" class="py-8 sm:py-20 bg-slate-50 border-y">
            <div class="max-w-5xl mx-auto px-3 sm:px-6">
                <div class="text-center">
                    <p class="text-xs font-black tracking-widest text-brand uppercase">Premium Access</p>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-black">Unlock all PDF tools</h2>
                    <p class="mt-3 text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-6">Start with your free
                        uploads. When your free usage is finished, upgrade once and continue using all PDFTools without
                        upload limits.</p>
                </div>

                <div class="mt-6 sm:mt-10 max-w-4xl mx-auto payment-glow">
                    <div class="payment-card relative bg-white border border-red-100 rounded-3xl p-3 sm:p-8 shadow-xl overflow-hidden">
                        <div class="mb-5 flex justify-end lg:absolute lg:top-0 lg:right-0 lg:mb-0">
                        <span class="inline-flex items-center gap-1 bg-brand text-white px-4 py-2 rounded-bl-2xl text-xs font-black">⭐ ONE-TIME UPGRADE</span></div>
                        <div class="grid items-center gap-5 sm:gap-8 lg:grid-cols-2 lg:pt-6">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-brand flex items-center justify-center text-2xl"> 🔓</div>
                                    <div>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-wider">PremiumAccess</p>
                                        <h3 class="text-xl sm:text-2xl font-black">PDFTools Unlimited</h3>
                                    </div>
                                </div>
                                <div class="mt-6 flex items-end gap-2">
                                    <span class="text-5xl font-black text-slate-900">$2</span>
                                    <span class="text-sm text-slate-500 mb-2">one-time payment</span>
                                    </div>
                                <p class="mt-3 text-sm text-slate-500 leading-6">Pay once and unlock the complete PDFTools workspace for your account.</p>
                                <a href="{{route('payment')}}"class="mt-6 inline-flex items-center justify-center gap-2 w-full sm:w-auto px-7 py-3.5 bg-brand text-white rounded-xl font-black shadow-lg shadow-red-100 hover:bg-brandDark transition">Continue to Payment <span>→</span></a>
                            </div>

                            <div>
                                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 sm:p-5">
                                    <h4 class="font-black text-base">What you get</h4>
                                    <div class="mt-4 space-y-3">
                                        @foreach(['All 12 PDF tools','Unlimited uploads','No daily upload limit','Works
                                        on mobile & desktop','One payment for all tools'] as $item)
                                        <div class="flex items-center gap-3">
                                            <div  class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-black">
                                                ✓</div>
                                            <span class="text-sm font-semibold text-slate-700">{{$item}}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <div class="rounded-xl border bg-white p-3 text-center">
                                        <div class="text-lg">📱</div>
                                        <p class="mt-1 text-[10px] font-black">JazzCash</p>
                                    </div>
                                    <div class="rounded-xl border bg-white p-3 text-center">
                                        <div class="text-lg">💳</div>
                                        <p class="mt-1 text-[10px] font-black">Credit Card</p>
                                    </div>
                                    <div class="rounded-xl border bg-white p-3 text-center">
                                        <div class="text-lg">🏦</div>
                                        <p class="mt-1 text-[10px] font-black">Any Bank</p>
                                    </div>
                                </div>
                                <p class="mt-3 text-[11px] text-slate-400 text-center">🔒 Secure payment verification
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="workflows" class="py-7 sm:py-14">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 text-center">
                <p class="text-xs font-black tracking-widest text-brand uppercase">Work your way</p>
                <h2 class="mt-2 text-3xl font-black">Simple tools. Simple workflow.</h2>
                <div class="mt-6 sm:mt-8 grid md:grid-cols-3 gap-3 sm:gap-5">
                    @foreach([['💻','Work anywhere','Use PDFTools on laptop, tablet or phone.'],['⚡','Get things
                    done','Finish common PDF tasks quickly.'],['🔒','Private by design','A clean workspace for PDF
                    processing.']] as $f)
                    <div class="bg-white border rounded-2xl p-4 sm:p-6 text-left">
                        <div class="text-3xl">{{$f[0]}}</div>
                        <h3 class="mt-4 font-black text-lg">{{$f[1]}}</h3>
                        <p class="mt-2 text-sm text-slate-500 leading-6">{{$f[2]}}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="about" class="py-7 sm:py-14">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 grid md:grid-cols-2 gap-4 sm:gap-8">
                <div>
                    <p class="text-xs font-black tracking-widest text-brand uppercase">About PDFTools</p>
                    <h2 class="mt-3 text-3xl font-black">Your everyday PDF workspace.</h2>
                </div>
                <p class="text-slate-500 leading-7">PDFTools brings useful PDF operations together in one clean
                    interface. From merging and splitting to converting, compressing, watermarking, signing and
                    protecting PDFs.</p>
            </div>
        </section>
    </main>

    <footer class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 py-6 sm:py-12">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-black">PDF<span class="text-red-400">Tools</span></h2>
                    <p class="mt-4 text-sm text-gray-400 leading-6 max-w-md">Simple, fast and reliable online PDF tools
                        for everyday document work. Manage, convert, edit and protect your documents with ease.</p>
                </div>
                <div>
                    <h3 class="font-bold">Product</h3>
                    <div class="mt-4 space-y-2 text-sm text-gray-400">
                        <a class="block" href="#home">Home</a><a class="block" href="#tools">All Tools</a><a
                            class="block" href="#pricing">Premium</a><a class="block" href="#workflows">Workflows</a><a
                            class="block" href="#about">About</a>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold">Popular Tools</h3>
                    <div class="mt-4 space-y-2 text-sm text-gray-400">
                        <a class="block" href="{{route('merge')}}">Merge PDF</a><a class="block"
                            href="{{route('compress')}}">Compress PDF</a><a class="block"
                            href="{{route('pdf.to.word')}}">PDF to Word</a><a class="block"
                            href="{{route('protect.pdf')}}">Protect PDF</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col items-start gap-5">
                <div class="flex items-center justify-center gap-4">
                    <a href="https://www.facebook.com/yourpage" target="_blank" rel="noopener noreferrer"
                        aria-label="Facebook"
                        class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-red-500 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 8h3V4h-3c-3.3 0-5 1.7-5 5v3H6v4h3v8h4v-8h3l1-4h-4V9c0-.7.3-1 1-1z" />
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@yourusername" target="_blank" rel="noopener noreferrer"
                        aria-label="TikTok"
                        class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-red-500 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19.6 7.1a5.8 5.8 0 0 1-3.5-1.2v7.4a5.7 5.7 0 1 1-4.9-5.7v3a2.8 2.8 0 1 0 1.9 2.7V2h3a5.8 5.8 0 0 0 3.5 2.2v2.9z" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/yourusername" target="_blank" rel="noopener noreferrer"
                        aria-label="Instagram"
                        class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-red-500 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="2" />
                            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2" />
                            <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://wa.me/923001234567" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
                        class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-red-500 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2a10 10 0 0 0-8.7 15l-1.1 4 4.1-1.1A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.4.7.7-2.3-.2-.3A8 8 0 1 1 12 20zm4.4-5.8c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.7 1-.1.2-.3.2-.5.1-1.4-.7-2.4-1.3-3.4-2.9-.2-.3.2-.3.5-1 .1-.2.1-.3 0-.5s-.6-1.5-.8-2c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.2s.9 2.5 1 2.7c.1.2 1.8 2.8 4.4 3.9 2.6 1.1 2.6.7 3.1.7.5 0 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1-.1-.1-.3-.2-.5-.3z" />
                        </svg>
                    </a>
                </div>
                <div
                    class="flex flex-col sm:flex-row items-center justify-center gap-2 text-xs text-gray-500 text-center">
                    <span>© {{date('Y')}} PDFTools. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>
  

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileToolsBtn = document.getElementById('mobileToolsBtn');
        const mobileTools = document.getElementById('mobileTools');
        const toolsBtn = document.getElementById('toolsBtn');
        const toolsPanel = document.getElementById('toolsPanel');
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');

        if (menuBtn && mobileMenu) menuBtn.addEventListener('click', function () {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            menuBtn.setAttribute('aria-expanded', String(!isOpen));
        });

        if (mobileToolsBtn && mobileTools) mobileToolsBtn.addEventListener('click', function () {
            const isOpen = !mobileTools.classList.contains('hidden');
            mobileTools.classList.toggle('hidden');
            mobileToolsBtn.setAttribute('aria-expanded', String(!isOpen));
        });

        if (toolsBtn && toolsPanel) toolsBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            toolsPanel.classList.toggle('hidden');
        });

        if (profileBtn && profileMenu) profileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            if (toolsPanel && toolsBtn && !toolsPanel.contains(event.target) && !toolsBtn.contains(event.target)) toolsPanel.classList.add('hidden');
            if (profileMenu && profileBtn && !profileMenu.contains(event.target) && !profileBtn.contains(event.target)) profileMenu.classList.add('hidden');
        });

        document.querySelectorAll('.filter').forEach(function (button) {
            button.addEventListener('click', function () {
                const category = button.dataset.filter;
                document.querySelectorAll('.filter').forEach(function (item) {
                    item.classList.remove('bg-brand', 'text-white');
                });
                button.classList.add('bg-brand', 'text-white');
                document.querySelectorAll('#toolGrid [data-category]').forEach(function (card) {
                    card.classList.toggle('hidden', category !== 'all' && card.dataset.category !== category);
                });
            });
        });

        if (mobileMenu) mobileMenu.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileMenu.classList.add('hidden');
                if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
            });
        });
    });
    </script>
</body>
</html>
