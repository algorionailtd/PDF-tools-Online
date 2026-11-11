<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Extract Pages - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
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

    .page {
        transition: .2s ease
    }

    .page:hover {
        transform: translateY(-2px)
    }

    .page.active {
        border-color: #e5322d;
        background: #fff5f5
    }

    .spinner {
        width: 22px;
        height: 22px;
        border: 3px solid #fee2e2;
        border-top-color: #e5322d;
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
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
            <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0"><span
                    class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span><span
                    class="text-2xl font-black">Tools</span></a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a><a href="{{route('home')}}#tools"
                    class="text-brand">All Tools</a><a href="{{route('home')}}#workflows"
                    class="hover:text-brand">Workflows</a><a href="{{route('home')}}#about"
                    class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 lg:py-20 text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">📄
                PDF Tool 10</span>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">Extract Pages from <span class="text-brand">PDF</span></h1>
            <p class="max-w-2xl mx-auto mt-5 text-slate-500 text-base sm:text-lg leading-7">Select the pages you need  and create a new PDF file instantly.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
        <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold">Free Uploads</p>
                    <p id="usageText" class="text-xs text-slate-500 mt-1">You have 4 free file uploads available across all PDF tools.</p>
                </div>
                <div class="text-sm font-black text-brand"><span id="usedCount">0</span> / 4</div>
            </div>
        </div>

        <div id="drop" class="upload bg-white border-2 border-dashed border-slate-300 rounded-2xl sm:rounded-3xl p-6 sm:p-10 lg:p-12 text-center cursor-pointer hover:border-brand hover:bg-red-50">
            <div class="mx-auto w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-red-50 flex items-center justify-center text-3xl sm:text-4xl">
                📄</div>
            <h2 class="mt-5 text-xl sm:text-2xl font-black">Upload your PDF</h2>
            <p class="mt-2 text-sm sm:text-base text-slate-500">Drag & drop your PDF here or click to browse</p>
            <input id="input" type="file" accept="application/pdf,.pdf" class="hidden">
            <button type="button" onclick="input.click()"
                class="mt-6 w-full sm:w-auto bg-brand hover:bg-brandDark text-white font-bold px-6 py-3 rounded-lg shadow-lg shadow-red-100">Choose
                PDF</button>
            <p class="mt-4 text-xs text-slate-400">PDF • No frontend MB upload limit</p>
        </div>

        <div id="box" class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center text-xl">📄</div>
                    <div class="min-w-0"><b id="name" class="block truncate text-base sm:text-lg"></b>
                        <p id="size" class="text-sm text-slate-500 mt-1"></p>
                    </div>
                </div>
                <button type="button" onclick="resetAll()"
                    class="w-full sm:w-auto bg-red-50 hover:bg-red-100 text-brand px-4 py-2.5 rounded-lg font-semibold">Delete</button>
            </div>

            <div id="passwordPrompt" class="hidden mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <label for="pdfPassword" class="block text-sm font-bold text-slate-800">This PDF is
                    password-protected</label>
                <p class="mt-1 text-sm text-slate-600">Enter the PDF password to select and extract pages.</p>
                <p class="mt-1 text-xs text-slate-500">Protected pages are exported as images, so text may not be
                    selectable.</p>
                <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                    <input id="pdfPassword" type="password" autocomplete="current-password"
                        class="min-w-0 flex-1 rounded-lg border border-slate-300 px-4 py-3" placeholder="PDF password">
                    <button type="button" onclick="unlockPDF()"
                        class="rounded-lg bg-brand px-5 py-3 font-bold text-white hover:bg-brandDark">Unlock
                        PDF</button>
                </div>
            </div>

            <div id="error" class="hidden mt-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-semibold">
            </div>

            <div id="pageSelection" class="hidden">
            <div class="border-t border-slate-100 mt-6 pt-6">
                <h3 class="font-black text-lg">Select Pages</h3>
                <p class="text-sm text-slate-500 mt-1">Choose individual pages or enter a range such as 1-3, 5, 8-10.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                    <input id="range" type="text" placeholder="1-3, 5, 8-10"
                        class="flex-1 border-2 border-slate-200 rounded-lg px-4 py-3 outline-none focus:border-brand">
                    <button type="button" onclick="applyRange()"
                        class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-lg font-bold">Apply</button>
                </div>
                <div class="flex flex-wrap gap-3 mt-4">
                    <button type="button" onclick="selectAll()"
                        class="bg-red-50 hover:bg-red-100 text-brand px-4 py-2 rounded-lg text-sm font-bold">Select
                        All</button>
                    <button type="button" onclick="clearPages()"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-bold">Clear</button>
                    <span id="count" class="px-4 py-2 text-sm font-bold text-slate-500">0 pages selected</span>
                </div>
            </div>

            <div id="pages" class="mt-6 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3"></div>

            <button id="generate" type="button" onclick="generatePDF()" disabled
                class="mt-7 w-full py-4 rounded-xl bg-brand hover:bg-slate-900 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-black text-lg">Extract
                Selected Pages →</button>
            </div>

            <div id="loading" class="hidden mt-6">
                <div class="flex items-center justify-center gap-3">
                    <div class="spinner"></div><span id="status" class="font-bold text-slate-600">Preparing...</span>
                </div>
                <div class="mt-4 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="bar" class="h-full bg-brand transition-all" style="width:0%"></div>
                </div>
            </div>
        </div>

        <x-payment-upgrade-card id="payment" />

        <div id="result"
            class="hidden mt-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm text-center">
            <div class="bg-emerald-50 text-emerald-600 rounded-xl p-4 font-bold">✓ PDF Generated Successfully</div>
            <p id="resultText" class="text-sm sm:text-base text-slate-500 mt-3 leading-6"></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <button type="button" onclick="viewPDF()"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg font-bold">👁 View
                    PDF</button>
                <a id="download"
                    class="w-full bg-brand hover:bg-brandDark text-white py-3 rounded-lg font-bold flex items-center justify-center">⬇
                    Download PDF</a>
            </div>
        </div>
    </main>

    <div id="viewer" class="hidden fixed inset-0 bg-black/90 z-[60] p-2 sm:p-4">
        <div class="bg-white w-full h-full rounded-xl sm:rounded-2xl overflow-hidden flex flex-col">
            <div class="p-3 sm:p-4 border-b flex items-center justify-between gap-4"><b class="text-sm sm:text-base">PDF
                    Preview</b><button type="button" onclick="closeViewer()"
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
        pdf = null,
        pdfJsDoc = null,
        selected = new Set(),
        outputUrl = null,
        totalPages = 0;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (typeof pdfjsLib !== "undefined") {
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    }

    function fmt(b) {
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        if (b < 1073741824) return (b / 1048576).toFixed(2) + " MB";
        return (b / 1073741824).toFixed(2) + " GB"
    }

    function showError(m) {
        $("error").textContent = m;
        $("error").classList.remove("hidden")
    }

    function clearError() {
        $("error").classList.add("hidden")
    }

    async function reserveUpload() {
        try {
            const r = await fetch("{{route('pdf.usage.reserve')}}", {
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
            const d = await r.json();
            if (d.free_uploads_used !== undefined) $("usedCount").textContent = Math.min(Number(d
                .free_uploads_used), 4);
            if (!d.allowed) {
                if (d.requires_payment) {
                    $("payment").classList.remove("hidden");
                    $("usageText").textContent = "Your 4 free file uploads have been used.";
                    $("payment").scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                    return false
                }
                showError(d.message || "Upload is not allowed.");
                return false;
            }
            if (d.premium) {
                $("usedCount").textContent = "4";
                $("usageText").textContent = "Premium access active. Unlimited uploads are allowed."
            } else {
                const used = Number(d.free_uploads_used ?? 0),
                    remaining = Math.max(0, 4 - used);
                $("usedCount").textContent = used + " / 4";
                $("usageText").textContent = remaining + " free file upload" + (remaining === 1 ? "" : "s") +
                    " remaining across all PDF tools."
            }
            return true;
        } catch (e) {
            console.error(e);
            showError("Unable to check upload access.");
            return false
        }
    }

    async function loadPDF(f, password = "") {
        clearError();
        $("loading").classList.remove("hidden");
        $("status").textContent = "Reading PDF...";
        $("bar").style.width = "10%";
        try {
            const bytes = new Uint8Array(await f.arrayBuffer());
            $("bar").style.width = "30%";

            pdf = null;
            pdfJsDoc = null;

            try {
                pdf = await PDFLib.PDFDocument.load(bytes, {
                    updateMetadata: false
                });
                totalPages = pdf.getPageCount();
            } catch (pdfLibError) {
                if (typeof pdfjsLib === "undefined") throw pdfLibError;

                const loadingTask = pdfjsLib.getDocument({
                    data: bytes.slice(),
                    password: password || undefined,
                    isEvalSupported: false,
                    useSystemFonts: true
                });
                pdfJsDoc = await loadingTask.promise;
                totalPages = pdfJsDoc.numPages;
            }

            if (totalPages < 1) throw new Error("No pages found.");
            file = f;
            $("passwordPrompt").classList.add("hidden");
            $("pdfPassword").value = "";
            selected.clear();
            for (let i = 0; i < totalPages; i++) selected.add(i);
            $("bar").style.width = "70%";
            renderPages();
            $("pageSelection").classList.remove("hidden");
            $("bar").style.width = "100%";
            $("status").textContent = totalPages + " pages loaded.";
            setTimeout(() => $("loading").classList.add("hidden"), 400);
        } catch (e) {
            console.error(e);
            pdf = null;
            pdfJsDoc = null;
            totalPages = 0;
            selected.clear();
            $("loading").classList.add("hidden");

            if (e.name === "PasswordException" || /password/i.test(e.message || "")) {
                file = f;
                $("passwordPrompt").classList.remove("hidden");
                $("pdfPassword").focus();
                showError(password ? "That password was not accepted. Try again." :
                    "Enter this PDF's password to continue.");
                return;
            }

            file = null;
            showError("This PDF cannot be processed in the browser. Please try another PDF.")
        }
    }

    function unlockPDF() {
        if (!file) return;
        const password = $("pdfPassword").value;
        if (!password) return showError("Enter the PDF password first.");
        loadPDF(file, password);
    }

    function loadFile(f) {
        if (f.size === 0) {
            $("input").value = "";
            return showError('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !f.name.toLowerCase().endsWith(".pdf")) return showError(
            "Please select a PDF file.");
        if (pdfJsDoc) pdfJsDoc.destroy();
        pdfJsDoc = null;
        pdf = null;
        file = f;
        totalPages = 0;
        selected.clear();
        $("range").value = "";
        $("name").textContent = f.name;
        $("size").textContent = "PDF Document • " + fmt(f.size);
        $("box").classList.remove("hidden");
        $("result").classList.add("hidden");
        $("pageSelection").classList.add("hidden");
        $("pages").innerHTML = "";
        $("passwordPrompt").classList.add("hidden");
        $("pdfPassword").value = "";
        loadPDF(f);
    }

    $("input").onchange = e => {
        if (e.target.files[0]) loadFile(e.target.files[0])
    };
    $("drop").onclick = e => {
        if (!e.target.closest("button")) $("input").click()
    };
    ["dragenter", "dragover"].forEach(t => $("drop").addEventListener(t, e => {
        e.preventDefault();
        e.stopPropagation();
        $("drop").classList.add("drag")
    }));
    ["dragleave", "drop"].forEach(t => $("drop").addEventListener(t, e => {
        e.preventDefault();
        e.stopPropagation();
        $("drop").classList.remove("drag")
    }));
    $("drop").ondrop = e => {
        const f = e.dataTransfer.files[0];
        if (f) loadFile(f)
    };

    function renderPages() {
        $("pages").innerHTML = "";
        for (let i = 0; i < totalPages; i++) {
            const b = document.createElement("button");
            b.type = "button";
            b.className = "page border-2 border-slate-200 rounded-2xl p-4 bg-white";
            if (selected.has(i)) b.classList.add("active");
            b.innerHTML =
                `<div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 flex items-center justify-center font-black">${i+1}</div><div class="mt-3 text-sm font-bold">Page ${i+1}</div><div class="mt-2 text-xs font-bold ${selected.has(i)?"text-brand":"text-slate-400"}">${selected.has(i)?"Selected":"Not selected"}</div>`;
            b.onclick = () => {
                selected.has(i) ? selected.delete(i) : selected.add(i);
                renderPages()
            };
            $("pages").appendChild(b);
        }
        updateCount();
    }

    function updateCount() {
        const n = selected.size;
        $("count").textContent = n + " page" + (n === 1 ? "" : "s") + " selected";
        $("generate").disabled = !file || n === 0
    }

    function selectAll() {
        if (!file) return;
        $("range").value = "";
        selected.clear();
        for (let i = 0; i < totalPages; i++) selected.add(i);
        renderPages()
    }

    function clearPages() {
        $("range").value = "";
        selected.clear();
        renderPages()
    }

    function applyRange() {
        clearError();
        if (!file || totalPages < 1) return showError("Upload and unlock a PDF before selecting pages.");
        const v = $("range").value.trim();
        if (!v) return showError("Enter page numbers first.");
        try {
            selected = parseRange(v);
            renderPages()
        } catch (e) {
            showError(e.message)
        }
    }

    $("range").addEventListener("input", () => {
        if (!file || totalPages < 1) return;
        const value = $("range").value.trim();
        if (!value) {
            selected.clear();
            renderPages();
            return;
        }
        try {
            selected = parseRange(value);
            clearError();
        } catch (e) {
            selected.clear();
        }
        renderPages();
    });

    $("range").addEventListener("keydown", event => {
        if (event.key === "Enter") {
            event.preventDefault();
            applyRange();
        }
    });

    function parseRange(v) {
        const result = new Set(),
            parts = v.split(",").map(x => x.trim()).filter(Boolean);
        for (const part of parts) {
            if (part.includes("-")) {
                const values = part.split("-").map(x => parseInt(x.trim(), 10));
                if (values.length !== 2 || isNaN(values[0]) || isNaN(values[1])) throw new Error("Invalid page range.");
                const start = Math.min(values[0], values[1]),
                    end = Math.max(values[0], values[1]);
                if (start < 1 || end > totalPages) throw new Error("Page must be between 1 and " + totalPages + ".");
                for (let p = start; p <= end; p++) result.add(p - 1);
            } else {
                const page = parseInt(part, 10);
                if (isNaN(page) || page < 1 || page > totalPages) throw new Error("Invalid page number.");
                result.add(page - 1);
            }
        }
        return result;
                $("range").value = "";
    }

    async function generatePDF() {
        clearError();
        if (!file || selected.size === 0) return showError("Please select at least one page.");
        if (!await reserveUpload()) return;
                $("range").value = "";
        $("generate").disabled = true;
        $("loading").classList.remove("hidden");
        $("status").textContent = "Creating PDF...";
        $("bar").style.width = "10%";
        try {
            const output = await PDFLib.PDFDocument.create();
            const indexes = Array.from(selected).sort((a, b) => a - b);

            if (pdf) {
                $("status").textContent = "Copying selected pages...";
                $("bar").style.width = "50%";
                const pages = await output.copyPages(pdf, indexes);
                pages.forEach(p => output.addPage(p));
            } else if (pdfJsDoc) {
                for (let i = 0; i < indexes.length; i++) {
                    $("status").textContent = "Rendering page " + (i + 1) + " of " + indexes.length + "...";
                    const sourcePage = await pdfJsDoc.getPage(indexes[i] + 1);
                    const pageSize = sourcePage.getViewport({
                        scale: 1
                    });
                    const viewport = sourcePage.getViewport({
                        scale: 1.5
                    });
                    const canvas = document.createElement("canvas");
                    canvas.width = Math.ceil(viewport.width);
                    canvas.height = Math.ceil(viewport.height);
                    const context = canvas.getContext("2d", {
                        alpha: false
                    });
                    if (!context) throw new Error("Could not render a PDF page.");

                    await sourcePage.render({
                        canvasContext: context,
                        viewport
                    }).promise;

                    const image = await output.embedJpg(canvas.toDataURL("image/jpeg", 0.92));
                    const page = output.addPage([pageSize.width, pageSize.height]);
                    page.drawImage(image, {
                        x: 0,
                        y: 0,
                        width: pageSize.width,
                        height: pageSize.height
                    });
                    canvas.width = 1;
                    canvas.height = 1;
                    $("bar").style.width = Math.round(((i + 1) / indexes.length) * 60 + 20) + "%";
                }
            } else {
                throw new Error("The PDF is not loaded.");
            }
            $("status").textContent = "Saving PDF...";
            $("bar").style.width = "80%";
            const bytes = await output.save();
            if (!bytes || bytes.length === 0) throw new Error("Empty PDF.");
            if (outputUrl) URL.revokeObjectURL(outputUrl);
            outputUrl = URL.createObjectURL(new Blob([bytes], {
                type: "application/pdf"
            }));
            $("download").href = outputUrl;
            $("download").download = file.name.replace(/\.pdf$/i, "") + "-extracted.pdf";
            $("resultText").textContent = indexes.length + " page" + (indexes.length === 1 ? "" : "s") +
                " extracted • " + fmt(bytes.length) + (pdfJsDoc ? " • pages flattened to images" : "");
            $("bar").style.width = "100%";
            $("status").textContent = "Complete!";
            setTimeout(() => {
                $("loading").classList.add("hidden");
                $("result").classList.remove("hidden");
                $("result").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                })
            }, 400);
        } catch (e) {
            console.error(e);
            $("loading").classList.add("hidden");
            showError("PDF could not be generated. Please try another PDF.")
        } finally {
            $("generate").disabled = false
        }
    }

    function viewPDF() {
        if (!outputUrl) return alert("Please generate the PDF first.");
        $("frame").src = outputUrl;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden")
    }

    function closeViewer() {
        $("viewer").classList.add("hidden");
        $("frame").src = "";
        document.body.classList.remove("overflow-hidden")
    }

    function resetAll() {
        closeViewer();
        if (outputUrl) {
            URL.revokeObjectURL(outputUrl);
            outputUrl = null
        }
        file = null;
        pdf = null;
        if (pdfJsDoc) pdfJsDoc.destroy();
        pdfJsDoc = null;
        selected.clear();
        totalPages = 0;
        $("input").value = "";
        $("box").classList.add("hidden");
        $("passwordPrompt").classList.add("hidden");
        $("pdfPassword").value = "";
        $("result").classList.add("hidden");
        $("payment").classList.add("hidden");
        $("pages").innerHTML = "";
        $("range").value = "";
        $("count").textContent = "0 pages selected";
        $("generate").disabled = true;
        $("loading").classList.add("hidden");
        $("bar").style.width = "0%";
        clearError();
    }

    window.addEventListener("beforeunload", () => {
        if (outputUrl) URL.revokeObjectURL(outputUrl)
    });
    </script>
</body>

</html>