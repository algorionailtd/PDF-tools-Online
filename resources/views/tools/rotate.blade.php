<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Rotate PDF - PDFTools</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
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

    html {
        scroll-behavior: smooth;
        scrollbar-width: none
    }

    body {
        font-family: Inter, Arial, sans-serif
    }

    .hero {
        background: #fff8f7;
        background-image: linear-gradient(#e5322d08 1px, transparent 1px), linear-gradient(90deg, #e5322d08 1px, transparent 1px);
        background-size: 45px 45px
    }

    .upload {
        transition: .25s ease
    }

    .upload:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 45px #0f172a14
    }

    .drag {
        background: #fff5f5 !important;
        border-color: #e5322d !important
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

    .angle-card {
        transition: .2s ease
    }

    .angle-card:hover {
        transform: translateY(-2px)
    }

    .angle-card.active {
        border-color: #e5322d;
        background: #fff5f5;
        box-shadow: 0 8px 25px #e5322d14
    }

    .angle-card.active .angle-icon {
        background: #e5322d;
        color: white
    }

    button,
    a {
        transition: .2s ease
    }
    </style>
</head>

<body class="bg-white text-slate-900">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">
            <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0">
                <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span>
                <span class="text-2xl font-black">Tools</span>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 lg:py-20 text-center">
            <span
                class="inline-flex items-center gap-2 rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">🔄
                PDF Tool 08</span>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">Rotate <span
                    class="text-brand">PDF</span></h1>
            <p class="max-w-2xl mx-auto mt-5 text-slate-500 text-base sm:text-lg leading-7">Rotate your PDF pages by
                90°, 180° or 270° quickly and easily.</p>
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

        <div id="drop" class="upload bg-white border-2 border-dashed border-slate-300  rounded-2xl sm:rounded-3xl p-6 sm:p-10 lg:p-12 text-center cursor-pointer hover:border-brand hover:bg-red-50">
            <div class="mx-auto w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-red-50 flex items-center justify-center text-3xl sm:text-4xl">
                🔄</div>
            <h2 class="mt-5 text-xl sm:text-2xl font-black">Upload your PDF</h2>
            <p class="mt-2 text-sm sm:text-base text-slate-500">Drag & drop your PDF here or click to browse</p>
            <input id="input" type="file" accept="application/pdf" class="hidden">
            <button type="button" onclick="event.stopPropagation();input.click()" class="mt-6 w-full sm:w-auto bg-brand hover:bg-brandDark text-white font-bold px-6 py-3 rounded-lg shadow-lg shadow-red-100">Choose
                PDF</button>
            <p class="mt-4 text-xs text-slate-400">PDF only • No frontend MB upload limit</p>
        </div>

        <div id="box" class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <b id="name" class="block truncate text-base sm:text-lg"></b>
                    <p id="size" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button type="button" onclick="resetAll()"
                    class="w-full sm:w-auto bg-red-50 hover:bg-red-100 text-brand px-4 py-2.5 rounded-lg font-semibold">Delete</button>
            </div>

            <div class="border-t border-slate-100 mt-6 pt-6">
                <h3 class="font-black text-lg">Rotation Settings</h3>
                <p class="text-sm text-slate-500 mt-1">Choose the rotation angle and pages.</p>

                <label class="block text-sm font-bold mt-5 mb-3">Rotation Angle</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                    <button type="button"
                        class="angle-card active border-2 border-slate-200 rounded-xl p-4 text-left bg-white"
                        data-angle="90" onclick="selectAngle(90,this)">
                        <div class="flex items-center gap-3">
                            <div
                                class="angle-icon w-11 h-11 rounded-xl bg-red-50 text-brand flex items-center justify-center text-xl">
                                ↻</div>
                            <div>
                                <p class="font-black">90°</p>
                                <p class="text-xs text-slate-500">Clockwise</p>
                            </div>
                        </div>
                    </button>

                    <button type="button" class="angle-card border-2 border-slate-200 rounded-xl p-4 text-left bg-white"
                        data-angle="180" onclick="selectAngle(180,this)">
                        <div class="flex items-center gap-3">
                            <div
                                class="angle-icon w-11 h-11 rounded-xl bg-red-50 text-brand flex items-center justify-center text-xl">
                                ↻</div>
                            <div>
                                <p class="font-black">180°</p>
                                <p class="text-xs text-slate-500">Upside down</p>
                            </div>
                        </div>
                    </button>

                    <button type="button" class="angle-card border-2 border-slate-200 rounded-xl p-4 text-left bg-white"
                        data-angle="270" onclick="selectAngle(270,this)">
                        <div class="flex items-center gap-3">
                            <div
                                class="angle-icon w-11 h-11 rounded-xl bg-red-50 text-brand flex items-center justify-center text-xl">
                                ↺</div>
                            <div>
                                <p class="font-black">270°</p>
                                <p class="text-xs text-slate-500">Clockwise</p>
                            </div>
                        </div>
                    </button>

                </div>

                <input type="hidden" id="angle" value="90">

                <label class="block text-sm font-bold mt-6 mb-3">Pages to Rotate</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <label id="allCard" class="border-2 border-brand bg-red-50 rounded-xl p-4 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pageMode" value="all" checked onchange="changePageMode()"
                                class="accent-red-600">
                            <div>
                                <p class="font-black">All Pages</p>
                                <p class="text-xs text-slate-500">Rotate every page</p>
                            </div>
                        </div>
                    </label>

                    <label id="selectedCard" class="border-2 border-slate-200 bg-white rounded-xl p-4 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pageMode" value="selected" onchange="changePageMode()"
                                class="accent-red-600">
                            <div>
                                <p class="font-black">Selected Pages</p>
                                <p class="text-xs text-slate-500">Choose specific pages</p>
                            </div>
                        </div>
                    </label>

                </div>

                <div id="selectedBox" class="hidden mt-4">
                    <label class="block text-sm font-bold mb-2">Page Numbers</label>
                    <input id="pages" type="text" placeholder="Example: 1,3,5-8"
                        class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 outline-none focus:border-brand">
                    <p class="text-xs text-slate-400 mt-2">Use commas for individual pages and hyphens for ranges.
                        Example: 1,3,5-8</p>
                </div>

                <button id="rotate" type="button" onclick="rotatePDF()"
                    class="mt-6 w-full bg-brand hover:bg-brandDark text-white py-3.5 rounded-xl font-bold shadow-lg shadow-red-100">Rotate
                    PDF →</button>

                <div id="progress" class="hidden mt-5">
                    <div class="flex justify-between gap-4 text-sm mb-2"><span id="ptext">Processing...</span><span
                            id="pct">0%</span></div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div id="bar" class="h-full bg-brand rounded-full w-0 transition-all duration-300"></div>
                    </div>
                </div>

                <x-payment-upgrade-card />
            </div>
        </div>

        <div id="result"
            class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm text-center">
            <div class="bg-emerald-50 text-emerald-600 rounded-xl p-4 font-bold">✓ PDF Rotated Successfully</div>
            <p id="stats" class="text-sm sm:text-base text-slate-500 mt-3 leading-6"></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <button type="button" onclick="viewPDF()"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg font-bold">View
                    PDF</button>
                <a id="download"
                    class="w-full bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold flex items-center justify-center">Download
                    PDF</a>
            </div>
        </div>

    </main>

    <div id="viewer" class="hidden fixed inset-0 bg-black/80 z-[60] p-2 sm:p-4">
        <div class="bg-white w-full h-full rounded-xl sm:rounded-2xl overflow-hidden flex flex-col">
            <div class="p-3 sm:p-4 border-b flex items-center justify-between gap-4">
                <b class="text-sm sm:text-base">PDF Preview</b>
                <button type="button" onclick="closeViewer()"
                    class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg text-sm font-semibold">Close</button>
            </div>
            <iframe id="frame" class="w-full flex-1 border-0"></iframe>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-100 py-7">
        <div
            class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row gap-4 justify-between items-center text-center sm:text-left">
            <b class="text-xl">PDF<span class="text-brand">Tools</span></b>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-brand font-semibold">All Tools →</a>
        </div>
    </footer>

    <script>
    const $ = id => document.getElementById(id);
    let file = null,
        url = null;

    drop.onclick = e => {
        if (!e.target.closest("button")) input.click()
    };
    input.onchange = e => {
        if (e.target.files[0]) load(e.target.files[0])
    };

    ["dragenter", "dragover"].forEach(t => drop.addEventListener(t, e => {
        e.preventDefault();
        e.stopPropagation();
        drop.classList.add("drag");
    }));

    ["dragleave", "drop"].forEach(t => drop.addEventListener(t, e => {
        e.preventDefault();
        e.stopPropagation();
        drop.classList.remove("drag");
    }));

    drop.ondrop = e => {
        if (e.dataTransfer.files[0]) load(e.dataTransfer.files[0])
    };

    function fmt(n) {
        if (!n) return "0 B";
        const u = ["B", "KB", "MB", "GB", "TB"],
            i = Math.floor(Math.log(n) / Math.log(1024));
        return (n / Math.pow(1024, i)).toFixed(i ? 2 : 0) + " " + u[i];
    }

    function load(f) {
        if (f.size === 0) {
            input.value = "";
            return alert('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !f.name.toLowerCase().endsWith(".pdf"))
            return alert("Please select a PDF file.");

        file = f;
        $("name").textContent = f.name;
        $("size").textContent = "PDF Document • " + fmt(f.size);
        $("box").classList.remove("hidden");
        $("result").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
        $("progress").classList.add("hidden");
    }

    function selectAngle(value, button) {
        $("angle").value = value;
        document.querySelectorAll(".angle-card").forEach(c => c.classList.remove("active"));
        button.classList.add("active");
    }

    function changePageMode() {
        const mode = document.querySelector('input[name="pageMode"]:checked').value;
        const selected = mode === "selected";

        $("selectedBox").classList.toggle("hidden", !selected);

        $("allCard").classList.toggle("border-brand", !selected);
        $("allCard").classList.toggle("bg-red-50", !selected);
        $("allCard").classList.toggle("border-slate-200", selected);
        $("allCard").classList.toggle("bg-white", selected);

        $("selectedCard").classList.toggle("border-brand", selected);
        $("selectedCard").classList.toggle("bg-red-50", selected);
        $("selectedCard").classList.toggle("border-slate-200", !selected);
        $("selectedCard").classList.toggle("bg-white", !selected);
    }

    function prog(v, t) {
        $("bar").style.width = v + "%";
        $("pct").textContent = v + "%";
        $("ptext").textContent = t;
    }

    function updateUsage(used, remaining) {
        used = Math.min(Math.max(Number(used || 0), 0), 4);
        $("usedCount").textContent = used;
        $("usageText").textContent = remaining > 0 ?
            remaining + " free upload(s) remaining across all PDF tools." :
            "Your 4 free uploads have been used.";
    }

    async function reserveUpload() {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch("{{route('pdf.usage.reserve')}}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({
                file_count: 1
            })
        });

        let data = {};
        try {
            data = await response.json()
        } catch (e) {
            throw new Error("Could not check upload access.");
        }

        return {
            ok: response.ok,
            status: response.status,
            ...data
        };
    }

    function parsePages(value, total) {
        const result = new Set();
        const parts = value.split(",").map(x => x.trim()).filter(Boolean);

        if (!parts.length) throw new Error("Please enter page numbers.");

        for (const part of parts) {
            if (part.includes("-")) {
                const range = part.split("-").map(x => parseInt(x.trim(), 10));
                if (range.length !== 2 || Number.isNaN(range[0]) || Number.isNaN(range[1]))
                    throw new Error("Invalid page range: " + part);

                const start = Math.min(range[0], range[1]),
                    end = Math.max(range[0], range[1]);
                if (start < 1 || end > total) throw new Error("Selected page is outside the PDF.");

                for (let i = start; i <= end; i++) result.add(i);
            } else {
                const page = parseInt(part, 10);
                if (Number.isNaN(page) || page < 1 || page > total)
                    throw new Error("Invalid page number: " + part);
                result.add(page);
            }
        }

        return Array.from(result).sort((a, b) => a - b);
    }

    async function rotatePDF() {
        if (!file) return alert("Please select a PDF first.");

        const selectedAngle = Number($("angle").value);
        if (![90, 180, 270].includes(selectedAngle))
            return alert("Please select a rotation angle.");

        const btn = $("rotate");
        btn.disabled = true;
        btn.innerHTML =
            '<span class="inline-flex items-center justify-center gap-2"><span class="loader"></span>Checking access...</span>';

        try {
            const access = await reserveUpload();

            if (access.requires_payment || access.allowed === false) {
                updateUsage(4, 0);
                $("paymentBox").classList.remove("hidden");
                btn.disabled = false;
                btn.textContent = "Rotate PDF →";
                $("paymentBox").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                return;
            }

            if (!access.ok || access.allowed === false)
                throw new Error(access.message || "Upload access could not be verified.");

            if (access.premium) {
                $("usedCount").textContent = "Unlimited";
                $("usageText").textContent = "Premium access active. Unlimited uploads are allowed.";
            } else {
                updateUsage(
                    Number(access.free_uploads_used ?? 0),
                    Number(access.remaining ?? 0)
                );
            }

            btn.innerHTML =
                '<span class="inline-flex items-center justify-center gap-2"><span class="loader"></span>Rotating...</span>';
            $("progress").classList.remove("hidden");

            prog(5, "Reading PDF...");
            const buffer = new Uint8Array(await file.arrayBuffer());

            prog(15, "Opening PDF...");
            const pdf = await PDFLib.PDFDocument.load(buffer);
            const total = pdf.getPageCount();

            const mode = document.querySelector('input[name="pageMode"]:checked').value;
            const pageNumbers = mode === "selected" ?
                parsePages($("pages").value.trim(), total) :
                Array.from({
                    length: total
                }, (_, i) => i + 1);

            prog(25, "Preparing pages...");

            for (let i = 0; i < pageNumbers.length; i++) {
                const page = pdf.getPage(pageNumbers[i] - 1);
                const current = page.getRotation().angle || 0;
                page.setRotation(PDFLib.degrees((current + selectedAngle) % 360));

                prog(
                    25 + Math.round(((i + 1) / pageNumbers.length) * 60),
                    "Rotating page " + pageNumbers[i] + " of " + total + "..."
                );
            }

            prog(90, "Creating rotated PDF...");
            const bytes = await pdf.save({
                useObjectStreams: true
            });
            const blob = new Blob([bytes], {
                type: "application/pdf"
            });

            if (url) URL.revokeObjectURL(url);
            url = URL.createObjectURL(blob);

            const original = file.name.replace(/\.pdf$/i, "");
            $("download").href = url;
            $("download").download = "rotated-" + original + ".pdf";

            $("stats").innerHTML = "<b>Original:</b> " + fmt(file.size) +
                " <span class='mx-1'>→</span> <b>New PDF:</b> " + fmt(blob.size) +
                "<br><small>" + pageNumbers.length + " page(s) rotated by " + selectedAngle + "°</small>";

            prog(100, "Complete!");
            $("result").classList.remove("hidden");
            $("result").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

        } catch (error) {
            console.error(error);
            alert(error.message || "Could not rotate this PDF.");
            $("progress").classList.add("hidden");
        } finally {
            btn.disabled = false;
            btn.textContent = "Rotate PDF →";
        }
    }

    function viewPDF() {
        if (!url) return;
        $("frame").src = url;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    }

    function closeViewer() {
        $("viewer").classList.add("hidden");
        $("frame").src = "";
        document.body.classList.remove("overflow-hidden");
    }

    function resetAll() {
        closeViewer();

        if (url) {
            URL.revokeObjectURL(url);
            url = null;
        }

        file = null;
        $("input").value = "";
        $("box").classList.add("hidden");
        $("result").classList.add("hidden");
        $("paymentBox").classList.add("hidden");
        $("progress").classList.add("hidden");
        $("pages").value = "";
        $("angle").value = "90";

        document.querySelectorAll(".angle-card").forEach(c => c.classList.remove("active"));
        document.querySelector('.angle-card[data-angle="90"]').classList.add("active");

        document.querySelector('input[name="pageMode"][value="all"]').checked = true;
        $("selectedBox").classList.add("hidden");
        changePageMode();
        prog(0, "Processing...");
        $("rotate").disabled = false;
        $("rotate").textContent = "Rotate PDF →";
    }

    updateUsage(0, 4);
    </script>

</body>

</html>