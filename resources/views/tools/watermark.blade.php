<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Watermark PDF - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
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
    }
    </script>
    <style>
    * {
        box-sizing: border-box
    }

    body {
        font-family: Inter, Arial, sans-serif
    }
          html {
        scrollbar-width: none
    }

    .hero {
        background: #fff8f7;
        background-image: linear-gradient(#e5322d08 1px, transparent 1px), linear-gradient(90deg, #e5322d08 1px, transparent 1px);
        background-size: 45px 45px
    }

    .drag {
        border-color: #e5322d !important;
        background: #fff5f5 !important
    }

    .option.active {
        border-color: #e5322d;
        background: #fff5f5;
        color: #c9211c
    }

    .loader {
        width: 20px;
        height: 20px;
        border: 3px solid #ffffff55;
        border-top-color: white;
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }
    </style>
</head>

<body class="bg-white text-slate-900">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">
            <a href="{{route('home')}}" class="flex items-center gap-2">
                <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span>
                <span class="text-2xl font-black">Tools</span>
            </a>
            <nav class="hidden md:flex gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="text-brand">All Tools</a>
                <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
                <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 text-center">
            <span
                class="inline-flex rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">💧
                PDF Tool 09</span>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">Watermark <span
                    class="text-brand">PDF</span></h1>
            <p class="max-w-2xl mx-auto mt-5 text-slate-500 text-base sm:text-lg">Add text or image watermarks to your
                PDF quickly and easily.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 sm:py-14">

        <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold">Free Uploads</p>
                    <p id="usageText" class="text-xs text-slate-500 mt-1">You have 4 free file uploads available across
                        all PDF tools.</p>
                </div>
                <div class="text-sm font-black text-brand"><span id="usedCount">0</span> / 4</div>
            </div>
        </div>

        <div id="drop" class="bg-white border-2 border-dashed border-slate-300 rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-center cursor-pointer hover:border-brand hover:bg-red-50">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-3xl">💧</div>
            <h2 class="mt-5 text-xl sm:text-2xl font-black">Upload your PDF</h2>
            <p class="mt-2 text-sm text-slate-500">Drag & drop your PDF here or click to browse</p>
            <input id="input" type="file" accept="application/pdf" class="hidden">
            <button type="button" id="chooseBtn" class="mt-6 bg-brand hover:bg-brandDark text-white font-bold px-6 py-3 rounded-lg">Choose PDF</button>
            <p class="mt-4 text-xs text-slate-400">PDF only • No frontend MB upload limit</p>
        </div>

        <div id="box" class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <b id="name" class="block truncate"></b>
                    <p id="size" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button id="deleteBtn"
                    class="bg-red-50 hover:bg-red-100 text-brand px-4 py-2 rounded-lg font-semibold">Delete</button>
            </div>

            <div class="border-t border-slate-100 mt-6 pt-6">
                <h3 class="font-black text-lg">Watermark Settings</h3>
                <p class="text-sm text-slate-500 mt-1">Customize your PDF watermark.</p>

                <label class="block text-sm font-bold mt-5 mb-2">Watermark Type</label>
                <div class="grid grid-cols-2 gap-3">
                    <button id="textBtn" class="option active border-2 rounded-xl py-3 font-bold">Text
                        Watermark</button>
                    <button id="imageBtn" class="option border-2 border-slate-200 rounded-xl py-3 font-bold">Logo /
                        Image</button>
                </div>

                <div id="textOptions" class="mt-5">
                    <label class="text-sm font-bold">Watermark Text</label>
                    <input id="waterText" value="CONFIDENTIAL"
                        class="w-full mt-2 border-2 border-slate-200 rounded-lg px-4 py-3 focus:border-brand outline-none">
                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="text-sm font-bold">Font Size</label>
                            <input id="fontSize" type="number" value="40" min="10" max="150"
                                class="w-full mt-2 border-2 border-slate-200 rounded-lg px-4 py-3">
                        </div>
                        <div>
                            <label class="text-sm font-bold">Opacity</label>
                            <input id="textOpacity" type="range" min=".1" max="1" step=".1" value=".35"
                                class="w-full mt-5 accent-red-600">
                        </div>
                    </div>
                </div>

                <div id="imageOptions" class="hidden mt-5">
                    <label class="text-sm font-bold">Upload Logo / Image</label>
                    <input id="image" type="file" accept="image/png,image/jpeg"
                        class="w-full mt-2 border-2 border-slate-200 rounded-lg p-3">
                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="text-sm font-bold">Logo Size</label>
                            <input id="logoSize" type="range" min="50" max="500" value="180"
                                class="w-full mt-5 accent-red-600">
                        </div>
                        <div>
                            <label class="text-sm font-bold">Opacity</label>
                            <input id="logoOpacity" type="range" min=".1" max="1" step=".1" value=".5"
                                class="w-full mt-5 accent-red-600">
                        </div>
                    </div>
                </div>

                <label class="block text-sm font-bold mt-5 mb-2">Position</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" class="pos option active border-2 rounded-xl py-3 font-bold"
                        data-pos="center">Center</button>
                    <button type="button" class="pos option border-2 border-slate-200 rounded-xl py-3 font-bold"
                        data-pos="top">Top</button>
                    <button type="button" class="pos option border-2 border-slate-200 rounded-xl py-3 font-bold"
                        data-pos="bottom">Bottom</button>
                </div>

                <label class="flex items-center gap-3 mt-5 text-sm font-semibold">
                    <input id="allPages" type="checkbox" checked class="w-5 h-5 accent-red-600">
                    Apply watermark to all pages
                </label>

                <button id="create"
                    class="mt-6 w-full bg-brand hover:bg-brandDark text-white py-3.5 rounded-xl font-bold">Add Watermark
                    →</button>

                <div id="progress" class="hidden mt-5">
                    <div class="flex justify-between text-sm mb-2"><span id="status">Processing...</span><span
                            id="percent">0%</span></div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div id="bar" class="h-full bg-brand w-0 transition-all"></div>
                    </div>
                </div>

                <x-payment-upgrade-card />

            </div>
        </div>

        <div id="result" class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="bg-emerald-50 text-emerald-600 rounded-xl p-4 font-bold">✓ Watermark Added Successfully</div>
            <p id="info" class="text-sm text-slate-500 mt-3"></p>
            <div class="grid sm:grid-cols-2 gap-3 mt-5">
                <button id="viewBtn" class="bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg font-bold">View
                    PDF</button>
                <a id="download"
                    class="bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold text-center">Download PDF
                    ↓</a>
            </div>
        </div>

    </main>

    <div id="viewer" class="hidden fixed inset-0 bg-black/80 z-[60] p-2 sm:p-4">
        <div class="bg-white w-full h-full rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 border-b flex justify-between items-center">
                <b>PDF Preview</b>
                <button id="closeViewer" class="bg-slate-100 px-4 py-2 rounded-lg font-semibold">Close</button>
            </div>
            <iframe id="frame" class="w-full flex-1 border-0"></iframe>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-100 py-7">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <b class="text-xl">PDF<span class="text-brand">Tools</span></b>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-brand font-semibold">All Tools →</a>
        </div>
    </footer>

    <script>
    const $ = id => document.getElementById(id),
        FREE_LIMIT = 4;
    let file = null,
        logoFile = null,
        url = null,
        mode = "text",
        position = "center";

    function fmt(n) {
        return n < 1024 ? n + " B" : n < 1048576 ? (n / 1024).toFixed(1) + " KB" : n < 1073741824 ? (n / 1048576)
            .toFixed(2) + " MB" : (n / 1073741824).toFixed(2) + " GB";
    }

    function load(f) {
        if (!f) return;
        if (f.size === 0) {
            $("input").value = "";
            return alert('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !/\.pdf$/i.test(f.name)) return alert("Please select a PDF file.");
        file = f;
        $("name").textContent = f.name;
        $("size").textContent = "PDF • " + fmt(f.size);
        $("box").classList.remove("hidden");
        $("result").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
    }

    $("chooseBtn").onclick = () => $("input").click();
    $("input").onchange = e => load(e.target.files[0]);

    $("drop").onclick = e => {
        if (e.target.id !== "chooseBtn") $("input").click();
    };
    $("drop").ondragover = e => {
        e.preventDefault();
        $("drop").classList.add("drag")
    };
    $("drop").ondragleave = () => $("drop").classList.remove("drag");
    $("drop").ondrop = e => {
        e.preventDefault();
        e.stopPropagation();
        $("drop").classList.remove("drag");
        load(e.dataTransfer.files[0]);
    };

    function setMode(v) {
        mode = v;
        $("textBtn").classList.toggle("active", v === "text");
        $("imageBtn").classList.toggle("active", v === "image");
        $("textOptions").classList.toggle("hidden", v !== "text");
        $("imageOptions").classList.toggle("hidden", v !== "image");
    }
    $("textBtn").onclick = () => setMode("text");
    $("imageBtn").onclick = () => setMode("image");

    $("image").onchange = e => {
        const f = e.target.files[0];
        if (!f) return;
        if (f.size === 0) {
            e.target.value = "";
            return alert('"' + f.name + '" is empty (0 bytes). Please choose a non-empty image.');
        }
        if (!["image/png", "image/jpeg"].includes(f.type)) return alert("Please select PNG or JPG image.");
        logoFile = f;
    };

    document.querySelectorAll(".pos").forEach(btn => btn.onclick = () => {
        document.querySelectorAll(".pos").forEach(x => {
            x.classList.remove("active");
            x.classList.add("border-slate-200");
        });
        btn.classList.add("active");
        btn.classList.remove("border-slate-200");
        position = btn.dataset.pos;
    });

    function progress(n, t) {
        $("bar").style.width = n + "%";
        $("percent").textContent = Math.round(n) + "%";
        $("status").textContent = t;
    }

    async function reserveUpload() {
        const r = await fetch("{{route('pdf.usage.reserve')}}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                file_count: 1
            })
        });
        let data = {};
        try {
            data = await r.json()
        } catch (e) {
            throw Error("Could not check upload access.")
        }
        return {
            ok: r.ok,
            ...data
        };
    }

    $("create").onclick = async () => {
        if (!file) return alert("Please upload PDF first.");
        if (mode === "image" && !logoFile) return alert("Please upload logo/image first.");

        const btn = $("create");
        btn.disabled = true;
        btn.innerHTML =
            '<span class="inline-flex items-center gap-2"><span class="loader"></span>Checking access...</span>';

        try {
            const access = await reserveUpload();

            if (access.requires_payment || access.allowed === false) {
                $("paymentBox").classList.remove("hidden");
                const used = Number(access.free_uploads_used ?? FREE_LIMIT);
                $("usedCount").textContent = Math.min(FREE_LIMIT, Math.max(0, used));
                $("usageText").textContent = "Your 4 free uploads across all PDF tools have been used.";
                $("paymentBox").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                return;
            }

            if (access.premium) {
                $("usedCount").textContent = "Unlimited";
                $("usageText").textContent = "Premium access active. Unlimited uploads are allowed.";
            } else {
                const used = Number(access.free_uploads_used ?? 0);
                const remaining = Math.max(0, Math.min(FREE_LIMIT, Number(access.remaining ?? FREE_LIMIT -
                    used)));
                $("usedCount").textContent = Math.min(FREE_LIMIT, Math.max(0, used));
                $("usageText").textContent = remaining + " free upload(s) remaining across all PDF tools.";
            }

            btn.innerHTML =
                '<span class="inline-flex items-center gap-2"><span class="loader"></span>Processing...</span>';
            $("progress").classList.remove("hidden");
            progress(5, "Reading PDF...");

            const pdf = await PDFLib.PDFDocument.load(await file.arrayBuffer());
            const pages = pdf.getPages();

            if (mode === "text") {
                const text = $("waterText").value.trim() || "CONFIDENTIAL";
                const fontSize = Number($("fontSize").value) || 40;
                const opacity = Number($("textOpacity").value) || .35;
                const font = await pdf.embedFont(PDFLib.StandardFonts.HelveticaBold);

                for (let i = 0; i < pages.length; i++) {
                    if ($("allPages").checked) {
                        const p = pages[i],
                            {
                                width: w,
                                height: h
                            } = p.getSize();
                        const tw = font.widthOfTextAtSize(text, fontSize);
                        const x = (w - tw) / 2;
                        const y = position === "top" ? h - fontSize - 40 : position === "bottom" ? 40 : (h -
                            fontSize) / 2;

                        p.drawText(text, {
                            x,
                            y,
                            size: fontSize,
                            font,
                            color: PDFLib.rgb(.35, .35, .35),
                            opacity,
                            rotate: position === "center" ? PDFLib.degrees(45) : PDFLib.degrees(0)
                        });
                    }
                    progress(10 + ((i + 1) / pages.length) * 80, "Watermarking page " + (i + 1) + "...");
                }
            } else {
                const bytes = await logoFile.arrayBuffer();
                const img = logoFile.type === "image/png" ? await pdf.embedPng(bytes) : await pdf.embedJpg(
                    bytes);
                const width = Number($("logoSize").value) || 180;
                const opacity = Number($("logoOpacity").value) || .5;
                const scale = width / img.width,
                    height = img.height * scale;

                for (let i = 0; i < pages.length; i++) {
                    if ($("allPages").checked) {
                        const p = pages[i],
                            {
                                width: w,
                                height: h
                            } = p.getSize();
                        const x = (w - width) / 2;
                        const y = position === "top" ? h - height - 40 : position === "bottom" ? 40 : (h -
                            height) / 2;
                        p.drawImage(img, {
                            x,
                            y,
                            width,
                            height,
                            opacity,
                            rotate: position === "center" ? PDFLib.degrees(45) : PDFLib.degrees(0)
                        });
                    }
                    progress(10 + ((i + 1) / pages.length) * 80, "Adding logo to page " + (i + 1) + "...");
                }
            }

            progress(95, "Creating PDF...");
            const bytes = await pdf.save();
            const blob = new Blob([bytes], {
                type: "application/pdf"
            });

            if (url) URL.revokeObjectURL(url);
            url = URL.createObjectURL(blob);

            $("download").href = url;
            $("download").download = "watermarked-" + file.name;
            $("info").textContent = pages.length + " page(s) processed • " + fmt(blob.size);

            progress(100, "Complete!");
            $("result").classList.remove("hidden");
            $("result").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

        } catch (error) {
            console.error(error);
            alert(error.message || "Could not create watermark.");
            $("progress").classList.add("hidden");
        } finally {
            btn.disabled = false;
            btn.textContent = "Add Watermark →";
        }
    };

    $("viewBtn").onclick = () => {
        if (!url) return;
        $("frame").src = url;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    };

    function closeViewer() {
        $("viewer").classList.add("hidden");
        $("frame").src = "";
        document.body.classList.remove("overflow-hidden");
    }
    $("closeViewer").onclick = closeViewer;

    function resetAll() {
        closeViewer();
        if (url) URL.revokeObjectURL(url);
        file = null;
        logoFile = null;
        url = null;
        $("input").value = "";
        $("image").value = "";
        $("box").classList.add("hidden");
        $("result").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
        $("progress").classList.add("hidden");
        $("bar").style.width = "0%";
        $("percent").textContent = "0%";
        $("status").textContent = "Processing...";
    }
    $("deleteBtn").onclick = resetAll;
    </script>
</body>

</html>